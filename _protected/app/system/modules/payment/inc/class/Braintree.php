<?php

/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Payment\Gateway\Api\Braintree as BraintreeGateway;

class Braintree extends BraintreeGateway
{
    use Api; // Import the Api trait

    public const JS_LIBRARY_URL = 'https://js.braintreegateway.com/web/dropin/1.44.1/js/dropin.min.js';
    public const SANDBOX_MERCHANT_ID = 'cbqd3ncztsszwbrh';

    public static function init(Config $oConfig)
    {
        $sEnvironment = 'production';

        if (self::isSandboxEnabled($oConfig)) {
            $sEnvironment = 'sandbox';
        }

        $sConfigurationClass = self::getConfigurationClass();
        $sConfigurationClass::environment($sEnvironment);

        $sConfigurationClass::merchantId($oConfig->values['module.setting']['braintree.merchant_id']);
        $sConfigurationClass::publicKey($oConfig->values['module.setting']['braintree.public_key']);
        $sPrivateKey = $oConfig->values['module.setting']['braintree.private_key'] ??
            $oConfig->values['module.setting']['braintree.private_ke'] ?? '';
        $sConfigurationClass::privateKey($sPrivateKey);
    }

    public static function generateClientToken(): string
    {
        $sClientTokenClass = class_exists(\Braintree\ClientToken::class)
            ? \Braintree\ClientToken::class
            : 'Braintree_ClientToken';

        return (string)$sClientTokenClass::generate();
    }

    public static function sale(array $aPayload)
    {
        $sTransactionClass = class_exists(\Braintree\Transaction::class)
            ? \Braintree\Transaction::class
            : 'Braintree_Transaction';

        return $sTransactionClass::sale($aPayload);
    }

    private static function isSandboxEnabled(Config $oConfig)
    {
        return (bool)$oConfig->values['module.setting']['sandbox.enabled']
            || $oConfig->values['module.setting']['braintree.merchant_id'] === static::SANDBOX_MERCHANT_ID;
    }

    private static function getConfigurationClass(): string
    {
        return class_exists(\Braintree\Configuration::class)
            ? \Braintree\Configuration::class
            : 'Braintree_Configuration';
    }
}
