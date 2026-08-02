<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Payment\Gateway\Api\Stripe as StripeGateway;

class Stripe extends StripeGateway
{
    use Api; // Import the Api trait

    public const JS_LIBRARY_URL = 'https://checkout.stripe.com/checkout.js';

    /**
     * @param string $sPrice Normal price format (e.g., 19.95).
     *
     * @return int returns amount in cents (without points) to be validated for Stripe
     */
    public static function getAmount($sPrice)
    {
        return (string)PaymentCheckout::getMinorUnits($sPrice);
    }
}
