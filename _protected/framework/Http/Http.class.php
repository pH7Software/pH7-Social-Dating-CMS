<?php
/**
 * @title            Http Class
 * @desc             HTTP Management Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Http
 * @version          1.0
 */

namespace PH7\Framework\Http;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Server\Server;

class Http
{

    /**
     * The HTTP response codes and messages.
     *
     * @staticvar array $aStatusCodes
     */
    protected static $aStatusCodes = [
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        102 => '102 Processing',
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        207 => '207 Multi-Status',
        208 => '208 Already Reported',
        226 => '226 IM Used',
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 Switch Proxy',
        307 => '307 Temporary Redirect',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Time-out',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Large',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested range not satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        424 => '424 Method Failure',
        425 => '425 Unordered Collection',
        426 => '426 Upgrade Required',
        428 => '428 Precondition Required',
        429 => '429 Too Many Requests',
        431 => '431 Request Header Fields Too Large',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Time-out',
        505 => '505 HTTP Version Unsupported',
        506 => '506 Variant Also Negotiates',
        507 => '507 Insufficient Storage',
        508 => '508 Loop Detected',
        510 => '510 Not Extended',
        511 => '511 Network Authentication Required',
        598 => '598 Network read timeout error',
        599 => '599 Network connect timeout error'
    ];


    /**
     * @static
     * @param integer $iStatus The "code" for the HTTP status
     * @return mixed (string | boolean) $iStatus Returns the "HTTP status code" if found otherwise returns "false"
     */
    public static function getStatusCodes($iStatus)
    {
        $iStatus = (int) $iStatus;
        return (!empty(static::$aStatusCodes[$iStatus])) ? $iStatus : false;
    }

    /**
     * Retrieve the list with headers that are sent or to be sent.
     *
     * @static
     * @return array
     */
    public static function getHeadersList()
    {
        return headers_list();
    }

    /**
     * Get domain name from a URL.
     *
     * @static
     * @param string $sUrl
     * @return string $sUrl Returns the URL to lower case and without the www. if present in the URL.
     */
    public static function getHostName($sUrl)
    {
        $sUrl = str_ireplace('www.', '', $sUrl);
        $sHost = parse_url($sUrl, PHP_URL_HOST);
        return $sHost;
    }

    /**
     * Set one or multiple headers.
     *
     * @static
     * @param mixed (string | array) $mHeaders Headers to send.
     * @throws \PH7\Framework\Http\Exception
     */
    public static function setHeaders($mHeaders)
    {
        // Header already sent
        if (static::_isSent())
            throw new Exception('Headers were already sent.');

        // Loop elements and set header
        foreach ((array) $mHeaders as $sHeader)
            header((string) $sHeader);
    }

    /**
     * Parse headers for a given status code.
     *
     * @static
     * @param int[optional] $iCode The code to use, possible values are: 200, 301, 302, 304, 307, 400, 401, 403, 404, 410, 500, 501, ... Default: 200
     */
    public static function setHeadersByCode($iCode = 200)
    {
        if (!static::getStatusCodes($iCode))
            $iCode = 200;

        // Set header
        static::setHeaders(static::getProtocol() . ' ' . static::getStatusCodes($iCode));
    }

    /**
     * Set a HTTP Content Type.
     *
     * @static
     * @param string $sType Example: "text/xml".
     * @return void
     */
    public static function setContentType($sType)
    {
        static::setHeaders('Content-Type: ' . $sType);
    }

    /**
     * Set the HTTP status codes for the maintenance page.
     *
     * @static
     * @param integer $iMaintenanceTime Time site will be down for (in seconds).
     * @return void
     */
    public static function setMaintenanceCodes($iMaintenanceTime)
    {
        header(static::getProtocol() . ' 503 Service Temporarily Unavailable');
        header('Retry-After: ' . $iMaintenanceTime);
    }

    /**
     * Required HTTP Authentification.
     *
     * @static
     * @param string $sUsr
     * @param string $sPwd
     * @return boolean TRUE if the authentication is correct, otherwise FALSE.
     */
    public static function requireAuth($sUsr, $sPwd)
    {
        $sAuthUsr = Server::getVar(Server::AUTH_USER);
        $sAuthPwd = Server::getVar(Server::AUTH_PW);

        if (!($sAuthUsr == $sUsr && $sAuthPwd == $sPwd))
        {
            header('WWW-Authenticate: Basic realm="HTTP Basic Authentication"');
            static::setHeadersByCode(401);
            echo t('You must enter a valid login ID and password to access this resource.') . "\n";
            exit(false);
        }
        else
            return true;
    }

    /**
     * Check if HTTP SSL is used.
     *
     * @static
     * @internal In this method, there are some yoda conditions.
     * @return boolean
     */
    public static function isSsl()
    {
        $sHttps = strtolower(Server::getVar(Server::HTTPS));

        if (null !== $sHttps)
        {
             $sHttps = strtolower($sHttps);

             if ('on' == $sHttps)
                return true;
             elseif ('1' == $sHttps)
                return true;
             else
             {
                 $iPort = Server::getVar(Server::SERVER_PORT);
                 if ('443' == $iPort)
                    return true;
             }
        }
        return false;
    }

    /**
     * Check if the URL is relative.
     *
     * @param string $sUrl
     * @return boolean
     */
    public function isRelativeUrl($sUrl)
    {
        return (0 !== stripos($sUrl, 'http'));
    }

    /**
     * Detects if the URL is a subdomain.
     *
     * @param string $sUrl URL
     * @return boolean
     */
    public function detectSubdomain($sUrl)
    {
        return ($this->getSubdomain($sUrl) !== null) ? true : false;
    }

    /**
     * Get the subdomain in a URL address.
     *
     * @param string $sUrl
     * @return mixed (string | null) Returns the "subdomain" in the URL address if he has found a subdomain otherwise "null".
     */
    public function getSubdomain($sUrl)
    {
        $sHost = static::getHostName($sUrl);
        $aDomainParts = explode('.', $sHost);
        return (count($aDomainParts) > 2) ? $aDomainParts[0] : null;
    }

    /**
     * @return mixed (string | null) The Request Method or the NULL value.
     */
    public function getRequestMethod()
    {
        return Server::getVar(Server::REQUEST_METHOD);
    }

    /**
     * @return string Request URI.
     */
    public function getRequestUri()
    {
        return Server::getVar(Server::REQUEST_URI);
    }

    /**
     * @return mixed (string | null) The Query String or the NULL value.
     */
    public function getQueryString()
    {
        return Server::getVar(Server::QUERY_STRING);
    }

    /**
     * @static
     * @return string The HTTP server protocol.
     */
     public static function getProtocol()
     {
         return Server::getVar(Server::SERVER_PROTOCOL);
     }

    /**
     * Checks if any headers were already sent.
     *
     * @access private
     * @static
     * @return boolean TRUE if the headers were sent, FALSE if not.
     */
    final private static function _isSent()
    {
        return headers_sent();
    }

}
