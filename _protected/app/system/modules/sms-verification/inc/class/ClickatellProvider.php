<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use Clickatell\ClickatellException;
use Clickatell\Rest as Client;
use PH7\Framework\Error\Logger;

class ClickatellProvider extends SmsProvider implements SmsProvidable
{
    public function send(string $sPhoneNumber, string $sTextMessage): bool
    {
        $oClickatell = new Client($this->sApiToken);

        try {
            $aResponse = $oClickatell->sendMessage(
                [
                    'to' => [$sPhoneNumber],
                    'content' => $sTextMessage,
                    'from' => $this->sSenderNumber
                ]
            );

            if (!is_array($aResponse)) {
                return false;
            }

            return $this->isSuccessResponse($aResponse);
        } catch (ClickatellException $oExcept) {
            (new Logger())->msg('Clickatell error while sending SMS: ' . $oExcept->getMessage());
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
}
