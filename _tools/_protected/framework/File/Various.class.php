<?php
/**
 * @title            Various File Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;

class Various
{
    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Convert bytes to human readable format.
     *
     * @param int $iBytes The size in bytes.
     * @param int $iPrecision Default 2
     *
     * @return string The size.
     */
    public static function bytesToSize($iBytes, $iPrecision = 2)
    {
        $aUnits = ['byte', 'kilobyte', 'megabyte', 'gigabyte', 'terabyte'];

        $iBytes = max($iBytes, 0);
        $iPow = floor(($iBytes ? log($iBytes) : 0) / log(1024));
        $iPow = min($iPow, count($aUnits) - 1);
        $iBytes /= (1 << (10 * $iPow));

        return t($aUnits[$iPow], round($iBytes, $iPrecision));
    }

    /**
     * Convert the string size to bytes.
     *
     * @param string The size (e.g.,10K, 10M, 10G).
     *
     * @return string The integer bytes.
     *
     * @throws PH7InvalidArgumentException Explanatory message.
     */
    public static function sizeToBytes($sSize)
    {
        $cSuffix = strtolower(substr(trim($sSize), -1));
        $iSize = (int)$sSize;

        switch ($cSuffix) {
            // kilobyte
            case 'k':
                $iSize *= 1024;
                break;

            // megabyte
            case 'm':
                $iSize *= 1024 * 1024;
                break;

            // gigabyte
            case 'g':
                $iSize *= 1024 * 1024 * 1024;
                break;

            default:
                throw new PH7InvalidArgumentException(
                    sprintf('Wrong suffix: %s! Choose between: K, M, G', $cSuffix)
                );
        }

        return $iSize;
    }
}
