<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvider.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvidable.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/ClickatellProvider.php';

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PH7\ClickatellProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class ClickatellProviderTest extends TestCase
{
    public function testSendUsesCurrentApiContract(): void
    {
        $aHistory = [];
        $oHandler = HandlerStack::create(
            new MockHandler([
                new Response(
                    202,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        'messages' => [
                            [
                                'accepted' => true,
                                'error' => null
                            ]
                        ]
                    ], JSON_THROW_ON_ERROR)
                )
            ])
        );
        $oHandler->push(Middleware::history($aHistory));
        $oProvider = new ClickatellProvider(
            '+61400000000',
            'api-token',
            new Client(['handler' => $oHandler])
        );

        $this->assertTrue($oProvider->send('+64210000000', 'Your verification code is 123456.'));
        $this->assertCount(1, $aHistory);

        /** @var RequestInterface $oRequest */
        $oRequest = $aHistory[0]['request'];
        $this->assertSame('POST', $oRequest->getMethod());
        $this->assertSame('https://platform.clickatell.com/messages', (string) $oRequest->getUri());
        $this->assertSame('api-token', $oRequest->getHeaderLine('Authorization'));
        $this->assertSame('application/json', $oRequest->getHeaderLine('Accept'));
        $this->assertSame(
            [
                'content' => 'Your verification code is 123456.',
                'to' => ['+64210000000'],
                'from' => '+61400000000'
            ],
            json_decode((string) $oRequest->getBody(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    #[DataProvider('invalidResponseProvider')]
    public function testSendRejectsInvalidProviderResponses(int $iStatusCode, string $sResponseBody): void
    {
        $oProvider = new ClickatellProvider(
            '+61400000000',
            'api-token',
            new Client([
                'handler' => new MockHandler([
                    new Response($iStatusCode, ['Content-Type' => 'application/json'], $sResponseBody)
                ])
            ])
        );

        $this->assertFalse($oProvider->send('+64210000000', 'Your verification code is 123456.'));
    }

    public static function invalidResponseProvider(): array
    {
        return [
            'unauthorized request' => [401, '{"error":"Unauthorized"}'],
            'malformed JSON' => [202, '{invalid-json'],
            'missing messages' => [202, '{}'],
            'missing error status' => [202, '{"messages":[{"accepted":true,"id":"abc123"}]}'],
            'rejected message' => [
                202,
                '{"messages":[{"accepted":false,"error":"Invalid destination address."}]}'
            ]
        ];
    }
}
