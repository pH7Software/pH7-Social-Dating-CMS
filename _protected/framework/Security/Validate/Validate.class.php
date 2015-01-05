<?php
/**
 * @title            Validate Class
 * @desc             Various methods to Validate.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Validate
 * @version          0.8
 */

namespace PH7\Framework\Security\Validate;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str, PH7\Framework\Security\Ban\Ban;

class Validate
{

    private $_oStr;

    public function __construct()
    {
        $this->_oStr = new Str;
    }

    /**
     * Check the type of a value.
     *
     * @static
     * @param string $sValue
     * @param string $sType Type whose value should be (case-insensitive).
     * @param boolean $bRequired Default TRUE
     * @return boolean
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the type doesn't exist.
     */
    public static function type($sValue, $sType, $bRequired = true)
    {
        $sType = strtolower($sType); // Case-insensitive type.

        if(false === $bRequired && 0 === $this->_oStr->length($sValue)) return true; // Yoga Condition ;-)

        switch($sType)
        {
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
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Type is invalid!');
        }

        return $bValid;
    }

    /**
     * Validate Is String.
     *
     * @param $sValue
     * @param integer $iMin Default NULL
     * @param integer $iMax Default NULL
     * @return boolean
     */
    public function str($sValue, $iMin = null, $iMax = null)
    {
        $sValue = filter_var($sValue, FILTER_SANITIZE_STRING);

        if(!empty($sValue))
            if(!empty($iMin) && $this->_oStr->length($sValue) < $iMin)
                return false;
            elseif(!empty($iMax) && $this->_oStr->length($sValue) > $iMax)
                return false;
            elseif(!is_string($sValue))
                return false;
            return true;
        return false;
    }

    /**
     * Validate Is Integer.
     *
     * @param integer $int
     * @param integer $iMin Default 0
     * @param integer $iMax Default 0
     * @return boolean
     */
    public function int($int, $iMin = 0, $iMax = 0)
    {
        $int = filter_var($int, FILTER_SANITIZE_NUMBER_INT);

        return filter_var($int, FILTER_VALIDATE_INT, array('options' => array('min_range'=>$iMin, 'max_range'=>$iMax)));

    }

    /**
     * Validate Is Numeric.
     *
     * @param mixed (numeric string | integer) $mNumeric
     * @return boolean
     */
    public function numeric($mNumeric)
    {
        return is_numeric($mNumeric);
    }

    /**
     * Validate Is Digit Character.
     *
     * @param string (numeric string) $sDigit
     * @return boolean
     */
    public function digitChar($sDigit)
    {
        return ctype_digit($sDigit);
    }

    /**
     * Validate Is Float.
     *
     * @param float $float
     * @param integer $iMin Default 0
     * @param integer $iMax Default 0
     * @return boolean
     */
    public function float($float, $iMin = 0, $iMax = 0)
    {
        $float = filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT);

        return filter_var($float, FILTER_VALIDATE_FLOAT, array('options' => array('min_range'=>$iMin, 'max_range'=>$iMax)));
    }

    /*
     * Validate Is Boolean.
     *
     * @param boolean $bool
     * @return boolean
     */
    public function bool($bool)
    {
        return filter_var($bool, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Validate Username.
     *
     * @param string $sUsername
     * @param integer $iMin Default 3
     * @param integer $iMax Default 40
     * @param string $sTable Default 'Members'
     * @return boolean
     */
    public function username($sUsername, $iMin = 3, $iMax = PH7_MAX_USERNAME_LENGTH, $sTable = 'Members')
    {
         $sUsername = trim($sUsername);

         return (preg_match('#^'.PH7_USERNAME_PATTERN.'{'.$iMin.','.$iMax.'}$#', $sUsername) && !is_file(PH7_PATH_ROOT . $sUsername . PH7_PAGE_EXT) && !Ban::isUsername($sUsername) && !(new \PH7\ExistsCoreModel)->username($sUsername, $sTable));
    }

    /**
     * Validate Password.
     *
     * @param string $sPwd
     * @param integer $iMin Default 6
     * @param integer $iMax Default 60
     * @return boolean
     */
    public function password($sPwd, $iMin = 6, $iMax = 60)
    {
        $iPwdLength = $this->_oStr->length($sPwd);
        return ($iPwdLength >= $iMin && $iPwdLength <= $iMax);
    }

    /**
     * Validate Email.
     *
     * @param string $sEmail
     * @param boolean $bRealHost Checks whether the Email Host is valid. Default FALSE
     * @return boolean
     */
    public function email($sEmail, $bRealHost = false)
    {
        $sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);

        if($bRealHost)
        {
            $sEmailHost = substr(strrchr($sEmail, '@'), 1);
            // This function now works with Windows since version PHP 5.3, so we mustn't include the PEAR NET_DNS library.
            if( !(checkdnsrr($sEmailHost, 'MX') && checkdnsrr($sEmailHost, 'A')) ) return false;
        }
        return (filter_var($sEmail, FILTER_VALIDATE_EMAIL) && $this->_oStr->length($sEmail) <= PH7_MAX_EMAIL_LENGTH && !Ban::isEmail($sEmail));
    }


    /**
     * Validate Birthday.
     *
     * @param string $sValue The date format must be formatted like this: mm/dd/yyyy
     * @param integer $iMin Default 18
     * @param integer $iMax Default 100
     * @return boolean
     */
    public function birthDate($sValue, $iMin = 18, $iMax = 100)
    {
        if(empty($sValue) || !preg_match('#^\d\d/\d\d/\d\d\d\d$#', $sValue)) return false;

        $aBirthDate = explode('/', $sValue); // Format is "mm/dd/yyyy"
        if(!checkdate($aBirthDate[0], $aBirthDate[1], $aBirthDate[2])) return false;

        $iUserAge = (new \PH7\Framework\Math\Measure\Year($aBirthDate[2], $aBirthDate[0], $aBirthDate[1]))->get(); // Get the current user's age
        return ($iUserAge >= $iMin && $iUserAge <= $iMax);
    }

    /**
     * Validate Date.
     *
     * @param string $sValue
     * @return boolean
     */
    public function date($sValue)
    {
        try {
            new \DateTime($sValue);
            return true;
        } catch(\Exception $oE) {
            return false;
        }
    }

    /**
     * Validate URL.
     *
     * @param string $sUrl
     * @param boolean $bRealUrl Checks whether the URL actually exists. Default FALSE
     * @return boolean
     */
    public function url($sUrl, $bRealUrl = false)
    {
        $sUrl = filter_var($sUrl, FILTER_SANITIZE_URL);

        if(!(filter_var($sUrl, FILTER_VALIDATE_URL) && $this->_oStr->length($sUrl) <= PH7_MAX_URL_LENGTH)) return false;

        if($bRealUrl)
        {
            /**
             * Checks if the URL is valid with the HTTP status code '200 OK' or '301 Moved Permanently'
             */
            $aUrl = @get_headers($sUrl);
            return (strpos($aUrl[0], '200 OK') || strpos($aUrl[0], '301 Moved Permanently'));
        }
        else
        {
            return true;
        }
    }

    /**
     * Validate IP address.
     *
     * @param string $sIp
     * @return boolean
     */
    public function ip($sIp)
    {
        return filter_var($sIp, FILTER_VALIDATE_IP);
    }

    /**
     * Validate international phone numbers in EPP format.
     *
     * @param string $sNumber
     * @return boolean
     */
    public function phone($sNumber)
    {
        return preg_match('#^'.\PH7\Framework\Config\Config::getInstance()->values['validate']['phone.pattern'].'$#', $sNumber);
    }

    /*
     * Check Email with test for check if the host email is valid.
     *
     * @param string $sEmail
     * @return boolean
     */
    /*
    public function emailHost($sEmail)
    {
        // The email address must be properly formatted
        if(!$this->email($sEmail))
            return false;

        // It gets domain
        list(, $sDomain ) = explode('@', $sEmail);
        // We look for MX records in DNS
        if(getmxrr($sDomain, $aMxHost))
            $sConnectAddress = $aMxHost[0];
        else
            $sConnectAddress = $sDomain;
        // We created the connection on SMTP port (25)
        if($rConnect = @fsockopen($sConnectAddress, 25, $iErrno, $sErrStr))
        {
            if(preg_match("/^220/", $sOut = fgets($rConnect, 1024)))
            {
                fputs($rConnect, "HELO {$_SERVER['HTTP_HOST']}\r\n");
                $sOut = fgets($rConnect, 1024);
                fputs($rConnect, "MAIL FROM: <{$sEmail}>\r\n");
                $sFrom = fgets($rConnect, 1024);
                fputs($rConnect, "RCPT TO: <{$sEmail}>\r\n");
                $sTo = fgets($rConnect, 1024);
                fputs($rConnect, "QUIT\r\n");
                fclose($rConnect);
                // If the code returned by the RCPT TO is 250 or 251 (cf: RFC)
                // Then the address exists
                if(!preg_match("/^250/", $sTo) && !preg_match("/^251/", $sTo))
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
            // You can display an error message by uncommenting the following two lines or leave the return value of the boolean false.
            // echo "Cannot connect to the mail server\n";
            // echo "$iErrno - $sErrStr\n";
            return false;
        }
    }
    */

}
