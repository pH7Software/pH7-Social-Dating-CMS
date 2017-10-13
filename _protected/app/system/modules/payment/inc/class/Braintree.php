<?php
/**
 * @title          Braintree
 *
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2017, Pierre-Henry Soria. All Rights Reserved.
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

    public static function init(Config $oConfig)
    {
        $sEnvironment = ((bool)$oConfig->values['module.setting']['sandbox.enabled']) ? 'sandbox' : 'production';
        Braintree_Configuration::environment($sEnvironment);

        Braintree_Configuration::merchantId($oConfig->values['module.setting']['braintree.merchant_id']);
        Braintree_Configuration::publicKey($oConfig->values['module.setting']['braintree.public_key']);
        Braintree_Configuration::privateKey($oConfig->values['module.setting']['braintree.private_ke']);
    }
}