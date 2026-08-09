<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Payment / Gateway / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Payment\Gateway\Api;

use PH7\Framework\Payment\Gateway\Api\PayPal;
use PHPUnit\Framework\TestCase;

final class PayPalTest extends TestCase
{
    private array $aPostBackup = [];

    protected function setUp(): void
    {
        $this->aPostBackup = $_POST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->aPostBackup;
    }

    public function testVerifiedCompletedPaymentIsAccepted(): void
    {
        $_POST = ['payment_status' => 'Completed'];

        $this->assertTrue($this->createVerifiedGateway()->valid());
    }

    public function testCheckoutAndVerificationUseTheirDedicatedPayPalHosts(): void
    {
        $oLiveGateway = $this->createGatewayWithStatus('VERIFIED', false);
        $oSandboxGateway = $this->createGatewayWithStatus('VERIFIED', true);

        $this->assertSame('https://www.paypal.com/cgi-bin/webscr', $oLiveGateway->getUrl());
        $this->assertSame('https://ipnpb.paypal.com/cgi-bin/webscr', $oLiveGateway->verificationUrl());
        $this->assertSame('https://www.sandbox.paypal.com/cgi-bin/webscr', $oSandboxGateway->getUrl());
        $this->assertSame(
            'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr',
            $oSandboxGateway->verificationUrl()
        );
    }

    public function testVerifiedPendingPaymentIsRejected(): void
    {
        $_POST = ['payment_status' => 'Pending'];

        $this->assertFalse($this->createVerifiedGateway()->valid());
    }

    public function testVerificationRequestPreservesTheRawNotificationExactly(): void
    {
        $oGateway = new class(false) extends PayPal {
            public function build(string $sRawPost): string
            {
                return $this->buildVerificationRequest($sRawPost);
            }
        };

        $sRawPost = 'payer_name=Pierre%20Henry&custom=a%2Bb%3D1';

        $this->assertSame('cmd=_notify-validate&' . $sRawPost, $oGateway->build($sRawPost));
    }

    public function testNonSuccessfulVerificationResponsesRemainRetryable(): void
    {
        $oGateway = new class(false) extends PayPal {
            public function transportFailed(mixed $mResponse, int $iCurlError, int $iHttpStatus): bool
            {
                return $this->didTransportFail($mResponse, $iCurlError, $iHttpStatus);
            }
        };

        $this->assertFalse($oGateway->transportFailed('VERIFIED', CURLE_OK, 200));
        $this->assertTrue($oGateway->transportFailed('rate limited', CURLE_OK, 429));
        $this->assertTrue($oGateway->transportFailed(false, CURLE_COULDNT_CONNECT, 0));
    }

    public function testUnexpectedVerificationBodyRemainsRetryable(): void
    {
        $_POST = ['payment_status' => 'Completed'];
        $oGateway = $this->createGatewayWithStatus('<html>temporary proxy response</html>');

        $this->assertFalse($oGateway->valid());
        $this->assertTrue($oGateway->hasTransportError());
    }

    public function testExactInvalidVerificationIsNotRetried(): void
    {
        $_POST = ['payment_status' => 'Completed'];
        $oGateway = $this->createGatewayWithStatus('INVALID');

        $this->assertFalse($oGateway->valid());
        $this->assertFalse($oGateway->hasTransportError());
    }

    public function testVerifiedBodyOnFailedTransportIsNeverAccepted(): void
    {
        $_POST = ['payment_status' => 'Completed'];
        $oGateway = $this->createGatewayWithStatus('VERIFIED', false, true);

        $this->assertFalse($oGateway->valid());
        $this->assertTrue($oGateway->hasTransportError());
    }

    private function createVerifiedGateway(): PayPal
    {
        return $this->createGatewayWithStatus('VERIFIED');
    }

    private function createGatewayWithStatus(
        string $sStatus,
        bool $bSandbox = false,
        bool $bSimulateTransportError = false
    ): PayPal {
        return new class($bSandbox, $sStatus, $bSimulateTransportError) extends PayPal {
            public function __construct(
                bool $bSandbox,
                private readonly string $sStatus,
                private readonly bool $bSimulateTransportError
            ) {
                parent::__construct($bSandbox);
            }

            protected function getStatus()
            {
                if ($this->bSimulateTransportError) {
                    $this->bTransportError = true;
                }

                return $this->sStatus;
            }

            public function verificationUrl(): string
            {
                return $this->getVerificationUrl();
            }
        };
    }
}
