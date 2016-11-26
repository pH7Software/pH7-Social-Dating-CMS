<?php
/**
 * @title          Security Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security
 * @version        1.2
 * @history        01/15/2014 - This system replaces the other highly secure password hashing created by Pierre-Henry Soria
 */

namespace PH7\Framework\Security;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Util\Various;

final class Security
{
    const
    PWD_ALGORITHM = PASSWORD_BCRYPT,
    /*** Our salts. Never change these values​​, otherwise all passwords and other strings will be incorrect ***/
    PREFIX_SALT = 'c好，你今Здраврыве ты ў паітаньне е54йте天rt&eh好嗎_dمرحبا أنت بخير ال好嗎attú^u5atá inniu4a,?478привіなたは大丈夫今日はтивпряьоהעלאai54ng_scси днесpt',
    SUFFIX_SALT = '*éà12_you_è§§=≃ù%µµ££$);&,?µp{èàùf*sxdslut_waruआप नमस्क你好，你今ार ठΓει好嗎α σαςb안녕하세oi요 괜찮은 o नमस्कार ठीnjre;,?*-<καλά σήμεραीक आजсегодняm_54tjהעלאdgezsядкمرحبا';

    private static $_aPwdOptions = array('cost' => 14);

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct() {}

    /**
     * Generate Random Salt for Password encryption.
     *
     * @param string $sPwd
     * @return string The Hash Password
     */
    public static function hashPwd($sPwd)
    {
        return password_hash($sPwd , self::PWD_ALGORITHM, self::$_aPwdOptions);
    }

    /**
     * Check the password.
     *
     * @param string $sPwd
     * @param string $sHash
     * @return boolean
     */
    public static function checkPwd($sPwd, $sHash)
    {
        return password_verify($sPwd, $sHash);
    }

    /**
     * Checks if the given hash matches the given options.
     *
     * @param string $sPwd
     * @param string $sHash
     *
     * @return mixed (string | boolean) Returns the new password if the password needs to be rehash, otherwise FALSE
     */
    public static function pwdNeedsRehash($sPwd, $sHash)
    {
        if (password_needs_rehash($sHash, self::PWD_ALGORITHM, self::$_aPwdOptions))
            return self::hashPwd($sPwd);

        return false;
    }

    /**
     * Generate a hash for Cookie Password encryption.
     *
     * @param string $sPassword
     * @param integer $iLength Default: 40
     * @return string The Password Hash
     */
    public static function hashCookie($sPwd, $iLength = 40)
    {
        return self::userHash($sPwd, $iLength);
    }

    /**
     * Generate a hash.
     *
     * @param string $sVal
     * @param integer $iLength Default 80
     * @return string
     */
    public static function hash($sVal, $iLength = 80)
    {
        return Various::padStr(hash('whirlpool', hash('sha512', self::PREFIX_SALT . hash('whirlpool', self::PREFIX_SALT)) . hash('whirlpool', $sVal) . hash('sha512', hash('whirlpool', self::SUFFIX_SALT) . self::SUFFIX_SALT)), $iLength);
    }

    /**
     * Generate a user hash.
     *
     * @param string $sPassword
     * @param integer $iLength
     * @param string $sAlgo The algorithm. Only 'whirlpool' or 'sha512' is accepted.
     * @return string
     */
    public static function userHash($sVal, $iLength, $sAlgo = 'whirlpool')
    {
        if ($sAlgo !== 'whirlpool' && $sAlgo !== 'sha512')
            exit('Wrong algorithm! Please choose between "whirlpool" or "sha512"');

        $sSalt = self::PREFIX_SALT . \PH7\Framework\Ip\Ip::get() . self::SUFFIX_SALT . (new \PH7\Framework\Navigation\Browser)->getUserAgent();
        return hash_pbkdf2($sAlgo, $sVal, $sSalt, 10000, $iLength);
    }
}
