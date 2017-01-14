<?php
/**
 * @title          Various Class
 * @desc           MISC (Miscellaneous Functions) Class.
 *                 Some various useful methods.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Util
 */

namespace PH7\Framework\Util;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Navigation\Browser;

class Various
{
    const MAX_LENGTH = 40;

    /**
     * Generate Random.
     *
     * @static
     * @param string $sStr
     * @param integer $iLength Default is 40 Characters.
     * @return string
     */
    public static function genRnd($sStr = null, $iLength = self::MAX_LENGTH)
    {
        $sStr = (!empty($sStr)) ? (string) $sStr : '';
        $sChars = hash('whirlpool', hash('whirlpool', uniqid(mt_rand(), true) . $sStr . Ip::get() . time()) . hash('sha512', (new Browser)->getUserAgent() . microtime(true)*9999));
        return self::padStr($sChars, $iLength);
    }

    /**
     * Padding String.
     *
     * @static
     * @param string $sStr
     * @param integer $iLength
     * @return string
     */
    public static function padStr($sStr, $iLength = self::MAX_LENGTH)
    {
        $iLength = (int) $iLength;
        return ((new Str)->length($sStr) >= $iLength) ? substr($sStr, 0, $iLength) : str_pad($sStr, $iLength, $sStr);
    }

    /**
     * Generate Random Word.
     *
     * @static
     * @param integer $iMinLength
     * @param integer $iMaxLength
     * @return string
     */
    public static function genRndWord($iMinLength, $iMaxLength)
    {
        // Grab a random word from dictionary between the two lengths
        // and return it

        $sWord = ''; // Default value

        // Remember to change this path to suit your system
        $sDir = PH7_PATH_FRAMEWORK . 'Translate/Dict/';
        $sDict = (file_exists($sDir . PH7_LANG_CODE)) ? PH7_LANG_CODE : PH7_DEFAULT_LANG_CODE;
        if (!$rHandle = @fopen($sDir . $sDict, 'r')) return false;
        $iSize = filesize($sDir . $sDict);

        // Go to a random location in dictionary
        $iRandLocation = rand(0, $iSize);
        fseek($rHandle, $iRandLocation);

        // Get the next whole word of the right length in the file
        do
        {
            $iWordLength = (new Str)->length($sWord);

            if (feof($rHandle)) fseek($rHandle, 0); // if at end, go to start

            $sWord = fgets($rHandle, 80);  // skip first word as it could be partial
            $sWord = fgets($rHandle, 80);  // the potential password
        }
        while ( ($iWordLength < $iMinLength) || ($iWordLength > $iMaxLength) || (strstr($sWord, "'")) );

        fclose($rHandle);

        $sWord = trim($sWord); // trim the trailing \n from fgets
        // add a number  between 0 and 999 to it
        // to make it a slightly better password
        $iRandNumber = mt_rand(0, 999);
        return $sWord . $iRandNumber;
    }
}
