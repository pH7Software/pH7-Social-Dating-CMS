<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use PH7\Framework\Mvc\Request\HttpRequest;

class AdsCore extends Framework\Ads\Ads
{

    /**
     * @desc Block constructing to prevent instantiation of class since it is a private class.
     * @access private
     */
    private function __construct() {}

    /**
     * @desc Gets Ads Table.
     * @return string The Table.
     */
    public static function getTable()
    {
        $oHttpRequest = new HttpRequest;
        $sTable = ($oHttpRequest->getExists('ads_type') && $oHttpRequest->get('ads_type') == 'affiliate') ? 'AdsAffiliate' : 'Ads';
        unset($oHttpRequest);
        return $sTable;
    }

    /**
     * @desc Checks Ads Table
     * @return mixed (string or void if table is not valid) Returns the table if it is correct.
     * @throws If the table is not valid, it throws an exception and displays a error message with the method \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() and exit().
     */
    public static function checkTable($sTable)
    {
        switch($sTable)
        {
            case 'Ads':
            case 'AdsAffiliate':
                return $sTable;
            break;

            default:
                Framework\Mvc\Model\Engine\Util\Various::launchErr($sTable);
        }
    }

    /**
     * @param string $sTable
     * @return mixed (string or void if table is not valid) Returns the table if it is correct.
     * @throws If the table is not valid, it throws an exception and displays a error message with the method \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() and exit().
     */
    public static function convertTableToId($sTable)
    {
        switch($sTable)
        {
            case 'Ads':
            case 'AdsAffiliate':
                $sId = 'adsId';
            break;

            default:
                Framework\Mvc\Model\Engine\Util\Various::launchErr();
        }

        return $sId;
    }

}
