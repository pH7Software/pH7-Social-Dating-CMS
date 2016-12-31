<?php
/**
 * @title          PayPal
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 * @version        0.2
 */
namespace PH7;

class PayPal extends Framework\Payment\Gateway\Api\PayPal
{

    use Api; // Import the Api trait

    public function __construct($bSandbox)
    {
        parent::__construct($bSandbox);
    }

}
