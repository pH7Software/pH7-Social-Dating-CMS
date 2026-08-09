<?php

namespace PH7\Framework\Security\Validate;

defined('PH7') or exit('Restricted access');

/**
 * NOTE: This script has been modified by Pierre-Henry Soria.
 *
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 *
 * @see        http://codeigniter.com
 * @since        Version 1.0
 *
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Security Class.
 *
 * @category    Security
 *
 * @author        ExpressionEngine Dev Team
 *
 * @see        http://codeigniter.com/user_guide/libraries/security.html
 */
class Filter
{
    /**
     * Random Hash for protecting URLs.
     *
     * @var string
     */
    protected $_xss_hash = '';
    /**
     * Random Hash for Cross Site Request Forgery Protection Cookie.
     *
     * @var string
     */
    protected $_csrf_hash = '';
    /**
     * Expiration time for Cross Site Request Forgery Protection Cookie
     * Defaults to two hours (in seconds).
     *
     * @var int
     */
    protected $_csrf_expire = 7200;
    /**
     * Token name for Cross Site Request Forgery Protection Cookie.
     *
     * @var string
     */
    protected $_csrf_token_name = 'ci_csrf_token';
    /**
     * Cookie name for Cross Site Request Forgery Protection Cookie.
     *
     * @var string
     */
    protected $_csrf_cookie_name = 'ci_csrf_token';
    /**
     * List of never allowed strings.
     *
     * @var array
     */
    protected $_never_allowed_str = [
        'document.cookie' => '', // '' OR [removed]
        'document.write' => '', // '' OR [removed]
        '.parentNode' => '', // '' OR [removed]
        '.innerHTML' => '', // '' OR [removed]
        'window.location' => '', // '' OR [removed]
        '-moz-binding' => '', // '' OR [removed]
        '<!--' => '&lt;!--',
        '-->' => '--&gt;',
        '<![CDATA[' => '&lt;![CDATA[',
        '<comment>' => '&lt;comment&gt;'
    ];

    /* never allowed, regex replacement */
    /**
     * List of never allowed regex replacement.
     *
     * @var array
     */
    protected $_never_allowed_regex = [
        "javascript\s*:" => '', // '' OR [removed]
        "expression\s*(\(|&\#40;)" => '[removed]', // CSS and IE
        "vbscript\s*:" => '[removed]', // IE, surprise!
        "Redirect\s+302" => '[removed]'
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        // CSRF config
        foreach (['csrf_expire', 'csrf_token_name', 'csrf_cookie_name'] as $key) {
            if (false !== ($val = config_item($key))) {
                $this->{'_' . $key} = $val;
            }
        }

        // Append application specific cookie prefix
        if (config_item('cookie_prefix')) {
            $this->_csrf_cookie_name = config_item('cookie_prefix') . $this->_csrf_cookie_name;
        }

        // Set the CSRF hash
        $this->_csrf_set_hash();

        // log_message('debug', "Security Class Initialized");
    }

    // --------------------------------------------------------------------

    /**
     * Verify Cross Site Request Forgery Protection.
     *
     * @return object
     */
    public function csrf_verify()
    {
        // If no POST data exists we will set the CSRF cookie
        if (count($_POST) === 0) {
            return $this->csrf_set_cookie();
        }

        // Do the tokens exist in both the _POST and _COOKIE arrays?
        if (!isset($_POST[$this->_csrf_token_name])
            or !isset($_COOKIE[$this->_csrf_cookie_name])) {
            $this->csrf_show_error();
        }

        // Do the tokens match?
        if ($_POST[$this->_csrf_token_name] !== $_COOKIE[$this->_csrf_cookie_name]) {
            $this->csrf_show_error();
        }

        // We kill this since we're done and we don't want to
        // polute the _POST array
        unset($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name]);

        // Nothing should last forever

        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        // log_message('debug', "CSRF token verified ");

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Cross Site Request Forgery Protection Cookie.
     *
     * @return object
     */
    public function csrf_set_cookie()
    {
        $expire = time() + $this->_csrf_expire;
        $secure_cookie = (config_item('cookie_secure') === true) ? 1 : 0;

        if ($secure_cookie) {
            $req = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : false;

            if (!$req or $req === 'off') {
                return false;
            }
        }

        setcookie($this->_csrf_cookie_name, $this->_csrf_hash, $expire, config_item('cookie_path'), config_item('cookie_domain'), $secure_cookie);

        // log_message('debug', "CRSF cookie Set");

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Show CSRF Error.
     *
     * @return void
     */
    public function csrf_show_error()
    {
        $sErrorMsg = 'The action you have requested is not allowed.';

        if (function_exists('show_error')) {
            show_error($sErrorMsg);

            return;
        }

        throw new \RuntimeException($sErrorMsg);
    }

    // --------------------------------------------------------------------

    /**
     * Get CSRF Hash.
     *
     * Getter Method
     *
     * @return string self::_csrf_hash
     */
    public function get_csrf_hash()
    {
        return $this->_csrf_hash;
    }

    // --------------------------------------------------------------------

    /**
     * Get CSRF Token Name.
     *
     * Getter Method
     *
     * @return string self::csrf_token_name
     */
    public function get_csrf_token_name()
    {
        return $this->_csrf_token_name;
    }

    // --------------------------------------------------------------------

    /**
     * Clean rich text with the maintained allow-list sanitizer.
     *
     * This replaces the legacy CodeIgniter-derived implementation.
     *
     * @return array|bool|string
     */
    public function xssClean($mValue, $bIsImage = false)
    {
        $mCleanValue = (new Purifier())->xssClean($mValue);

        return $bIsImage ? $mCleanValue === $mValue : $mCleanValue;
    }

    // --------------------------------------------------------------------

    /**
     * Random Hash for protecting URLs.
     *
     * @return string
     */
    public function xss_hash()
    {
        if ($this->_xss_hash === '') {
            $this->_xss_hash = bin2hex(random_bytes(16));
        }

        return $this->_xss_hash;
    }

    // --------------------------------------------------------------------

    /**
     * HTML Entities Decode.
     *
     * This function is a replacement for html_entity_decode()
     *
     * The reason we are not using html_entity_decode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly.  html_entity_decode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     *
     * @param    string
     * @param    string
     *
     * @return string
     */
    public function entity_decode($str, $charset = 'UTF-8')
    {
        if (stristr($str, '&') === false) {
            return $str;
        }

        $str = html_entity_decode($str, ENT_COMPAT, $charset);
        $str = preg_replace_callback(
            '~&#x(0*[0-9a-f]{2,5})~i',
            static function (array $aMatch): string {
                return chr(hexdec($aMatch[1]));
            },
            $str
        ) ?: $str;

        return preg_replace_callback(
            '~&#([0-9]{2,4})~',
            static function (array $aMatch): string {
                return chr((int)$aMatch[1]);
            },
            $str
        ) ?: $str;
    }

    // --------------------------------------------------------------------

    /**
     * Filename Security.
     *
     * @param    string
     * @param     bool
     *
     * @return string
     */
    public function sanitize_filename($str, $relative_path = false)
    {
        $bad = [
            '../',
            '<!--',
            '-->',
            '<',
            '>',
            "'",
            '"',
            '&',
            '$',
            '#',
            '{',
            '}',
            '[',
            ']',
            '=',
            ';',
            '?',
            '%20',
            '%22',
            '%3c',        // <
            '%253c',    // <
            '%3e',        // >
            '%0e',        // >
            '%28',        // (
            '%29',        // )
            '%2528',    // (
            '%26',        // &
            '%24',        // $
            '%3f',        // ?
            '%3b',        // ;
            '%3d'        // =
        ];

        if (!$relative_path) {
            $bad[] = './';
            $bad[] = '/';
        }

        $str = remove_invisible_characters($str, false);

        return stripslashes(str_replace($bad, '', $str));
    }

    // ----------------------------------------------------------------

    /**
     * Compact Exploded Words.
     *
     * Callback function for xssClean() to remove whitespace from
     * things like j a v a s c r i p t
     *
     * @param    type
     *
     * @return type
     */
    protected function _compact_exploded_words($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
    }

    // --------------------------------------------------------------------

    /*
     * Remove Evil HTML Attributes (like evenhandlers and style)
     *
     * It removes the evil attribute and either:
     *     - Everything up until a space
     *        For example, everything between the pipes:
     *        <a |style=document.write('hello');alert('world');| class=link>
     *     - Everything inside the quotes
     *        For example, everything between the pipes:
     *        <a |style="document.write('hello'); alert('world');"| class="link">
     *
     * @param string $str The string to check
     * @param boolean $is_image TRUE if this is an image
     * @return string The string with the evil attributes removed
     */
    protected function _remove_evil_attributes($str, $is_image)
    {
        $evil_attributes = ['on\w*', 'style', 'xmlns', 'formaction'];

        if ($is_image === true) {
            /*
             * Adobe Photoshop puts XML metadata into JFIF images,
             * including namespacing, so we have to allow this for images.
             */
            unset($evil_attributes[array_search('xmlns', $evil_attributes, true)]);
        }

        do {
            $count = 0;
            $attribs = [];

            // find occurrences of illegal attribute strings without quotes
            preg_match_all('/(' . implode('|', $evil_attributes) . ")\s*=\s*([^\s]*)/is", $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            preg_match_all('/(' . implode('|', $evil_attributes) . ")\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is", $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // replace illegal attribute strings that are inside an html tag
            if (count($attribs) > 0) {
                $str = preg_replace("/<(\/?[^><]+?)([^A-Za-z\-])(" . implode('|', $attribs) . ")([\s><])([><]*)/i", '<$1$2$4$5', $str, -1, $count);
            }
        } while ($count);

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * Sanitize Naughty HTML.
     *
     * Callback function for xssClean() to remove naughty HTML elements
     *
     * @param    array
     *
     * @return string
     */
    protected function _sanitize_naughty_html($matches)
    {
        // encode opening brace
        $str = '&lt;' . $matches[1] . $matches[2] . $matches[3];

        // encode captured opening or closing brace to prevent recursive vectors
        $str .= str_replace(['>', '<'], ['&gt;', '&lt;'],
            $matches[4]);

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * JS Link Removal.
     *
     * Callback function for xssClean() to sanitize links
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on link-heavy strings
     *
     * @param    array
     *
     * @return string
     */
    protected function _js_link_removal($match)
    {
        $attributes = $this->_filter_attributes(str_replace(['<', '>'], '', $match[1]));

        return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", '', $attributes), $match[0]);
    }

    // --------------------------------------------------------------------

    /**
     * JS Image Removal.
     *
     * Callback function for xssClean() to sanitize image tags
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings
     *
     * @param    array
     *
     * @return string
     */
    protected function _js_img_removal($match)
    {
        $attributes = $this->_filter_attributes(str_replace(['<', '>'], '', $match[1]));

        return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", '', $attributes), $match[0]);
    }

    // --------------------------------------------------------------------

    /**
     * Attribute Conversion.
     *
     * Used as a callback for XSS Clean
     *
     * @param    array
     *
     * @return string
     */
    protected function _convert_attribute($match)
    {
        return str_replace(['>', '<', '\\'], ['&gt;', '&lt;', '\\\\'], $match[0]);
    }

    // --------------------------------------------------------------------

    /**
     * Filter Attributes.
     *
     * Filters tag attributes for consistency and safety
     *
     * @param    string
     *
     * @return string
     */
    protected function _filter_attributes($str)
    {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }

        return $out;
    }

    // --------------------------------------------------------------------

    /**
     * HTML Entity Decode Callback.
     *
     * Used as a callback for XSS Clean
     *
     * @param    array
     *
     * @return string
     */
    protected function _decode_entity($match)
    {
        return $this->entity_decode($match[0], strtoupper(config_item('charset')));
    }

    // --------------------------------------------------------------------

    /**
     * Validate URL entities.
     *
     * Called by xssClean()
     *
     * @param     string
     *
     * @return string
     */
    protected function _validate_entities($str)
    {
        /*
         * Protect GET variables in URLs
         */

        // 901119URL5918AMP18930PROTECT8198

        $str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $this->xss_hash() . '\\1=\\2', $str);

        /*
         * Validate standard character entities
         *
         * Add a semicolon if missing.  We do this to enable
         * the conversion of entities to ASCII later.
         *
         */
        $str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', '\\1;\\2', $str);

        /*
         * Validate UTF16 two byte encoding (x00)
         *
         * Just as above, adds a semicolon if missing.
         *
         */
        $str = preg_replace('#(&\#x?)([0-9A-F]+);?#i', '\\1\\2;', $str);

        /*
         * Un-Protect GET variables in URLs
         */
        $str = str_replace($this->xss_hash(), '&', $str);

        return $str;
    }

    // ----------------------------------------------------------------------

    /**
     * Do Never Allowed.
     *
     * A utility function for xssClean()
     *
     * @param     string
     *
     * @return string
     */
    protected function _do_never_allowed($str)
    {
        foreach ($this->_never_allowed_str as $key => $val) {
            $str = str_replace($key, $val, $str);
        }

        foreach ($this->_never_allowed_regex as $key => $val) {
            $str = preg_replace('#' . $key . '#i', $val, $str);
        }

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * Set Cross Site Request Forgery Protection Cookie.
     *
     * @return string
     */
    protected function _csrf_set_hash()
    {
        if ($this->_csrf_hash === '') {
            // If the cookie exists we will use it's value.
            // We don't necessarily want to regenerate it with
            // each page load since a page could contain embedded
            // sub-pages causing this feature to fail
            if (isset($_COOKIE[$this->_csrf_cookie_name])
                && $_COOKIE[$this->_csrf_cookie_name] !== '') {
                return $this->_csrf_hash = $_COOKIE[$this->_csrf_cookie_name];
            }

            return $this->_csrf_hash = bin2hex(random_bytes(16));
        }

        return $this->_csrf_hash;
    }
}

// END Security Class

/**
 * Loads the main config.php file.
 *
 * This function lets us grab the config file even if the Config class
 * hasn't been instantiated yet
 *
 * @return array
 */
function &get_config($replace = [])
{
    static $_config;

    if (isset($_config)) {
        return $_config[0];
    }

    require __DIR__ . '/config_filter.inc.php';

    // Does the $config array exist in the file?
    if (!isset($config) or !is_array($config)) {
        exit('Your config file does not appear to be formatted correctly.');
    }

    // Are any values being dynamically replaced?
    if (count($replace) > 0) {
        foreach ($replace as $key => $val) {
            if (isset($config[$key])) {
                $config[$key] = $val;
            }
        }
    }

    $_config[0] = &$config;

    return $_config[0];
}

/**
 * Returns the specified config item.
 */
function config_item($item)
{
    static $_config_item = [];

    if (!isset($_config_item[$item])) {
        $config = &get_config();

        if (!isset($config[$item])) {
            return false;
        }
        $_config_item[$item] = $config[$item];
    }

    return $_config_item[$item];
}

// --------------------------------------------------------------------

/**
 * Remove Invisible Characters.
 *
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 *
 * @param    string
 *
 * @return string
 */
function remove_invisible_characters($str, $url_encoded = true)
{
    $non_displayables = [];

    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)

    if ($url_encoded) {
        $non_displayables[] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/';    // url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

    do {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    } while ($count);

    return $str;
}

/* End of file Security.php */
/* Location: ./system/libraries/Security.php */
