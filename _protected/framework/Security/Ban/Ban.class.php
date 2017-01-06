<?php
/**
 * Method for managing the banishment of pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Ban
 * @version          1.3
 */

namespace PH7\Framework\Security\Ban;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Statik;

class Ban
{
    const
    DIR = 'bans/',
    USERNAME_FILE = 'username.txt',
    EMAIL_FILE = 'email.txt',
    WORD_FILE = 'word.txt',
    BANK_ACCOUNT_FILE = 'bank_account.txt',
    IP_FILE = 'ip.txt';

    private static $_sFile, $_sVal, $_bIsEmail = false;

    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Checks if the username is not a banned username.
     *
     * @param string $sVal
     * @return boolean
     */
    public static function isUsername($sVal)
    {
        self::$_sFile = static::USERNAME_FILE;
        self::$_sVal = $sVal;
        return self::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isEmail($sVal)
    {
        self::$_sFile = static::EMAIL_FILE;
        self::$_sVal = $sVal;
        self::$_bIsEmail = true;
        return self::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isBankAccount($sVal)
    {
        self::$_sFile = static::BANK_ACCOUNT_FILE;
        self::$_sVal = $sVal;
        self::$_bIsEmail = true;
        return self::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isIp($sVal)
    {
        self::$_sFile = static::IP_FILE;
        self::$_sVal = $sVal;
        return self::_is();
    }

    /**
     * Filter words.
     *
     * @param string $sVal
     * @return string
     */
    public static function filterWord($sVal, $bWordReplace = true)
    {
        self::$_sFile = static::WORD_FILE;
        self::$_sVal = $sVal;
        return self::_replace($bWordReplace);
    }

    /**
     * Generic method that checks if there.
     *
     * @access private
     * @return boolean Returns TRUE if the text is banned, FALSE otherwise.
     */
    private static function _is()
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . self::$_sFile);

        if (self::$_bIsEmail)
            if (self::_check(strrchr(self::$_sVal, '@'))) return true;

        return self::_check(self::$_sVal);
    }

    /**
     * Generic method to replace forbidden words.
     *
     * @access private
     * @param boolean $bWordReplace TRUE = Replace the ban word by an other word. FALSE = Replace the ban word by an empty string.
     * @return string The clean text.
     */
    private static function _replace($bWordReplace)
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . self::$_sFile);

        foreach ($aBans as $sBan)
        {
            $sBan = trim($sBan);
            $sWordReplace = ($bWordReplace) ? \PH7\Framework\Mvc\Model\DbConfig::getSetting('banWordReplace') : '';
            self::$_sVal = str_ireplace($sBan, $sWordReplace, self::$_sVal);
        }

        return self::$_sVal;
    }

    /**
     * @access private
     * @param string $sVal
     * @return boolean Returns TRUE if the value is banned, FALSE otherwise.
     */
    private static function _check($sVal)
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . self::$_sFile);

        return in_array($sVal, array_map('trim', $aBans));
    }
}
