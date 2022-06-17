<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Security / Ban
 * @version          2.0
 */

declare(strict_types=1);

namespace PH7\Framework\Security\Ban;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Pattern\Statik;

class Ban
{
    public const DIR = 'banned/';
    public const EXT = '.txt';
    public const USERNAME_FILE = 'username.txt';
    public const EMAIL_FILE = 'email.txt';
    public const WORD_FILE = 'word.txt';
    public const BANK_ACCOUNT_FILE = 'bank_account.txt';
    public const IP_FILE = 'ip.txt';

    private const COMMENT_SIGN = '#';

    private static string $sFile;

    private static string $sVal;

    private static bool $bIsEmail = false;

    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Checks if the username is not a banned username.
     */
    public static function isUsername(string $sVal): bool
    {
        self::$sFile = static::USERNAME_FILE;
        self::$sVal = $sVal;

        return self::is();
    }

    public static function isEmail(string $sVal): bool
    {
        self::$sFile = static::EMAIL_FILE;
        self::$sVal = $sVal;
        self::$bIsEmail = true;

        return self::is();
    }

    public static function isBankAccount(string $sVal): bool
    {
        self::$sFile = static::BANK_ACCOUNT_FILE;
        self::$sVal = $sVal;
        self::$bIsEmail = true;

        return self::is();
    }

    public static function isIp(string $sVal): bool
    {
        self::$sFile = static::IP_FILE;
        self::$sVal = $sVal;

        return self::is();
    }

    public static function filterWord(string $sVal, bool $bWordReplace = true): string
    {
        self::$sFile = static::WORD_FILE;
        self::$sVal = $sVal;

        return self::replace($bWordReplace);
    }

    /**
     * Generic method that checks if a keyword has been banned.
     *
     * @return bool Returns TRUE if the text is banned, FALSE otherwise.
     */
    private static function is(): bool
    {
        self::setCaseInsensitive();

        if (self::$bIsEmail) {
            $mEmailDomain = strrchr(self::$sVal, '@');
            if ($mEmailDomain === false) {
                return false;
            }
            if (self::check($mEmailDomain)) {
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
     * @return string|null The clean text.
     */
    private static function replace(bool $bWordReplace): ?string
    {
        $aBannedContents = self::readFile();

        foreach ($aBannedContents as $sBan) {
            $sBan = trim($sBan);

            if (empty($sBan) || self::isCommentFound($sBan)) {
                // Skip comments
                continue;
            }

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
    private static function check(string $sVal): bool
    {
        $aBannedContents = self::readFile();

        return in_array($sVal, array_map('trim', $aBannedContents), true);
    }

    private static function setCaseInsensitive(): void
    {
        self::$sVal = strtolower(self::$sVal);
    }

    private static function isCommentFound($sBan): bool
    {
        return strpos($sBan, self::COMMENT_SIGN) === 0;
    }

    private static function readFile(): array
    {
        return (array)file(PH7_PATH_APP_CONFIG . static::DIR . self::$sFile, FILE_SKIP_EMPTY_LINES);
    }
}
