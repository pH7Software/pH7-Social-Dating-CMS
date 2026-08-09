<?php

/**
 * @title          Various Class
 *
 * @desc           MISC (Miscellaneous Functions) Class.
 *                 Some various useful methods.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7\Framework\Util;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str;

class Various
{
    public const DEFAULT_LENGTH = 40;

    /**
     * Generate Random string.
     *
     * @param string|null $sStr
     * @param int         $iLength default is 40 Characters
     *
     * @return string
     */
    public static function genRnd($sStr = null, $iLength = self::DEFAULT_LENGTH)
    {
        $iLength = (int)$iLength;
        if ($iLength <= 0) {
            return '';
        }

        $iBytesLength = (int)max(16, ceil($iLength / 2));

        $sChars = bin2hex(random_bytes($iBytesLength));

        return substr($sChars, 0, $iLength);
    }

    /**
     * Padding String.
     *
     * @param string $sStr
     * @param int    $iLength
     *
     * @return string
     */
    public static function padStr($sStr, $iLength = self::DEFAULT_LENGTH)
    {
        $iLength = (int)$iLength;

        return ((new Str())->length($sStr) >= $iLength) ? substr($sStr, 0, $iLength) : str_pad($sStr, $iLength, $sStr);
    }

    /**
     * Generate Random Word.
     *
     * @param int $iLength
     *
     * @return string
     */
    public static function genRndWord($iLength)
    {
        $sWord = '';
        $aSpecialChars = ['-', '_', '~', '|', '%', '^', '!', '$', '#', '@', '?'];
        $aKeys = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('Z', 'Z'),
            $aSpecialChars
        );

        for ($iAmount = 0; $iAmount < $iLength; ++$iAmount) {
            $sWord .= $aKeys[array_rand($aKeys)];
        }

        return $sWord;
    }
}
