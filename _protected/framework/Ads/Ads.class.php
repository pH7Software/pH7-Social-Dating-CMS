<?php
/**
 * @title            Advertisement Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ads
 * @version          1.0
 */

namespace PH7\Framework\Ads;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc;

class Ads
{

    const PARAM_URL = 'ads_url';

    /**
     * Output Advertisement.
     *
     * @static
     * @param object $oData Db query.
     * @return string
     */
    public static function output($oData)
    {
        $sLink = ''; // Default value

        if (preg_match('#href="(https?://(www\.)?([a-z0-9-_]+)\.[a-z]{2,4}/?([^"]+)?)"#i', $oData->code, $aMatch))
        {
            $sLink = $aMatch[1];
            $sCode = str_replace($sLink, PH7_URL_ROOT . '?' . static::PARAM_URL . '=' . base64_encode($sLink), $oData->code);
        }
        else
        {
            $sCode = $oData->code;
        }

        // Stat Advertisement Shows
        \PH7\Framework\Analytics\Statistic::setView($oData->adsId, 'Ads');

        // Advertisement Clicks
        $oHttpRequest = new Mvc\Request\HttpRequest;
        if ($oHttpRequest->getExists(static::PARAM_URL))
        {
            $sUrl = base64_decode($oHttpRequest->get(static::PARAM_URL));

            // Check link
            if ($sUrl === $sLink)
            {
                Mvc\Model\Ads::setClick($oData->adsId, $sLink);
            }

            // Redirect to the site to advertise
            \PH7\Framework\Url\HeaderUrl::redirect($sUrl);
        }
        unset($oHttpRequest);

        return (new \PH7\Framework\Parse\SysVar)->parse($sCode);
    }

}
