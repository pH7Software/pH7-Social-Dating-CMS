<?php
/**
 * @title            Http Request
 * @desc             Useful class for managing HTTP request.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Request
 * @version          1.4
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Mvc\Request;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Navigation\Browser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security as Secty;

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
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST'; // Create (REST)
    const METHOD_PUT = 'PUT'; // Update
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH'; // Partially update
    const METHOD_OPTIONS = 'OPTIONS';
    const ONLY_XSS_CLEAN = 'XSS_CLEAN';
    const NO_CLEAN = 'NO_CLEAN';

    const SPECIAL_CHARS = [
        '%20',
        '%27',
        '%C3',
        '%A9',
        '%C3',
        '%A9',
        '%C3',
        '%A9'
    ];

    /** @var array */
    private $aRequest;

    /** @var array */
    private $aGet;

    /** @var array */
    private $aPost;

    /** @var bool */
    private $bStrip = false;

    public function __construct()
    {
        $this->aRequest = &$_REQUEST;
        $this->aGet = &$_GET;
        $this->aPost = &$_POST;
    }

    /**
     * Get method (alias of Http::getRequestMethod() ).
     *
     * @return string 'GET', 'POST', 'HEAD', 'PUT'
     */
    public function getMethod()
    {
        return $this->getRequestMethod();
    }

    /**
     * Get Request URI (alias of Http::getRequestUri() ).
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getRequestUri();
    }

    /**
     * Check is the request method GET exists.
     *
     * @param array|string $mKey The key of the request or an array with the list of key of the variables request.
     * @param string $sParam Optional, check the type of the request variable | Value types are: str, int, float, bool
     *
     * @return bool
     */
    public function getExists($mKey, $sParam = null)
    {
        $bExists = false; // Default value

        if (is_array($mKey)) {
            foreach ($mKey as $sKey) {
                if (!$bExists = $this->getExists($sKey, $sParam)) {
                    break;
                }
            }
        } else {
            if (!$this->validate($this->aGet, $mKey, $sParam)) {
                return false;
            }

            $bExists = isset($this->aGet[$mKey]);
        }

        return $bExists;
    }

    /**
     * Check is the POST request method exists.
     *
     * @param array|string $mKey The key of the request or an array with the list of key of the variables request.
     * @param string $sParam Optional, check the type of the request variable | Value types are: str, int, float, bool
     *
     * @return bool
     */
    public function postExists($mKey, $sParam = null)
    {
        $bExists = false; // Default value

        if (is_array($mKey)) {
            foreach ($mKey as $sKey) {
                if (!$bExists = $this->postExists($sKey, $sParam)) {
                    break;
                }
            }
        } else {
            if (!$this->validate($this->aPost, $mKey, $sParam)) {
                return false;
            }

            $bExists = isset($this->aPost[$mKey]);
        }

        return $bExists;
    }

    /**
     * Sets a variable in the GET and POST request.
     *
     * @param string $sKey
     * @param string $sValue
     *
     * @return void
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
     *
     * @return void
     */
    public function setGet($sKey, $sValue)
    {
        $this->setRequestVar($this->aGet, $sKey, $sValue);
    }

    /**
     * Add a variable in the POST request.
     *
     * @param string $sKey
     * @param string $sValue
     *
     * @return void
     */
    public function setPost($sKey, $sValue)
    {
        $_SERVER['REQUEST_METHOD'] = self::METHOD_POST;

        $this->setRequestVar($this->aPost, $sKey, $sValue);
    }

    /**
     * $_GET and $_POST request type.
     *
     * @param string $sKey
     * @param string $sParam Optional, set a type of the request | Value types are: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     *
     * @return string|void Uses Str::escape() method to secure the data display unless you specified the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     */
    public function gets($sKey, $sParam = null)
    {
        if ($this->getExists($sKey, $sParam)) {
            return $this->get($sKey, $sParam);
        }

        if ($this->postExists($sKey, $sParam)) {
            return $this->post($sKey, $sParam);
        }
    }

    /**
     * Get Request Parameter.
     *
     * @param string $sKey The key of the request.
     * @param string $sParam Optional, set a type of the request | Value types are: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     * @param bool $bStrip If TRUE, strip only HTML tags instead of converting them into HTML entities, so less secure
     *
     * @return string with the "Str::escape()" method to secure the data display unless you specify the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     */
    public function get($sKey, $sParam = null, $bStrip = false)
    {
        if (!isset($this->aGet[$sKey])) {
            return '';
        }

        // Clear the CSRF token in the request variable
        /*
         * CSRF token is not used in all URLs
         $this->aGet[$sKey] = $this->clearCSRFToken($this->aGet, $sKey);
         */

        if ($sParam === self::NO_CLEAN) {
            return $this->aGet[$sKey];
        }

        $this->bStrip = $bStrip;
        $this->setType($this->aGet, $sKey, $sParam);

        return $this->cleanData($this->aGet, $sKey, $sParam);
    }

    /**
     * Post Request Parameter.
     *
     * @param string $sKey The key of the request.
     * @param string $sParam Optional, set a type of the request | Value types are: str, int, float, bool, self::ONLY_XSS_CLEAN, or self::NO_CLEAN
     * @param bool $bStrip If TRUE, strip only HTML tags instead of converting them into HTML entities, so less secure.
     *
     * @return string The string with the "Str::escape()" method to secure the data display unless you specify the constant "self::ONLY_XSS_CLEAN" or "self::NO_CLEAN"
     *
     * @throws WrongRequestMethodException If the request is not POST.
     */
    public function post($sKey, $sParam = null, $bStrip = true)
    {
        if ($this->getMethod() !== self::METHOD_POST) {
            throw new WrongRequestMethodException(
                'POST',
                WrongRequestMethodException::POST_METHOD
            );
        }

        if (!isset($this->aPost[$sKey])) {
            return '';
        }

        if ($sParam === self::NO_CLEAN) {
            return $this->aPost[$sKey];
        }

        $this->bStrip = $bStrip;
        $this->setType($this->aPost, $sKey, $sParam);

        return $this->cleanData($this->aPost, $sKey, $sParam);
    }

    /**
     * Get Request URI.
     *
     * @return string Gives REQUEST_URI without relative subfolder path and the left first slash removed.
     */
    public function requestUri()
    {
        $sUri = $this->getUri();

        // Remove relative subfolder path and the first left slash
        $sRequestUri = ltrim($sUri, PH7_SH);
        $sRelative = ltrim(PH7_RELATIVE, PH7_SH);

        return str_replace($sRelative, '', $sRequestUri);
    }

    /**
     * @return string The current URL.
     */
    public function currentUrl()
    {
        return htmlspecialchars(
            PH7_URL_PROT . PH7_DOMAIN . $this->getUri(),
            ENT_QUOTES
        );
    }

    /**
     * @return string The Previous Page.
     */
    public function previousPage()
    {
        return (new Browser)->getHttpReferer();
    }

    /**
     * @return string The name of the current controller.
     */
    public function currentController()
    {
        return str_replace(
            'controller',
            '',
            strtolower(Registry::getInstance()->controller)
        );
    }

    /**
     * @param string $sUrl
     *
     * @return string The correct pH7's URL.
     */
    public function pH7Url($sUrl)
    {
        return $this->isRelativeUrl($sUrl) ? PH7_URL_ROOT . $sUrl : $sUrl;
    }

    /**
     * Check is a request variable is valid.
     *
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sParam
     *
     * @return bool Returns TRUE if the type of the variable is valid, FALSE otherwise.
     */
    protected function validate(&$aType, $sKey, $sParam)
    {
        if (!empty($sParam) && !Secty\Validate\Validate::type($aType[$sKey], $sParam)) {
            return false;
        }

        return true;
    }

    /**
     * Set the type of a request variable.
     *
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sType A PHP Type: "bool", "int", "float", "string", "array", "object" or "null".
     *
     * @return void
     */
    protected function setType(&$aType, $sKey, $sType)
    {
        if (!empty($sType) && $sType !== self::ONLY_XSS_CLEAN) {
            settype($aType[$sKey], $sType);
        }
    }

    /**
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sParam Optional self::ONLY_XSS_CLEAN to delete only the XSS vulnerability.
     *
     * @return string
     */
    protected function cleanData(&$aType, $sKey, $sParam)
    {
        // Avoid to use escape() func, because it converts integer/float/boolean value into string type
        if ($this->isUnescapableType($aType[$sKey])) {
            return $aType[$sKey];
        }

        // For space and others in the address bar
        if ($this->getMethod() === self::METHOD_GET) {
            $aType[$sKey] = $this->cleanGetUrl($aType[$sKey]);
        }

        if (!empty($sParam) && $sParam === self::ONLY_XSS_CLEAN) {
            return (new Secty\Validate\Filter)->xssClean($aType[$sKey]);
        }

        return escape($aType[$sKey], $this->bStrip);
    }

    /**
     * Set the Request Variable.
     *
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey
     * @param string $sValue
     *
     * @return void
     */
    private function setRequestVar(&$aType, $sKey, $sValue)
    {
        $aType[$sKey] = $sValue;
    }

    /**
     * @param mixed $mValue
     *
     * @return bool
     */
    private function isUnescapableType($mValue)
    {
        return is_int($mValue) || is_float($mValue) || is_bool($mValue);
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    private function cleanGetUrl($sValue)
    {
        return str_replace(self::SPECIAL_CHARS, '', $sValue);
    }

    /**
     * Clear the CSRF token in the request variable name.
     *
     * @param array $aType Request variable type ($_GET, $_POST, $_COOKIE, $_REQUEST).
     * @param string $sKey The request variable to clean.
     *
     * @return string
     */
    private function clearCSRFToken(&$aType, $sKey)
    {
        return preg_replace(
            '#(\?|&)' . Secty\CSRF\Token::VAR_NAME . '\=[^/]+$#',
            '',
            $aType[$sKey]
        );
    }
}
