<?php
/**
 * Method for managing the banishment of pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Ban
 * @version          1.3
 */

namespace PH7\Framework\Security\Ban;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Pattern\Statik;

class Ban
{
    const DIR = 'bans/';
    const USERNAME_FILE = 'username.txt';
    const EMAIL_FILE = 'email.txt';
    const WORD_FILE = 'word.txt';
    const BANK_ACCOUNT_FILE = 'bank_account.txt';
    const IP_FILE = 'ip.txt';

    /** @var string */
    private static $sFile;

    /** @var string */
    private static $sVal;

    /** @var bool */
    private static $bIsEmail = false;

    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Checks if the username is not a banned username.
     *
     * @param string $sVal
     *
     * @return bool
     */
    public static function isUsername($sVal)
    {
        self::$sFile = static::USERNAME_FILE;
        self::$sVal = $sVal;

        return self::is();
    }

    /**
     * @param string $sVal
     *
     * @return bool
     */
    public static function isEmail($sVal)
    {
        self::$sFile = static::EMAIL_FILE;
        self::$sVal = $sVal;
        self::$bIsEmail = true;

        return self::is();
    }

    /**
     * @param string $sVal
     *
     * @return bool
     */
    public static function isBankAccount($sVal)
    {
        self::$sFile = static::BANK_ACCOUNT_FILE;
        self::$sVal = $sVal;
        self::$bIsEmail = true;

        return self::is();
    }

    /**
     * @param string $sVal
     *
     * @return bool
     */
    public static function isIp($sVal)
    {
        self::$sFile = static::IP_FILE;
        self::$sVal = $sVal;

        return self::is();
    }

    /**
     * Filter words.
     *
     * @param string $sVal
     *
     * @return string
     */
    public static function filterWord($sVal, $bWordReplace = true)
    {
        self::$sFile = static::WORD_FILE;
        self::$sVal = $sVal;

        return self::replace($bWordReplace);
    }

    /**
     * Generic method that checks if there.
     *
     * @return bool Returns TRUE if the text is banned, FALSE otherwise.
     */
    private static function is()
    {
        if (self::$bIsEmail) {
            if (self::check(strrchr(self::$sVal, '@'))) {
                return true;
            }
        }

        return self::check(self::$sVal);
    }

    /**
     * Generic method to replace forbidden words.
     *
     * @param bool $bWordReplace TRUE = Replace the ban word by an other word. FALSE = Replace the ban word by an empty string.
     *
     * @return string The clean text.
     */
    private static function replace($bWordReplace)
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . self::$sFile);

        foreach ($aBans as $sBan) {
            $sBan = trim($sBan);
            $sWordReplace = $bWordReplace ? DbConfig::getSetting('banWordReplace') : '';
            self::$sVal = str_ireplace($sBan, $sWordReplace, self::$sVal);
        }

        return self::$sVal;
    }

    /**
     * @param string $sVal
     *
     * @return bool Returns TRUE if the value is banned, FALSE otherwise.
     */
    private static function check($sVal)
    {
        $aBans = file(PH7_PATH_APP_CONFIG . static::DIR . self::$sFile);

        return in_array($sVal, array_map('trim', $aBans), true);
    }
}
