<?php
/**
 * @title          Security Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security
 * @version        1.2
 * @history        01/15/2014 - This system replaces the other highly secure password hashing created by Pierre-Henry Soria
 */

namespace PH7\Framework\Security;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Util\Various;

final class Security
{
    const HASH_LENGTH = 80;
    const COOKIE_HASH_LENGTH = 40;
    const PBKDF2_ITERATION = 10000;

    // TODO: Use PASSWORD_ARGON2I instead when PHP 7.2 will be supported
    const PWD_ALGORITHM = PASSWORD_BCRYPT;
    const PWD_WORK_FACTOR = 12;

    const SHA512_ALGORITHM = 'sha512';
    const WHIRLPOOL_ALGORITHM = 'whirlpool';

    /*** Our salts. Never change these values, otherwise all passwords and other strings will be incorrect ***/
    const PREFIX_SALT = 'c好，你今Здраврыве ты ў паітаньне е54йте天rt&eh好嗎_dمرحبا أنت بخير ال好嗎attú^u5atá inniu4a,?478привіなたは大丈夫今日はтивпряьоהעלאai54ng_scси днесpt';
    const SUFFIX_SALT = '*éà12_you_è§§=≃ù%µµ££$);&,?µp{èàùf*sxdslut_waruआप नमस्क你好，你今ार ठΓει好嗎α σαςb안녕하세oi요 괜찮은 o नमस्कार ठीnjre;,?*-<καλά σήμεραीक आजсегодняm_54tjהעלאdgezsядкمرحبا';

    /** @var array */
    private static $aPwdOptions = ['cost' => self::PWD_WORK_FACTOR];

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Generate Random Salt for Password encryption.
     *
     * @param string $sPassword
     *
     * @return string The Hash Password
     */
    public static function hashPwd($sPassword)
    {
        return password_hash($sPassword, self::PWD_ALGORITHM, self::$aPwdOptions);
    }

    /**
     * Check the password.
     *
     * @param string $sPassword
     * @param string $sHash
     *
     * @return bool
     */
    public static function checkPwd($sPassword, $sHash)
    {
        return password_verify($sPassword, $sHash);
    }

    /**
     * Checks if the given hash matches the given options.
     *
     * @param string $sPassword
     * @param string $sHash
     *
     * @return string|bool Returns the new password if the password needs to be rehash, otherwise FALSE
     */
    public static function pwdNeedsRehash($sPassword, $sHash)
    {
        if (password_needs_rehash($sHash, self::PWD_ALGORITHM, self::$aPwdOptions)) {
            return self::hashPwd($sPassword);
        }

        return false;
    }

    /**
     * Generate a hash for Cookie Password encryption.
     *
     * @param string $sPassword
     * @param int $iLength
     *
     * @return string The password hashed.
     *
     * @throws InvalidAlgorithmException
     */
    public static function hashCookie($sPassword, $iLength = self::COOKIE_HASH_LENGTH)
    {
        return self::userHash($sPassword, $iLength);
    }

    /**
     * Generate a hash.
     *
     * @param string $sVal
     * @param int $iLength
     *
     * @return string
     */
    public static function hash($sVal, $iLength = self::HASH_LENGTH)
    {
        $sCoreHashString = hash(self::SHA512_ALGORITHM, self::PREFIX_SALT . hash(self::WHIRLPOOL_ALGORITHM, self::PREFIX_SALT)) . hash(self::WHIRLPOOL_ALGORITHM, $sVal) . hash(self::SHA512_ALGORITHM, hash(self::WHIRLPOOL_ALGORITHM, self::SUFFIX_SALT) . self::SUFFIX_SALT);

        return Various::padStr(
            hash(
                self::WHIRLPOOL_ALGORITHM,
                $sCoreHashString
            ),
            $iLength
        );
    }

    /**
     * Generate a user hash.
     *
     * @param string $sVal
     * @param int $iLength
     * @param string $sAlgo The algorithm. Only 'whirlpool' or 'sha512' are accepted.
     *
     * @return string
     *
     * @throws InvalidAlgorithmException
     */
    public static function userHash($sVal, $iLength, $sAlgo = self::WHIRLPOOL_ALGORITHM)
    {
        if ($sAlgo !== self::WHIRLPOOL_ALGORITHM && $sAlgo !== self::SHA512_ALGORITHM) {
            throw new InvalidAlgorithmException('Wrong algorithm! Please choose between "whirlpool" or "sha512"');
        }

        $sSalt = self::PREFIX_SALT . Ip::get() . self::SUFFIX_SALT . (new Browser)->getUserAgent();

        return hash_pbkdf2($sAlgo, $sVal, $sSalt, self::PBKDF2_ITERATION, $iLength);
    }
}
