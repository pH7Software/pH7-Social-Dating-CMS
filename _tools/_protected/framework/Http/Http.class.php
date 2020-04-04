<?php
/**
 * @title            Http Class
 * @desc             HTTP Management Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Http
 */

namespace PH7\Framework\Http;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Server\Server;
use Teapot\StatusCode;

class Http
{
    const STATUS_CODE = [
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
     * @param int $iStatus The "code" for the HTTP status.
     *
     * @return string|bool $iStatus Returns the "HTTP status code" if found, FALSE otherwise.
     */
    public static function getStatusCode($iStatus)
    {
        $iStatus = (int)$iStatus;

        return !empty(static::STATUS_CODE[$iStatus]) ? static::STATUS_CODE[$iStatus] : false;
    }

    /**
     * Retrieve the list with headers that are sent or to be sent.
     *
     * @return array
     */
    public static function getHeadersList()
    {
        return headers_list();
    }

    /**
     * Get domain name from a URL.
     *
     * @param string $sUrl
     *
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
     * @param string|array $mHeaders Headers to send.
     *
     * @throws Exception
     */
    public static function setHeaders($mHeaders)
    {
        // Header already sent
        if (static::isSent()) {
            throw new Exception('Headers were already sent.');
        }

        // Loop elements and set header
        foreach ((array)$mHeaders as $sHeader) {
            header((string)$sHeader);
        }
    }

    /**
     * Parse headers for a given status code.
     *
     * @param int $iCode The code to use, possible values are: 200, 301, 302, 304, 307, 400, 401, 403, 404, 410, 500, 501, ...
     *
     * @throws Exception
     */
    public static function setHeadersByCode($iCode = StatusCode::OK)
    {
        if (!static::getStatusCode($iCode)) {
            $iCode = StatusCode::OK;
        }

        static::setHeaders(static::getProtocol() . ' ' . static::getStatusCode($iCode));
    }

    /**
     * Set a HTTP Content Type.
     *
     * @param string $sType Example: "text/xml".
     *
     * @throws Exception
     */
    public static function setContentType($sType)
    {
        static::setHeaders('Content-Type: ' . $sType);
    }

    /**
     * Set the HTTP status code for the maintenance page.
     *
     * @param int $iMaintenanceTime Time site will be down for (in seconds).
     */
    public static function setMaintenanceCode($iMaintenanceTime)
    {
        header(static::getProtocol() . ' 503 Service Temporarily Unavailable');
        header('Retry-After: ' . $iMaintenanceTime);
    }

    /**
     * Required HTTP Authentification.
     *
     * @param string $sUsr
     * @param string $sPwd
     * @param string $sMsg
     *
     * @return bool TRUE if the authentication is correct, otherwise FALSE.
     */
    public static function requireAuth($sUsr, $sPwd, $sMsg = 'HTTP Basic Authentication')
    {
        $sAuthUsr = Server::getVar(Server::AUTH_USER);
        $sAuthPwd = Server::getVar(Server::AUTH_PW);

        if (!($sAuthUsr === $sUsr && $sAuthPwd === $sPwd)) {
            header(sprintf('WWW-Authenticate: Basic realm="%s"', $sMsg));
            static::setHeadersByCode(StatusCode::UNAUTHORIZED);
            echo t('You must enter a valid login ID and password to access this resource.') . "\n";
            exit(false);
        }

        return true;
    }

    /**
     * Check if HTTP SSL is used.
     *
     * @internal In this method, there are some yoda conditions.
     *
     * @return bool
     */
    public static function isSsl()
    {
        $sHttps = strtolower(Server::getVar(Server::HTTPS));

        if (null !== $sHttps) {
            $sHttps = strtolower($sHttps);

            if ('on' == $sHttps) {
                return true;
            } elseif ('1' == $sHttps) {
                return true;
            } else {
                $iPort = Server::getVar(Server::SERVER_PORT);

                if ('443' == $iPort) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the URL is relative.
     *
     * @param string $sUrl
     *
     * @return bool
     */
    public function isRelativeUrl($sUrl)
    {
        return stristr($sUrl, 'http') === false;
    }

    /**
     * Detects if the URL is a subdomain.
     *
     * @param string $sUrl URL
     *
     * @return bool
     */
    public function detectSubdomain($sUrl)
    {
        return $this->getSubdomain($sUrl) !== null;
    }

    /**
     * Get the subdomain in a URL address.
     *
     * @param string $sUrl
     *
     * @return string|null Returns the "subdomain" in the URL address if he has found a subdomain otherwise "null".
     */
    public function getSubdomain($sUrl)
    {
        $sHost = static::getHostName($sUrl);
        $aDomainParts = explode('.', $sHost);

        return count($aDomainParts) > 2 ? $aDomainParts[0] : null;
    }

    /**
     * @return string|null The Request Method or the NULL value.
     */
    public function getRequestMethod()
    {
        return Server::getVar(Server::REQUEST_METHOD);
    }

    /**
     * Gives the requested URI with URL-decoded
     * to avoid issues with special I18N characters in URL.
     *
     * @return string
     */
    public function getRequestUri()
    {
        $sRequestUri = Server::getVar(Server::REQUEST_URI);

        return rawurldecode($sRequestUri);
    }

    /**
     * @return string|null The Query String or the NULL value.
     */
    public function getQueryString()
    {
        return Server::getVar(Server::QUERY_STRING);
    }

    /**
     * @return string The HTTP server protocol.
     */
    public static function getProtocol()
    {
        return Server::getVar(Server::SERVER_PROTOCOL);
    }

    /**
     * Checks if any headers were already sent.
     *
     * @return bool TRUE if the headers were sent, FALSE if not.
     */
    final private static function isSent()
    {
        return headers_sent();
    }
}
