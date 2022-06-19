<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class / Design
 */

namespace PH7;

class AffiliateDesign
{
    /**
     * @return string
     */
    public static function getPayPalIcon()
    {
        $sHtml = '<a href="https://www.paypal.com" rel="noopener" target="_blank">';
        $sHtml .= '<img src="' . PH7_URL_STATIC . PH7_IMG . 'icon/paypal-small.svg" alt="PayPal" title="PayPal">';
        $sHtml .= '</a><br />';

        return $sHtml;
    }
}
