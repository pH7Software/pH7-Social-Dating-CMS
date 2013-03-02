<?php
/**
 * @title Security Class
 *
 * Security
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security
 * @version        1.1
 */

namespace PH7\Framework\Security;

defined('PH7') or exit('Restricted access');

use
PH7\Framework\Registry\Registry,
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Util\Various;

final class Security
{

    const
    ADMIN = 'admin',
    USER = 'user',
    LENGTH_USER_PASSWORD = 120,
    LENGTH_ADMIN_PASSWORD = 240,
    /***** Our grain of salt. Never change this value, if all passwords and other strings are incorrect *****/
    PREFIX_SALT = 'c好，你今Здраврыве ты ў паітаньне е54йте天rt&eh好嗎_dمرحبا أنت بخير ال好嗎attú^u5atá inniu4a,?478привіなたは大丈夫今日はтивпряьоהעלאai54ng_scси днесpt',
    SUFFIX_SALT = '*éà12_you_è§§=≃ù%µµ££$);&,?µp{èàùf*sxdslut_waruआप नमस्क你好，你今ार ठΓει好嗎α σαςb안녕하세oi요 괜찮은 o नमस्कार ठीnjre;,?*-<καλά σήμεραीक आजсегодняm_54tjהעלאdgezsядкمرحبا';

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct() {}

    /**
     * Generate Random Salt for Password encryption.
     *
     * @param string $sPrefixSalt
     * @param string $sPassword
     * @param string $sSuffixSalt
     * @param string $sMod The Values are the constants of this class: self::ADMIN or self::USER  |  Default NULL
     * @return string The Hash Password
     */
    public static function hashPwd($sPrefixSalt, $sPassword, $sSuffixSalt, $sMod = null)
    {
        // Password 240 characters for administrators and 120 for users
        if (!empty($sMod) && ($sMod === self::USER || $sMod === self::ADMIN))
            $iLengthPwd = ($sMod === self::ADMIN) ? self::LENGTH_ADMIN_PASSWORD : self::LENGTH_USER_PASSWORD;
        else
            $iLengthPwd = (Registry::getInstance()->module === PH7_ADMIN_MOD || (new HttpRequest)->get('mod') === PH7_ADMIN_MOD) ? self::LENGTH_ADMIN_PASSWORD : self::LENGTH_USER_PASSWORD;

        // Chop the password
        return Various::padStr(hash('whirlpool', hash('sha512', self::PREFIX_SALT . hash('whirlpool', $sPrefixSalt)) . hash('whirlpool', $sPassword) . hash('sha512', hash('whirlpool', $sSuffixSalt) . self::SUFFIX_SALT)), $iLengthPwd);
    }

    /**
     * Generate hash for Cookie Password encryption.
     *
     * @param string $sPassword
     * @return string The Hash Password
     */
    public static function hashCookie($sPassword)
    {
        return sha1(self::PREFIX_SALT . \PH7\Framework\Ip\Ip::get() . $sPassword . self::SUFFIX_SALT . (new \PH7\Framework\Navigation\Browser)->getUserAgent());
    }

    /**
     * Generate a hash.
     *
     * @param string $sValue
     * @param integer $iLength Default 80
     * @return string
     */
    public static function hash($sValue, $iLength = 80)
    {
        return Various::padStr(hash('whirlpool', hash('sha512', self::PREFIX_SALT . hash('whirlpool', self::PREFIX_SALT)) . hash('whirlpool', $sValue) . hash('sha512', hash('whirlpool', self::SUFFIX_SALT) . self::SUFFIX_SALT)), $iLength);
    }

}
