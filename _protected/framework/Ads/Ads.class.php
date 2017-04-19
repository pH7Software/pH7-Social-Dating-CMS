<?php
/**
 * @title            Advertisement Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ads
 */

namespace PH7\Framework\Ads;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc;
use PH7\Framework\Parse\SysVar;
use PH7\Framework\Analytics\Statistic;

class Ads
{
    const PARAM_URL = 'ad_click';

    /**
     * Output Advertisement.
     *
     * @param object $oData Db query.
     *
     * @return string
     */
    public static function output($oData)
    {
        // Stat Advertisement Shows
        Statistic::setView($oData->adsId, 'Ads');

        // Advertisement Clicks
        $oHttpRequest = new Mvc\Request\Http;
        if ($oHttpRequest->getExists(static::PARAM_URL) && $oHttpRequest->get(static::PARAM_URL) == $oData->adsId) {
            Mvc\Model\Ads::setClick($oData->adsId);
        }
        unset($oHttpRequest);

        return (new SysVar)->parse($oData->code);
    }
}
