<?php
/**
 * @title            Various File Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 * @version          0.4
 */

namespace PH7\Framework\File;
defined('PH7') or exit('Restricted access');

class Various
{

    /**
     * Convert bytes to human readable format.
     *
     * @static
     * @param integer $iBytes The size in bytes.
     * @param integer $iPrecision Default 2
     * @return string The size.
     */
    public static function bytesToSize($iBytes, $iPrecision = 2)
    {
        $aUnits = ['byte', 'kilobyte', 'megabyte', 'gigabyte', 'terabyte'];

        $iBytes = max($iBytes, 0);
        $iPow = floor(($iBytes ? log($iBytes) : 0) / log(1024));
        $iPow = min($iPow, count($aUnits)-1);
        $iBytes /= (1 << (10 * $iPow));

        return t($aUnits[$iPow], round($iBytes, $iPrecision));
    }

}
