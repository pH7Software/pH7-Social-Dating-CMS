<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 */

namespace PH7;

use PH7\Framework\Payment\Gateway\Api\Stripe as StripeGateway;

class Stripe extends StripeGateway
{
    use Api; // Import the Api trait

    const JS_LIBRARY_URL = 'https://checkout.stripe.com/checkout.js';

    /**
     * @param string $sPrice Normal price format (e.g., 19.95).
     *
     * @return int Returns amount in cents (without points) to be validated for Stripe.
     */
    public static function getAmount($sPrice)
    {
        return str_replace('.', '', $sPrice);
    }
}
