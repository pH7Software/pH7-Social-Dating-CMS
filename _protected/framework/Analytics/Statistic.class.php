<?php
/**
 * @title            Statistic Class
 * @desc             View Statistics methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Analytics
 * @version          1.1
 */

namespace PH7\Framework\Analytics;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc;

class Statistic
{

    /**
     * Private constructor to prevent instantiation of class since it is a static class.
     *
     * @access private
     */
    private function __construct() {}


    /**
     * Set Views (pH Views) Statistics with a verification session to avoid duplication in the number of page views.
     *
     * @static
     * @param integer $iId
     * @param string $sTable
     * @return void
     */
    public static function setView($iId, $sTable)
    {
        $oSession = new \PH7\Framework\Session\Session;
        $sSessionName = 'pHV' . $iId . $sTable;

        if (!$oSession->exists($sSessionName))
        {
            Mvc\Model\StatisticModel::setView($iId, $sTable);
            $oSession->set($sSessionName, 1);
        }

        unset($oSession);
    }

    /**
     * @static
     * @param object $oData
     * @return void
     */
    public static function adsOutput($oData)
    {
        $sLink = ''; // Default value

        if (preg_match('#href="(https?://(www\.)?([a-z0-9-_]+)\.[a-z]{2,4}/?([^"]+)?)"#i', $oData->code, $aMatch))
        {
            $sLink = $aMatch[1];
            $sCode = str_replace($sLink, PH7_URL_ROOT . '?ads_url=' . base64_encode($sLink), $oData->code);
        }
        else
        {
            $sCode = $oData->code;
        }

        echo (new \PH7\Framework\Parse\SysVar)->parse($sCode);

        // Stat Avertisements Shows
        self::setView($oData->adsId, 'Ads');

        // Advertisements Clicks
        $oHttpRequest = new Mvc\Request\HttpRequest;
        if ($oHttpRequest->getExists('ads_url'))
        {
            $sUrl = base64_decode($oHttpRequest->get('ads_url'));

            // Check link
            if ($sUrl === $sLink)
            {
                Mvc\Model\DesignModel::addAdsClick($oData->adsId, $sLink);
            }
            // Redirect to the site to advertise
            \PH7\Framework\Url\HeaderUrl::redirect($sUrl);
        }
        unset($oHttpRequest);
    }

}
