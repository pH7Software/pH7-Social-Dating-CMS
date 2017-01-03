<?php
/**
 * @title          Stripe
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 * @version        1.0
 */
namespace PH7;

class Stripe extends Framework\Payment\Gateway\Api\Stripe
{
    use Api; // Import the Api trait

    /**
     * @param string $sPrice Normal price format (e.g., 19.95).
     * @return integer Returns amount in cents (without points) to be validated for Stripe.
     */
    public static function getAmount($sPrice)
    {
        return str_replace('.', '', $sPrice);
    }
}