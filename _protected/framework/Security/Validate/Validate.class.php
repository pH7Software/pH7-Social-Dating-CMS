<?php

/**
 * @desc             Various methods to Validate.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7\Framework\Security\Validate;

defined('PH7') or exit('Restricted access');

use PH7\DbTableName;
use PH7\ExistCoreModel;
use PH7\Framework\Config\Config;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Math\Measure\Year as YearMeasure;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Str\Str;
use PH7\JustHttp\StatusCode;
use PH7\UserCore;

class Validate
{
    public const REGEX_INVALID_NAME_PATTERN = '`(?:[\|<>"\=\]\[\}\{\\\\$€@%~^#\(\):;\?!¿\*])|(?:(?:https?|ftps?)://)|(?:[0-9])`';
    public const REGEX_DATE_FORMAT = '`^\d\d/\d\d/\d\d\d\d$`';

    public const MAX_INT_NUMBER = 999999999999;

    public const MIN_NAME_LENGTH = 2;
    public const MAX_NAME_LENGTH = 20;

    public const HEX_HASH = '#';
    public const MIN_HEX_LENGTH = 3;
    public const MAX_HEX_LENGTH = 6;

    public const DEF_MIN_USERNAME_LENGTH = 3;
    public const DEF_MIN_PASS_LENGTH = 6;
    public const DEF_MAX_PASS_LENGTH = 60;
    public const DEF_MIN_AGE = 18;
    public const DEF_MAX_AGE = 99;

    private const VALID_HTTP_WEBSITE_RESPONSES = [
        StatusCode::OK,
        StatusCode::MOVED_PERMANENTLY,
        StatusCode::FOUND
    ];

    /** @var Str */
    private $oStr;

    public function __construct()
    {
        $this->oStr = new Str();
    }

    /**
     * Check the type of a value.
     *
     * @param string $sValue
     * @param string $sType     type whose value should be (case-insensitive)
     * @param bool   $bRequired Default TRUE
     *
     * @throws PH7InvalidArgumentException if the type doesn't exist
     *
     * @return bool
     */
    public static function type($sValue, $sType, $bRequired = true)
    {
        $sType = strtolower($sType); // Case-insensitive type.

        if (false === $bRequired && 0 === (new Str())->length($sValue)) { // Yoda Condition ;-)
            return true;
        }

        switch ($sType) {
            case 'str':
            case 'string':
                $bValid = is_string($sValue);
                break;
            case 'int':
            case 'integer':
                $bValid = is_int($sValue);
                break;
            case 'float':
            case 'double':
                $bValid = is_float($sValue);
                break;
            case 'bool':
            case 'boolean':
                $bValid = is_bool($sValue);
                break;
            case 'num':
            case 'numeric':
                $bValid = is_numeric($sValue);
                break;
            case 'arr':
            case 'array':
                $bValid = is_array($sValue);
                break;
            case 'null':
                $bValid = is_null($sValue);
                break;
            case 'obj':
            case 'object':
                $bValid = is_object($sValue);
                break;
            default:
                throw new PH7InvalidArgumentException('Invalid Type!');
        }

        return $bValid;
    }

    /**
     * Validate Is String.
     *
     * @param int $iMin Default NULL
     * @param int $iMax Default NULL
     *
     * @return bool
     */
    public function str($sValue, $iMin = null, $iMax = null)
    {
        // strip_tags() replaces FILTER_SANITIZE_STRING (deprecated in PHP 8.1): for this
        // length/emptiness check it yields the same result without the deprecation notice.
        $sValue = strip_tags((string)$sValue);

        if (!empty($sValue)) {
            if (!empty($iMin) && $this->oStr->length($sValue) < $iMin) {
                return false;
            } elseif (!empty($iMax) && $this->oStr->length($sValue) > $iMax) {
                return false;
            } elseif (!is_string($sValue)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Validate if it's an integer.
     *
     * @param int $iInt
     * @param int $iMin Default 0
     * @param int $iMax Default 999999999999
     *
     * @return bool
     */
    public function int($iInt, $iMin = 0, $iMax = self::MAX_INT_NUMBER)
    {
        $iInt = filter_var($iInt, FILTER_SANITIZE_NUMBER_INT);

        return filter_var($iInt, FILTER_VALIDATE_INT, static::getFilterOption($iMin, $iMax)) !== false;
    }

    /**
     * Validate if it's a numeric.
     *
     * @param string|int (numeric string or integer) $mNumeric
     *
     * @return bool
     */
    public function numeric($mNumeric)
    {
        return is_numeric($mNumeric);
    }

    /**
     * Validate if it's a digit character.
     *
     * @param string (numeric string) $sDigit
     *
     * @return bool
     */
    public function digitChar($sDigit)
    {
        return ctype_digit($sDigit);
    }

    /**
     * Validate if it's a float type.
     *
     * @param float $fFloat
     *
     * @return bool
     */
    public function float($fFloat)
    {
        $fFloat = filter_var($fFloat, FILTER_SANITIZE_NUMBER_FLOAT);

        return filter_var($fFloat, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Validate if it's a boolean type.
     *
     * @param bool $bBool
     *
     * @return bool
     */
    public function bool($bBool)
    {
        return filter_var($bBool, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    /**
     * Validate username pattern and check if username is unique (doesn't already exist).
     *
     * @param string $sUsername
     * @param int    $iMin      Default 3
     * @param int    $iMax      Default 40
     * @param string $sTable    Default DbTableName::MEMBER
     *
     * @return bool
     */
    public function username($sUsername, $iMin = self::DEF_MIN_USERNAME_LENGTH, $iMax = PH7_MAX_USERNAME_LENGTH, $sTable = DbTableName::MEMBER)
    {
        $sUsername = trim($sUsername);

        /*
         * Do quicker check for admin usernames.
         * Admin usernames don't have URL pages (www.mysite.com/@<USERNAME>), so no need to check if any file names conflict with the username
         * and don't need to check for banned usernames either.
         */
        if ($sTable === DbTableName::ADMIN) {
            return preg_match('#^' . PH7_USERNAME_PATTERN . '{' . $iMin . ',' . $iMax . '}$#', $sUsername)
                && !(new ExistCoreModel())->username($sUsername, $sTable);
        }

        return preg_match('#^' . PH7_USERNAME_PATTERN . '{' . $iMin . ',' . $iMax . '}$#', $sUsername)
            && !file_exists(PH7_PATH_ROOT . UserCore::PROFILE_PAGE_PREFIX . $sUsername)
            && !Ban::isUsername($sUsername)
            && !(new ExistCoreModel())->username($sUsername, $sTable);
    }

    /**
     * Validate Password.
     *
     * @param string $sPwd
     * @param int    $iMin Default 6
     * @param int    $iMax Default 60
     *
     * @return bool
     */
    public function password($sPwd, $iMin = self::DEF_MIN_PASS_LENGTH, $iMax = self::DEF_MAX_PASS_LENGTH)
    {
        $iPwdLength = $this->oStr->length($sPwd);

        return $iPwdLength >= $iMin && $iPwdLength <= $iMax;
    }

    /**
     * Validate Email.
     *
     * @param string $sEmail
     * @param bool   $bRealHost checks whether the Email Host is valid
     *
     * @return bool
     */
    public function email($sEmail, $bRealHost = false)
    {
        $sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);

        if ($bRealHost) {
            $sEmailHost = substr(strrchr($sEmail, '@'), 1);
            // This function now works with Windows since version PHP 5.3, so we mustn't include the PEAR NET_DNS library.
            if (!(checkdnsrr($sEmailHost, 'MX') && checkdnsrr($sEmailHost, 'A'))) {
                return false;
            }
        }

        return filter_var($sEmail, FILTER_VALIDATE_EMAIL) !== false
            && $this->oStr->length($sEmail) <= PH7_MAX_EMAIL_LENGTH && !Ban::isEmail($sEmail);
    }

    /**
     * Validate Birthday.
     *
     * @param string $sValue The date must use YYYY-MM-DD or MM/DD/YYYY
     * @param int    $iMin   Default 18
     * @param int    $iMax   Default 99
     *
     * @return bool
     */
    public function birthDate($sValue, $iMin = self::DEF_MIN_AGE, $iMax = self::DEF_MAX_AGE)
    {
        $sBirthDate = static::normalizeBirthDate($sValue);
        if ($sBirthDate === null) {
            return false;
        }

        [$iYear, $iMonth, $iDay] = array_map('intval', explode('-', $sBirthDate));

        $iUserAge = (new YearMeasure($iYear, $iMonth, $iDay))->get(); // Get the current user's age

        return $iUserAge >= $iMin && $iUserAge <= $iMax;
    }

    public static function normalizeBirthDate(mixed $mBirthDate): ?string
    {
        if (!is_string($mBirthDate)) {
            return null;
        }

        $sBirthDate = trim($mBirthDate);
        if (preg_match('/\A(\d{4})-(\d{2})-(\d{2})\z/', $sBirthDate, $aMatches)) {
            $iYear = (int)$aMatches[1];
            $iMonth = (int)$aMatches[2];
            $iDay = (int)$aMatches[3];
        } elseif (preg_match('/\A(\d{2})\/(\d{2})\/(\d{4})\z/', $sBirthDate, $aMatches)) {
            $iMonth = (int)$aMatches[1];
            $iDay = (int)$aMatches[2];
            $iYear = (int)$aMatches[3];
        } else {
            return null;
        }

        if (!checkdate($iMonth, $iDay, $iYear)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $iYear, $iMonth, $iDay);
    }

    /**
     * Validate Date.
     *
     * @param string $sValue
     *
     * @return bool
     */
    public function date($sValue)
    {
        try {
            new \DateTime($sValue);

            return true;
        } catch (\Exception $oE) {
            return false;
        }
    }

    /**
     * Validate URL.
     *
     * @param string $sUrl
     * @param bool   $bRealUrl checks if the current URL exists
     *
     * @return bool
     */
    public function url($sUrl, $bRealUrl = false)
    {
        $sUrl = filter_var($sUrl, FILTER_SANITIZE_URL);

        if (filter_var($sUrl, FILTER_VALIDATE_URL) === false || $this->oStr->length($sUrl) >= PH7_MAX_URL_LENGTH) {
            return false;
        }

        // FILTER_VALIDATE_URL still accepts "javascript://", "ftp://", ... — restrict to http(s) so a
        // stored website URL can't inject a script scheme when it's rendered inside an href attribute.
        if (!preg_match('~^https?://~i', $sUrl)) {
            return false;
        }

        if ($bRealUrl) {
            /**
             * Checks if the URL is valid and contains the HTTP status code '200 OK', '301 Moved Permanently' or '302 Found'.
             */
            $rCurl = curl_init();
            curl_setopt_array($rCurl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $sUrl]);
            curl_exec($rCurl);
            $iResponse = (int)curl_getinfo($rCurl, CURLINFO_HTTP_CODE);

            return in_array($iResponse, self::VALID_HTTP_WEBSITE_RESPONSES, true);
        }

        return true;
    }

    /**
     * Validate IP address.
     *
     * @param string $sIp
     *
     * @return bool
     */
    public function ip($sIp)
    {
        return filter_var($sIp, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate international phone numbers in EPP format.
     *
     * @param string $sNumber
     *
     * @return int|false returns 1 if valid, 0 if invalid, or FALSE in case of error
     */
    public function phone($sNumber)
    {
        return preg_match('#^' . Config::getInstance()->values['validate']['phone.pattern'] . '$#', $sNumber);
    }

    /**
     * @param string $sHexCode
     *
     * @return bool
     */
    public function hex($sHexCode)
    {
        $sHexChars = str_replace(self::HEX_HASH, '', $sHexCode);
        $iHexCharsLength = strlen($sHexChars);

        return strpos($sHexCode, self::HEX_HASH) !== false
            && $iHexCharsLength >= self::MIN_HEX_LENGTH
            && $iHexCharsLength <= self::MAX_HEX_LENGTH;
    }

    /**
     * Validate Name.
     *
     * @param string $sName
     * @param int    $iMin  Default 2
     * @param int    $iMax  Default 20
     *
     * @return bool
     */
    public function name($sName, $iMin = self::MIN_NAME_LENGTH, $iMax = self::MAX_NAME_LENGTH)
    {
        // Check the length
        if ($this->oStr->length($sName) < $iMin || $this->oStr->length($sName) > $iMax) {
            return false;
        }

        // Check the name pattern. Name cannot contain any of the below characters
        if (preg_match(static::REGEX_INVALID_NAME_PATTERN, $sName)) {
            return false;
        }

        return true;
    }

    /*
    /**
     * Check Email with test for check if the host email is valid.
     *
     * @param string $sEmail
     *
     * @return bool
     */
    /*
    public function emailHost($sEmail)
    {
        // The email address must be properly formatted
        if (!$this->email($sEmail))
            return false;

        // It gets domain
        list(, $sDomain ) = explode('@', $sEmail);
        // We look for MX records in DNS
        if (getmxrr($sDomain, $aMxHost))
            $sConnectAddress = $aMxHost[0];
        else
            $sConnectAddress = $sDomain;
        // We created the connection on SMTP port (25)
        if ($rConnect = @fsockopen($sConnectAddress, 25, $iErrno, $sErrStr))
        {
            if (preg_match("/^220/", $sOut = fgets($rConnect, 1024)))
            {
                fputs($rConnect, "HELO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
                $sOut = fgets($rConnect, 1024);
                fputs($rConnect, "MAIL FROM: <{$sEmail}>\r\n");
                $sFrom = fgets($rConnect, 1024);
                fputs($rConnect, "RCPT TO: <{$sEmail}>\r\n");
                $sTo = fgets($rConnect, 1024);
                fputs($rConnect, "QUIT\r\n");
                fclose($rConnect);
                // If the code returned by the RCPT TO is 250 or 251 (cf: RFC)
                // Then the address exists
                if (!preg_match("/^250/", $sTo) && !preg_match("/^251/", $sTo))
                // Address rejected by the serve
                    return false;
                else
                // Accepted by the server address
                    return true;
            }
            else
            {
                // The server did not respond
                return false;
            }
        }
        else
        {
            // You can display an error message by uncommenting the following two lines or leave the return value of the false boolean.
            // echo "Cannot connect to the mail server\n";
            // echo "$iErrno - $sErrStr\n";
            return false;
        }
    }
    //*/

    /**
     * Get option for some filter_var().
     *
     * @param float|int $mMin minimum range
     * @param float|int $mMax maximum range
     *
     * @return array
     */
    protected static function getFilterOption($mMin, $mMax)
    {
        return ['options' => ['min_range' => $mMin, 'max_range' => $mMax]];
    }
}
