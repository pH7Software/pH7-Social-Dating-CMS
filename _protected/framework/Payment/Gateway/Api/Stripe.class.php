<?php
/**
 * @title            Stripe Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;

class Stripe extends Provider implements Api
{
    /**
     * Get the Form Action URL.
     *
     * @param string $sParam
     *
     * @return string
     *
     * @internal We add an empty $sParam param for the method to be compatible with the API interface.
     */
    public function getUrl($sParam = '')
    {
        return Uri::get(
            'payment',
            'main',
            'process',
            'stripe'
        );
    }

    public function getMsg()
    {
        // Useless for Stripe. Need it only to be compatible with its API interface.
    }

    /**
     * {@inheritDoc}
     */
    public function valid($sParam1 = '', $sParam2 = '')
    {
        // Useless for Stripe. Need only in order to be compatible with its API interface.
    }
}
