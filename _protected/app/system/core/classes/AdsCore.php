<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use PH7\Framework\Mvc\Request\Http;

class AdsCore extends Framework\Ads\Ads
{

    /**
     * Block constructing to prevent instantiation of class since it's a private class.
     */
    private function __construct() {}

    /**
     * Gets Ads Table.
     *
     * @return string The Table.
     */
    public static function getTable()
    {
        $oHttpRequest = new Http;
        $sTable = ($oHttpRequest->getExists('ads_type') && $oHttpRequest->get('ads_type') == 'affiliate') ? 'AdsAffiliates' : 'Ads';
        unset($oHttpRequest);
        return $sTable;
    }

    /**
     * Checks Ads Table.
     *
     * @return mixed (string or void if table is not valid) Returns the table if it is correct.
     * @throws If the table is not valid, it throws an exception and displays an error message with the method \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() and exit().
     */
    public static function checkTable($sTable)
    {
        switch ($sTable)
        {
            case 'Ads':
            case 'AdsAffiliates':
                return $sTable;
            break;

            default:
                Framework\Mvc\Model\Engine\Util\Various::launchErr($sTable);
        }
    }

    /**
     * Convert table to Ads's ID.
     *
     * @param string $sTable
     * @return mixed (string or void if table is not valid) Returns the table if it is correct.
     * @throws If the table is not valid, it throws an exception and displays an error message with the method \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() and exit().
     */
    public static function convertTableToId($sTable)
    {
        switch ($sTable)
        {
            case 'Ads':
            case 'AdsAffiliates':
                $sId = 'adsId';
            break;

            default:
                Framework\Mvc\Model\Engine\Util\Various::launchErr();
        }

        return $sId;
    }

}
