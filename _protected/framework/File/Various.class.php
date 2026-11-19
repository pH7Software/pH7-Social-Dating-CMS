<?php
/**
 * @title            Various File Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / File
 */

declare(strict_types=1);

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
    public static function bytesToSize(int|float $iBytes, int $iPrecision = 2): string
    {
        $aUnits = ['byte', 'kilobyte', 'megabyte', 'gigabyte', 'terabyte'];

        $iBytes = max($iBytes, 0);
        $iPow = (int)floor(($iBytes > 0 ? log($iBytes) : 0) / log(1024));
        $iPow = min($iPow, array_key_last($aUnits));
        $iBytes /= (1 << (10 * $iPow));

        return t($aUnits[$iPow], round($iBytes, $iPrecision));
    }

    /**
     * Convert the string size to bytes.
     *
     * @param string The size (e.g.,10K, 10M, 10G).
     *
     * @return int The integer bytes.
     *
     * @throws PH7InvalidArgumentException Explanatory message.
     */
    public static function sizeToBytes(string $sSize): int
    {
        $cSuffix = strtolower(substr(trim($sSize), -1));
        $iSize = (int)$sSize;

        $iMultiplier = match ($cSuffix) {
            'k' => 1024,
            'm' => 1024 ** 2,
            'g' => 1024 ** 3,
            default => throw new PH7InvalidArgumentException(
                sprintf('Wrong suffix: %s! Choose between: K, M, G', $cSuffix)
            ),
        };

        return $iSize * $iMultiplier;
    }
}
