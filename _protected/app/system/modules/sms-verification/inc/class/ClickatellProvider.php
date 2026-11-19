<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PH7\Framework\Error\Logger;

class ClickatellProvider extends SmsProvider implements SmsProvidable
{
    private const API_ENDPOINT = 'https://platform.clickatell.com/v1/message';

    private ClientInterface $oHttpClient;

    public function __construct(
        string $sSenderNumber,
        string $sApiToken,
        ?ClientInterface $oHttpClient = null
    ) {
        parent::__construct($sSenderNumber, $sApiToken);

        $this->oHttpClient = $oHttpClient ?? new Client([
            'connect_timeout' => 5,
            'timeout' => 10
        ]);
    }

    public function send(string $sPhoneNumber, string $sTextMessage): bool
    {
        $aMessage = [
            'channel' => 'sms',
            'to' => self::normalizePhoneNumber($sPhoneNumber),
            'content' => $sTextMessage
        ];
        if ($this->sSenderNumber !== '') {
            $aMessage['from'] = self::normalizePhoneNumber($this->sSenderNumber);
        }

        try {
            $oResponse = $this->oHttpClient->request(
                'POST',
                self::API_ENDPOINT,
                [
                    'http_errors' => false,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => $this->sApiToken
                    ],
                    'json' => [
                        'messages' => [$aMessage]
                    ]
                ]
            );

            if ($oResponse->getStatusCode() !== 202) {
                return false;
            }

            $aResponse = json_decode(
                (string) $oResponse->getBody(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (!is_array($aResponse)) {
                return false;
            }

            return $this->isSuccessResponse(
                is_array($aResponse['messages'] ?? null)
                    ? $aResponse['messages']
                    : []
            );
        } catch (GuzzleException|\JsonException $oExcept) {
            if (defined('PH7_PATH_LOG')) {
                (new Logger())->msg('Clickatell error while sending SMS: ' . $oExcept->getMessage());
            }

            return false;
        }
    }

    private function isSuccessResponse(array $aResponse): bool
    {
        $aMessage = array_pop($aResponse);
        if (!is_array($aMessage) || !array_key_exists('error', $aMessage)) {
            return false;
        }

        return ($aMessage['accepted'] ?? false) === true && $aMessage['error'] === null;
    }

    private static function normalizePhoneNumber(string $sPhoneNumber): string
    {
        return str_starts_with($sPhoneNumber, '+') ? substr($sPhoneNumber, 1) : $sPhoneNumber;
    }
}
