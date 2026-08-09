<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / App / System / Module / Payment / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'payment/inc/class/PaymentGatewayConfig.php';

use PH7\PaymentGatewayConfig;
use PHPUnit\Framework\TestCase;

final class PaymentGatewayConfigTest extends TestCase
{
    public function testStripeIsUnavailableEvenWhenAnUpgradeKeptItEnabled(): void
    {
        $aSettings = $this->configuredSettings();

        $this->assertFalse(PaymentGatewayConfig::isReady('stripe', $aSettings));
    }

    public function testTwoCheckoutIsUnavailableEvenWhenAnUpgradeKeptItEnabled(): void
    {
        $this->assertFalse(PaymentGatewayConfig::isReady('2co', $this->configuredSettings()));
    }

    public function testEnabledGatewayWithoutEveryCredentialIsUnavailable(): void
    {
        $aSettings = $this->configuredSettings();
        $aSettings['braintree.private_key'] = '';

        $this->assertFalse(PaymentGatewayConfig::isReady('braintree', $aSettings));
    }

    public function testInvalidPayPalMerchantEmailIsUnavailable(): void
    {
        $aSettings = $this->configuredSettings();
        $aSettings['paypal.email'] = 'not-an-email';

        $this->assertFalse(PaymentGatewayConfig::isReady('paypal', $aSettings));
    }

    public function testConfiguredGatewaysAreAvailable(): void
    {
        $this->assertSame(
            [
                'paypal' => true,
                'stripe' => false,
                'braintree' => true,
                '2co' => false
            ],
            PaymentGatewayConfig::getAvailability($this->configuredSettings())
        );
    }

    public function testLegacyBraintreePrivateKeyRemainsSupported(): void
    {
        $aSettings = $this->configuredSettings();
        $aSettings['braintree.private_ke'] = $aSettings['braintree.private_key'];
        unset($aSettings['braintree.private_key']);

        $this->assertTrue(PaymentGatewayConfig::isReady('braintree', $aSettings));
    }

    public function testShippedLegacyPlaceholdersNeverEnableCheckout(): void
    {
        $aSettings = [
            'paypal.enabled' => 1,
            'paypal.email' => 'your_paypal_email_address@domain.com',
            'stripe.enabled' => 1,
            'stripe.publishable_key' => 'pk_test_6pRNASCoBOKtIshFeQd4XMUh',
            'stripe.secret_key' => 'sk_test_d8e8fca2dc0f896fd7cb4cb0031ba249',
            'braintree.enabled' => 1,
            'braintree.merchant_id' => 'cbqd3ncztsszwbrh',
            'braintree.public_key' => '7mr28wc74trf2xp4',
            'braintree.private_key' => '3a21c73d4c362d0bf38080e9d87a5854',
            '2co.enabled' => 1,
            '2co.vendor_id' => 'APIuser1817037',
            '2co.secret_word' => '0L8GUb@yM|Be2H#uW3E]'
        ];

        $this->assertSame(
            ['paypal' => false, 'stripe' => false, 'braintree' => false, '2co' => false],
            PaymentGatewayConfig::getAvailability($aSettings)
        );
    }

    private function configuredSettings(): array
    {
        return [
            'paypal.enabled' => 1,
            'paypal.email' => 'merchant@example.com',
            'stripe.enabled' => 1,
            'stripe.publishable_key' => 'pk_test_example',
            'stripe.secret_key' => 'sk_test_example',
            'braintree.enabled' => 1,
            'braintree.merchant_id' => 'merchant',
            'braintree.public_key' => 'public',
            'braintree.private_key' => 'private',
            '2co.enabled' => 1,
            '2co.vendor_id' => 'vendor',
            '2co.secret_word' => 'secret'
        ];
    }
}
