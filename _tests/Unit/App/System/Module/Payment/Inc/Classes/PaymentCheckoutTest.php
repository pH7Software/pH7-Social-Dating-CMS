<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Payment / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'payment/inc/class/PaymentCheckout.php';

use InvalidArgumentException;
use PH7\PaymentCheckout;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class PaymentCheckoutTest extends TestCase
{
    private const MEMBERSHIP_ID = 6;
    private const TOKEN = '0123456789abcdef0123456789abcdef01234567';

    public function testReferenceRoundTrip(): void
    {
        $sReference = PaymentCheckout::createReference(self::MEMBERSHIP_ID, self::TOKEN);

        $this->assertSame(
            [
                'membership_id' => self::MEMBERSHIP_ID,
                'token' => self::TOKEN
            ],
            PaymentCheckout::parseReference($sReference)
        );
    }

    public function testCheckoutSessionNamesAreScopedToMembership(): void
    {
        $this->assertSame('payment_checkout_6', PaymentCheckout::getTokenName(self::MEMBERSHIP_ID));
        $this->assertSame('payment_checkout_context_6', PaymentCheckout::getContextName(self::MEMBERSHIP_ID));
    }

    #[DataProvider('invalidReferenceProvider')]
    public function testInvalidReferenceIsRejected(string $sReference): void
    {
        $this->assertNull(PaymentCheckout::parseReference($sReference));
    }

    public static function invalidReferenceProvider(): array
    {
        return [
            'empty' => [''],
            'not base64url' => ['%%%'],
            'missing token' => [rtrim(strtr(base64_encode('6|'), '+/', '-_'), '=')],
            'zero membership' => [rtrim(strtr(base64_encode('0|' . self::TOKEN), '+/', '-_'), '=')],
            'overflowing membership' => [rtrim(strtr(base64_encode(str_repeat('9', 40) . '|' . self::TOKEN), '+/', '-_'), '=')],
            'invalid token' => [rtrim(strtr(base64_encode('6|short'), '+/', '-_'), '=')]
        ];
    }

    public function testTotalIncludesPercentageVatAndRoundsToMinorUnits(): void
    {
        $this->assertSame('23.99', PaymentCheckout::getTotalAmount('19.99', '20'));
        $this->assertSame('19.99', PaymentCheckout::getTotalAmount('19.99', '0'));
    }

    public function testAmountComparisonUsesNormalizedMinorUnits(): void
    {
        $this->assertTrue(PaymentCheckout::isExpectedAmount('19.99', '20', '23.99'));
        $this->assertTrue(PaymentCheckout::isExpectedAmount('19.90', '0', '19.9'));
        $this->assertFalse(PaymentCheckout::isExpectedAmount('19.99', '20', '19.99'));
        $this->assertFalse(PaymentCheckout::isExpectedAmount('19.99', '20', 'invalid'));
    }

    public function testMinorUnitsPreserveSingleDecimalPlaces(): void
    {
        $this->assertSame(1990, PaymentCheckout::getMinorUnits('19.9'));
    }

    public function testInvalidAmountIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PaymentCheckout::getTotalAmount('-1.00', '20');
    }

    public function testOnlyEnabledPaidMembershipIsPurchasable(): void
    {
        $oMembership = new stdClass;
        $oMembership->enable = '1';
        $oMembership->price = '9.99';

        $this->assertTrue(PaymentCheckout::isPurchasableMembership($oMembership));

        $oMembership->enable = '0';
        $this->assertFalse(PaymentCheckout::isPurchasableMembership($oMembership));

        $oMembership->enable = '1';
        $oMembership->price = '0.00';
        $this->assertFalse(PaymentCheckout::isPurchasableMembership($oMembership));

        $this->assertFalse(PaymentCheckout::isPurchasableMembership(false));
        $this->assertFalse(PaymentCheckout::isPurchasableMembership([]));
    }

    public function testValidPayPalPaymentMatchesTrustedCheckoutDetails(): void
    {
        $oMembership = $this->createMembership();

        $this->assertTrue(
            PaymentCheckout::isValidPayPalPayment(
                $this->createPayPalPayment(),
                $oMembership,
                'merchant@example.com',
                'USD',
                '20'
            )
        );
    }

    #[DataProvider('invalidPayPalPaymentProvider')]
    public function testInvalidPayPalPaymentIsRejected(string $sField, string $sValue): void
    {
        $aPayment = $this->createPayPalPayment();
        $aPayment[$sField] = $sValue;

        $this->assertFalse(
            PaymentCheckout::isValidPayPalPayment(
                $aPayment,
                $this->createMembership(),
                'merchant@example.com',
                'USD',
                '20'
            )
        );
    }

    public static function invalidPayPalPaymentProvider(): array
    {
        return [
            'pending' => ['payment_status', 'Pending'],
            'missing transaction' => ['txn_id', ''],
            'wrong merchant' => ['receiver_email', 'attacker@example.com'],
            'wrong currency' => ['mc_currency', 'EUR'],
            'underpayment' => ['mc_gross', '9.99'],
            'wrong membership' => ['item_number', '5'],
            'non-numeric membership' => ['item_number', '6foo']
        ];
    }

    public function testPayPalPaymentWithMissingFieldIsRejected(): void
    {
        $aPayment = $this->createPayPalPayment();
        unset($aPayment['txn_id']);

        $this->assertFalse(
            PaymentCheckout::isValidPayPalPayment(
                $aPayment,
                $this->createMembership(),
                'merchant@example.com',
                'USD',
                '20'
            )
        );
    }

    private function createMembership(): stdClass
    {
        $oMembership = new stdClass;
        $oMembership->groupId = self::MEMBERSHIP_ID;
        $oMembership->enable = '1';
        $oMembership->price = '19.99';

        return $oMembership;
    }

    private function createPayPalPayment(): array
    {
        return [
            'payment_status' => 'Completed',
            'txn_id' => 'PAYPAL-TRANSACTION-1',
            'receiver_email' => 'merchant@example.com',
            'mc_currency' => 'USD',
            'mc_gross' => '23.99',
            'item_number' => (string)self::MEMBERSHIP_ID
        ];
    }
}
