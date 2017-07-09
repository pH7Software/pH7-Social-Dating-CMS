<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Pattern\Statik;

class AdsCore extends Framework\Ads\Ads
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

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
     * @return string|void Returns the table name if it is correct, nothing otherwise.
     *
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the table is not valid.
     */
    public static function checkTable($sTable)
    {
        switch ($sTable) {
            case 'Ads':
            case 'AdsAffiliates':
                return $sTable;

            default:
                Various::launchErr($sTable);
        }
    }

    /**
     * Convert table to Ads's ID.
     *
     * @param string $sTable
     *
     * @return string|void Returns the table if it is correct, nothing otherwise.
     *
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the table is not valid.
     */
    public static function convertTableToId($sTable)
    {
        switch ($sTable) {
            case 'Ads':
            case 'AdsAffiliates':
                return 'adsId';

            default:
                Various::launchErr();
        }
    }
}
