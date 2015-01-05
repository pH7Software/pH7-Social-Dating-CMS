<?php
/**
 * @title            Ban Class
 * @desc             Method for managing the banishment of pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Ban
 * @version          1.3
 */

namespace PH7\Framework\Security\Ban;
defined('PH7') or exit('Restricted access');

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
     * Private constructor to prevent instantiation of class since it is a private class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Checks if the username is not a banned username.
     *
     * @param string $sVal
     * @return boolean
     */
    public static function isUsername($sVal)
    {
        static::$_sFile = static::USERNAME_FILE;
        static::$_sVal = $sVal;
        return static::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isEmail($sVal)
    {
        static::$_sFile = static::EMAIL_FILE;
        static::$_sVal = $sVal;
        static::$_bIsEmail = true;
        return static::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isBankAccount($sVal)
    {
        static::$_sFile = static::BANK_ACCOUNT_FILE;
        static::$_sVal = $sVal;
        static::$_bIsEmail = true;
        return static::_is();
    }

    /**
     * @param string $sVal
     * @return boolean
     */
    public static function isIp($sVal)
    {
        static::$_sFile = static::IP_FILE;
        static::$_sVal = $sVal;
        return static::_is();
    }

    /**
     * Filter words.
     *
     * @param string $sVal
     * @return string
     */
    public static function filterWord($sVal, $bWordReplace = true)
    {
        static::$_sFile = static::WORD_FILE;
        static::$_sVal = $sVal;
        return static::_replace($bWordReplace);
    }

    /**
     * Generic method that checks if there.
     *
     * @access private
     * @return boolean Returns TRUE if the text is banned, FALSE otherwise.
     */
    private static function _is()
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . static::$_sFile);

        if (static::$_bIsEmail)
            if (static::_check(strrchr(static::$_sVal, '@'))) return true;

        return static::_check(static::$_sVal);
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
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . static::$_sFile);

        foreach ($aBans as $sBan)
        {
            $sBan = trim($sBan);
            $sWordReplace = ($bWordReplace) ? \PH7\Framework\Mvc\Model\DbConfig::getSetting('banWordReplace') : '';
            static::$_sVal = str_ireplace($sBan, $sWordReplace, static::$_sVal);
        }

        return static::$_sVal;
    }

    /**
     * @access private
     * @param string $sVal
     * @return boolean Returns TRUE if the value is banned, FALSE otherwise.
     */
    private static function _check($sVal)
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . static::$_sFile);

        return in_array($sVal, array_map('trim', $aBans));
    }

}
