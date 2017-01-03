<?php
/**
 * @title            Http Request
 * @desc             Useful class for managing HTTP request.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Request
 * @version          1.2
 * @update           09/24/13
 * @link             http://hizup.com
 */

namespace PH7\Framework\Mvc\Request;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Registry\Registry,
PH7\Framework\Security as Secty;

/**
 * Small example of using this class.
 *
 * @example
 *
 * Example with Http::get() method:
 *
 * <code>
 * namespace MyExample;
 *
 * $oHttpRequest = new \PH7\Framework\Mvc\Request\Http;
 * if ($oHttpRequest->getExists('pH'))
 *     echo $oHttpRequest->get('pH');
 * else
 *     echo 'I love Pierre-Henry S ;)';
 * </code>
 *
 */

class Http extends \PH7\Framework\Http\Http
{

    const
    METHOD_HEAD = 'HEAD',
    METHOD_GET = 'GET',
    METHOD_POST = 'POST',
    METHOD_PUT = 'PUT',
    METHOD_PATCH = 'PATCH',
    METHOD_DELETE = 'DELETE',
    METHOD_OPTIONS = 'OPTIONS',
    ONLY_XSS_CLEAN = 'XSS_CLEAN',
    NO_CLEAN = 'NO_CLEAN';

    private $_sRequestUri, $_sMethod, $_aRequest, $_aGet, $_aPost, $_bStrip = false;

    public function __construct()
    {
        $this->_sMethod = $this->getRequestMethod();
        $this->_sRequestUri = $this->getRequestUri();

        $this->_aRequest = &$_REQUEST;
        $this->_aGet = &$_GET;
        $this->_aPost = &$_POST;
    }

    /**
     * Get method.
     *
     * @return string 'GET', 'POST', 'HEAD', 'PUT'
     */
    public function getMethod()
    {
        return $this->_sMethod;
    }

    /**
     * Check is the request method GET exists.
     *
     * @param mixed (array or string) $mKey The key of the request or an array with the list of key of the variables request.
     * @param string $sParam Optional parameter, check the type of the request variable | Value type is: str, int, float, bool
     * @return boolean
     */
    public function getExists($mKey, $sParam = null)
    {
        $bExists = false; // Default value

        if (is_array($mKey))
        {
            foreach ($mKey as $sKey)
            {
                if (!$bExists = $this->getExists($sKey, $sParam)) // Recursive method
                    break;
            }
        }
        else
        {
            if (!$this->validate($this->_aGet, $mKey, $sParam)) return false;

            $bExists = isset($this->_aGet[$mKey]);
        }

        return $bExists;
    }

    /**
     * Check is the POST request method exists.
     *
     * @param mixed (array or string) $mKey The key of the request or an array with the list of key of the variables request.
     * @param string $sParam Optional parameter, check the type of the request variable | Value type is: str, int, float, bool
     * @return boolean
     */
    public function postExists($mKey, $sParam = null)
    {
        $bExists = false; // Default value

        if (is_array($mKey))
        {
            foreach ($mKey as $sKey)
            {
                if (!$bExists = $this->postExists($sKey, $sParam)) // Recursive method
                    break;
            }
        }
        else
        {
            if (!$this->validate($this->_aPost, $mKey, $sParam)) return false;

            $bExists = isset($this->_aPost[$mKey]);
        }

        return $bExists;
    }

    /**
     * Sets a variable in the GET and POST request.
     *
     * @param string $sKey
     * @param string $sValue
     * return void
     */
    public function sets($sKey, $sValue)
    {
        $this->setGet($sKey, $sValue);
        $this->setPost($sKey, $sValue);
    }

    /**
     * Add a variable in the GET request.
     *
     * @param string $sKey
     * @param string $sValue
     * return void
     */
    public function setGet($sKey, $sValue)
    {
        $this->_setRequestVar($this->_aGet, $sKey, $sValue);
    }

    /**
     * Add a variable in the POST request.
     *
     * @param string $sKey
     * @param string $sValue
     * return void
     */
    public function setPost($sKey, $sValue)
    {
        $this->_setRequestVar($this->_aPost, $sKey, $sValue);
    }

    /**
     * $_GET and $_POST request type.
     *
     * @param string $sKey
     * @param string $sParam Optional parameter, set a type of the request | Value type is: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     * @return string with the "Str::escape()" method to secure the data display unless you specify the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     */
    public function gets($sKey, $sParam = null)
    {
        if ($this->getExists($sKey, $sParam))
        {
            return $this->get($sKey, $sParam);
        }
        elseif ($this->postExists($sKey, $sParam))
        {
            return $this->post($sKey, $sParam);
        }
    }

    /**
     * Get Request Parameter.
     *
     * @param string $sKey The key of the request.
     * @param string $sParam Optional parameter, set a type of the request | Value type is: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     * @param boolean $bStrip If TRUE, strip only HTML tags instead of converting them into HTML entities. Less secure. Default: FALSE
     * @return string with the "Str::escape()" method to secure the data display unless you specify the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     */
    public function get($sKey, $sParam = null, $bStrip = false)
    {
        //if ($this->_sMethod !== self::METHOD_GET) throw new Exception('GET');

        if (!isset($this->_aGet[$sKey]))
            return '';

        // Clear the CSRF token in the request variable
       /*
        * CSRF token is not used in all URLs
        $this->_aGet[$sKey] = $this->_clearCSRFToken($this->_aGet, $sKey);
        */

        if ($sParam === self::NO_CLEAN)
            return $this->_aGet[$sKey];

        $this->_bStrip = $bStrip;
        $this->setType($this->_aGet, $sKey, $sParam);

        return $this->cleanData($this->_aGet, $sKey, $sParam);
    }

    /**
     * Post Request Parameter.
     *
     * @param string $sKey The key of the request.
     * @param string $sParam Optional parameter, set a type of the request | Value type is: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     * @param boolean $bStrip If TRUE, strip only HTML tags instead of converting them into HTML entities. Less secure. Default: FALSE
     * @return string The string with the "Str::escape()" method to secure the data display unless you specify the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     * @throws \PH7\Framework\Mvc\Request\Exception If the request is not POST.
     */
    public function post($sKey, $sParam = null, $bStrip = false)
    {
        if ($this->_sMethod !== self::METHOD_POST) throw new Exception('POST');

        if (!isset($this->_aPost[$sKey]))
            return '';

        if ($sParam === self::NO_CLEAN)
            return $this->_aPost[$sKey];

        $this->_bStrip = $bStrip;
        $this->setType($this->_aPost, $sKey, $sParam);

        return $this->cleanData($this->_aPost, $sKey, $sParam);
    }

    /**
     * Get Request URI.
     *
     * @return string URI
     */
    public function requestUri()
    {
        $sRequestUri = (substr($this->_sRequestUri, 0, 1) === PH7_SH) ? substr($this->_sRequestUri, 1) : $this->_sRequestUri;
        $sRelative = (substr(PH7_RELATIVE, 0, 1) === PH7_SH) ? substr(PH7_RELATIVE, 1) : PH7_RELATIVE;
        return str_replace($sRelative, '', $sRequestUri);
    }

    /**
     * @return string The current URL.
     */
    public function currentUrl()
    {
        return PH7_URL_PROT . PH7_DOMAIN . $this->_sRequestUri;
    }

    /**
     * @return string The Previous Page.
     */
    public function previousPage()
    {
        return (new \PH7\Framework\Navigation\Browser)->getHttpReferer();
    }

    /**
     * @return string The name of the current controller.
     */
    public function currentController()
    {
       return str_replace('controller', '', strtolower(Registry::getInstance()->controller));
    }

    /**
     * @return string The correct pH7's URL.
     */
    public function pH7Url($sUrl)
    {
      return ($this->isRelativeUrl($sUrl)) ? PH7_URL_ROOT . $sUrl : $sUrl;
    }

    /**
     * Check is a request variable is valid.
     *
     * @access protected
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sParam
     * @return boolean Returns TRUE if the type of the variable is valid, FALSE otherwise.
     */
    protected function validate(&$aType, $sKey, $sParam)
    {
        if (!empty($sParam))
            if (!Secty\Validate\Validate::type($aType[$sKey], $sParam)) return false;

        return true;
    }

    /**
     * Set the type of a request variable.
     *
     * @access protected
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sType A PHP Type: "bool", "int", "float", "string", "array", "object" or "null".
     * @return void
     */
    protected function setType(&$aType, $sKey, $sType)
    {
        if (!empty($sType) && $sType !== self::ONLY_XSS_CLEAN)
            settype($aType[$sKey], $sType);
    }

    /**
     * Clean Data.
     *
     * @access protected
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sParam Optional self::ONLY_XSS_CLEAN To delete only the XSS vulnerability.
     * @return string
     */
    protected function cleanData(&$aType, $sKey, $sParam)
    {
        // For space and other in address bar
        if ($this->_sMethod === self::METHOD_GET)
            $aType[$sKey] = str_replace(array('%20','%27','%C3','%A9','%C3','%A9','%C3','%A9'), '', $aType[$sKey]);

        if (!empty($sParam) && $sParam === self::ONLY_XSS_CLEAN)
            return (new Secty\Validate\Filter)->xssClean($aType[$sKey]);

        return escape($aType[$sKey], $this->_bStrip);
    }

    /**
     * Set the Request Variable.
     *
     * @access private
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sValue
     * @return void
     */
    private function _setRequestVar(&$aType, $sKey, $sValue)
    {
        $aType[$sKey] = $sValue;
    }

    /**
     * Clear the CSRF token in the request variable name.
     *
     * @access private
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey The request variable to clean.
     * @return string
     */
    private function _clearCSRFToken(&$aType, $sKey)
    {
        return preg_replace('#(\?|&)' . Secty\CSRF\Token::VAR_NAME . '\=[^/]+$#', '', $aType[$sKey]);
    }

    public function __destruct()
    {
        unset(
            $this->_sRequestUri,
            $this->_sMethod,
            $this->_aRequest,
            $this->_aGet,
            $this->_aPost
        );
    }

}
