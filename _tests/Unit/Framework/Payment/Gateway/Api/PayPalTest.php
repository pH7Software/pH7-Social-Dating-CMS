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

    public function testVerifiedPendingPaymentIsRejected(): void
    {
        $_POST = ['payment_status' => 'Pending'];

        $this->assertFalse($this->createVerifiedGateway()->valid());
    }

    public function testRawPostParserPreservesEqualsCharactersInValues(): void
    {
        $oGateway = new class(false) extends PayPal {
            public function parse(string $sRawPost): array
            {
                return $this->parsePostData($sRawPost);
            }
        };

        $this->assertSame(
            ['custom' => 'membership=token', 'payment_status' => 'Completed'],
            $oGateway->parse('custom=membership%3Dtoken&payment_status=Completed')
        );
    }

    private function createVerifiedGateway(): PayPal
    {
        return new class(false) extends PayPal {
            protected function getStatus()
            {
                return 'VERIFIED';
            }

            protected function getPostData()
            {
                return [];
            }
        };
    }
}
