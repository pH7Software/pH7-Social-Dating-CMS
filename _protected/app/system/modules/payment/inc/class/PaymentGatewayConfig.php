<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

final class PaymentGatewayConfig
{
    private const UNAVAILABLE_PROVIDERS = [
        'stripe',
        '2co'
    ];

    private const ENABLE_SETTINGS = [
        'paypal' => 'paypal.enabled',
        'stripe' => 'stripe.enabled',
        'braintree' => 'braintree.enabled',
        '2co' => '2co.enabled'
    ];

    private const REQUIRED_SETTINGS = [
        'paypal' => ['paypal.email'],
        'stripe' => ['stripe.publishable_key', 'stripe.secret_key'],
        'braintree' => ['braintree.merchant_id', 'braintree.public_key'],
        '2co' => ['2co.vendor_id', '2co.secret_word']
    ];

    private const SHIPPED_PLACEHOLDER_VALUES = [
        'your_paypal_email_address@domain.com',
        'pk_test_6pRNASCoBOKtIshFeQd4XMUh',
        'sk_test_d8e8fca2dc0f896fd7cb4cb0031ba249',
        'cbqd3ncztsszwbrh',
        '7mr28wc74trf2xp4',
        '3a21c73d4c362d0bf38080e9d87a5854',
        'APIuser1817037',
        '0L8GUb@yM|Be2H#uW3E]'
    ];

    private function __construct()
    {
    }

    /**
     * @return array<string, bool>
     */
    public static function getAvailability(array $aSettings): array
    {
        $aAvailability = [];
        foreach (array_keys(self::ENABLE_SETTINGS) as $sProvider) {
            $aAvailability[$sProvider] = self::isReady($sProvider, $aSettings);
        }

        return $aAvailability;
    }

    public static function isReady(string $sProvider, array $aSettings): bool
    {
        // These bundled legacy flows require migration before they can be exposed safely.
        if (in_array($sProvider, self::UNAVAILABLE_PROVIDERS, true)) {
            return false;
        }

        $sEnableSetting = self::ENABLE_SETTINGS[$sProvider] ?? null;
        if ($sEnableSetting === null || (int)($aSettings[$sEnableSetting] ?? 0) !== 1) {
            return false;
        }

        foreach (self::REQUIRED_SETTINGS[$sProvider] as $sSetting) {
            if (!self::hasUsableValue($aSettings[$sSetting] ?? null)) {
                return false;
            }
        }

        if ($sProvider === 'paypal' && filter_var($aSettings['paypal.email'], FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        if ($sProvider === 'braintree') {
            $mPrivateKey = $aSettings['braintree.private_key'] ?? $aSettings['braintree.private_ke'] ?? null;

            return self::hasUsableValue($mPrivateKey);
        }

        return true;
    }

    private static function hasUsableValue(mixed $mValue): bool
    {
        if (!is_scalar($mValue) || is_bool($mValue)) {
            return false;
        }

        $sValue = trim((string)$mValue);

        return $sValue !== '' && !in_array($sValue, self::SHIPPED_PLACEHOLDER_VALUES, true);
    }
}
