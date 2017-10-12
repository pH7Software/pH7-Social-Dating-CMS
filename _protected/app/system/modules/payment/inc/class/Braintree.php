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

use PH7\Framework\Payment\Gateway\Api\Braintree as BraintreeGateway;

class Braintree extends BraintreeGateway
{
    use Api; // Import the Api trait
}