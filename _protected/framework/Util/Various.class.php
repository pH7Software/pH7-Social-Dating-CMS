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

declare(strict_types=1);

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
    public static function genRnd(?string $sStr = null, int $iLength = self::DEFAULT_LENGTH): string
    {
        if ($iLength <= 0) {
            return '';
        }

        $iBytesLength = max(16, (int)ceil($iLength / 2));

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
    public static function padStr(string $sStr, int $iLength = self::DEFAULT_LENGTH): string
    {
        return ((new Str())->length($sStr) >= $iLength) ? substr($sStr, 0, $iLength) : str_pad($sStr, $iLength, $sStr);
    }

    /**
     * Generate Random Word.
     *
     * @param int $iLength
     *
     * @return string
     */
    public static function genRndWord(int $iLength): string
    {
        if ($iLength <= 0) {
            return '';
        }

        $sWord = '';
        $aSpecialChars = ['-', '_', '~', '|', '%', '^', '!', '$', '#', '@', '?'];
        $aKeys = array_merge(
            range('0', '9'),
            range('a', 'z'),
            range('A', 'Z'),
            $aSpecialChars
        );
        $iLastKey = array_key_last($aKeys);

        for ($iAmount = 0; $iAmount < $iLength; ++$iAmount) {
            $sWord .= $aKeys[random_int(0, $iLastKey)];
        }

        return $sWord;
    }
}
