<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 */

namespace PH7;

use Braintree_Configuration;
use PH7\Framework\Config\Config;
use PH7\Framework\Payment\Gateway\Api\Braintree as BraintreeGateway;

class Braintree extends BraintreeGateway
{
    use Api; // Import the Api trait

    const JS_LIBRARY_URL = 'https://js.braintreegateway.com/v2/braintree.js';
    const SANDBOX_MERCHANT_ID = 'cbqd3ncztsszwbrh';

    public static function init(Config $oConfig)
    {
        $sEnvironment = 'production';

        if (self::isSandboxEnabled($oConfig)) {
            $sEnvironment = 'sandbox';
        }

        Braintree_Configuration::environment($sEnvironment);

        Braintree_Configuration::merchantId($oConfig->values['module.setting']['braintree.merchant_id']);
        Braintree_Configuration::publicKey($oConfig->values['module.setting']['braintree.public_key']);
        Braintree_Configuration::privateKey($oConfig->values['module.setting']['braintree.private_ke']);
    }

    private static function isSandboxEnabled(Config $oConfig)
    {
        return (bool)$oConfig->values['module.setting']['sandbox.enabled'] ||
            $oConfig->values['module.setting']['braintree.merchant_id'] === static::SANDBOX_MERCHANT_ID;
    }
}
