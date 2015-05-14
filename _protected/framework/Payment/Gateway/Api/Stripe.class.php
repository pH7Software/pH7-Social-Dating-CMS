<?php
/**
 * @title            Stripe Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;
defined('PH7') or exit('Restricted access');

class Stripe
{

    public function __construct()
    {
        parent::__construct();

        Import::lib('Service.Stripe.init'); // Import the Stripe library
    }

}
