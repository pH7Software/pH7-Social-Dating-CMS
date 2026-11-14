<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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

    public function testRemovedBraintreeGatewayCannotBecomeAvailable(): void
    {
        $this->assertFalse(PaymentGatewayConfig::isReady('braintree', $this->configuredSettings()));
        $this->assertArrayNotHasKey(
            'braintree',
            PaymentGatewayConfig::getAvailability($this->configuredSettings())
        );
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
                '2co' => false
            ],
            PaymentGatewayConfig::getAvailability($this->configuredSettings())
        );
    }

    public function testShippedLegacyPlaceholdersNeverEnableCheckout(): void
    {
        $aSettings = [
            'paypal.enabled' => 1,
            'paypal.email' => 'your_paypal_email_address@domain.com',
            'stripe.enabled' => 1,
            'stripe.publishable_key' => 'pk_test_6pRNASCoBOKtIshFeQd4XMUh',
            'stripe.secret_key' => 'sk_test_d8e8fca2dc0f896fd7cb4cb0031ba249',
            '2co.enabled' => 1,
            '2co.vendor_id' => 'APIuser1817037',
            '2co.secret_word' => '0L8GUb@yM|Be2H#uW3E]'
        ];

        $this->assertSame(
            ['paypal' => false, 'stripe' => false, '2co' => false],
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
            '2co.enabled' => 1,
            '2co.vendor_id' => 'vendor',
            '2co.secret_word' => 'secret'
        ];
    }
}
