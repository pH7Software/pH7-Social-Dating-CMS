<?php

/**
 * @title          Server Class
 *
 * @desc           This class is used to manage settings of the web server and can simulate a server secure and reliable.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Framework\Server;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Core\Kernel;

use function PH7\is_internet;

final class Server
{
    public const SERVER_PORT = 'SERVER_PORT';
    public const SERVER_PROTOCOL = 'SERVER_PROTOCOL';
    public const SERVER_NAME = 'SERVER_NAME';
    public const SERVER_ADDR = 'SERVER_ADDR';
    public const LOCAL_ADDR = 'LOCAL_ADDR';
    public const HTTPS = 'HTTPS';
    public const HTTP_HOST = 'HTTP_HOST';
    public const HTTP_X_FORWARDED_HOST = 'HTTP_X_FORWARDED_HOST';
    public const REMOTE_ADDR = 'REMOTE_ADDR';
    public const HTTP_CLIENT_IP = 'HTTP_CLIENT_IP';
    public const HTTP_X_FORWARDED_FOR = 'HTTP_X_FORWARDED_FOR';
    public const AUTH_USER = 'PHP_AUTH_USER';
    public const AUTH_PW = 'PHP_AUTH_PW';
    public const CURRENT_FILE = 'PHP_SELF';
    public const REQUEST_METHOD = 'REQUEST_METHOD';
    public const REQUEST_URI = 'REQUEST_URI';
    public const QUERY_STRING = 'QUERY_STRING';
    public const HTTP_ACCEPT = 'HTTP_ACCEPT';
    public const HTTP_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE';
    public const HTTP_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING';
    public const HTTP_X_WAP_PROFILE = 'HTTP_X_WAP_PROFILE';
    public const HTTP_PROFILE = 'HTTP_PROFILE';
    public const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    public const HTTP_REFERER = 'HTTP_REFERER';
    public const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
    public const HTTP_IF_MODIFIED_SINCE = 'HTTP_IF_MODIFIED_SINCE';

    public const LOCAL_IP = '127.0.0.1';
    public const LOCAL_IPV6 = '::1';
    public const LOCAL_HOSTNAME = 'localhost';

    public const UNIX_OS = [
        'UNIX',
        'LINUX',
        'FREEBSD',
        'OPENBSD'
    ];

    public function __construct()
    {
        // Don't disclose version/build details (avoids easy fingerprinting of known CVEs).
        header('Server: ' . Kernel::SOFTWARE_SERVER_NAME);
        header('X-Powered-By: ' . Kernel::SOFTWARE_TECHNOLOGY_NAME);

        self::sendSecurityHeaders();
    }

    /**
     * Baseline security headers for every response (OWASP Secure Headers recommendations).
     */
    public static function sendSecurityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(self), geolocation=(self)');

        /*
         * Report-Only on purpose: nothing is blocked, violations only show in the browser console,
         * so site owners can discover what their install actually loads before the policy is enforced
         * (templates still emit inline scripts; nonces are planned with the Bootstrap 5 template pass).
         */
        header(
            'Content-Security-Policy-Report-Only: ' .
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: blob: https:; " .
            "font-src 'self' data:; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "frame-ancestors 'self'"
        );

        if (self::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    /**
     * Check to see if we are on a Windows server.
     *
     * @return bool TRUE if windows, FALSE if not
     */
    public static function isWindows(): bool
    {
        return 0 === stripos(PHP_OS, 'WIN');
    }

    /**
     * See if we are on a Unix server...?
     *
     * @return bool TRUE if Unix, FALSE if not
     */
    public static function isUnix(): bool
    {
        $sOS = strtoupper(PHP_OS);

        return in_array($sOS, self::UNIX_OS, true);
    }

    /**
     * Check to see if we are on a Mac OS server.
     *
     * @return bool TRUE if windows, FALSE if not
     */
    public static function isMac(): bool
    {
        return 0 === stripos(PHP_OS, 'MAC');
    }

    /**
     * Get the IP address of server.
     *
     * @internal we use LOCAL_ADDR variable for compatibility with Windows servers
     *
     * @return string|null IP address if found. NULL otherwise.
     */
    public static function getIp(): ?string
    {
        return self::getVar(
            self::SERVER_ADDR,
            self::getVar(self::LOCAL_ADDR, gethostbyname(self::getName()))
        );
    }

    /**
     * Retrieve a member of the $_SERVER super global.
     *
     * @param string|null $sKey    if NULL, returns the entire $_SERVER variable
     * @param string|null $sDefVal a default value to use if server key is not found
     *
     * @return string|array|null
     */
    public static function getVar($sKey = null, $sDefVal = null)
    {
        if (null === $sKey) {
            return $_SERVER;
        }

        return !empty($_SERVER[$sKey]) ? htmlspecialchars((string)$_SERVER[$sKey], ENT_QUOTES) : $sDefVal;
    }

    /**
     * Get the server name.
     *
     * @return string|null the name of the server host if exists, NULL otherwise
     */
    public static function getName(): ?string
    {
        return self::getVar(self::SERVER_NAME);
    }

    /**
     * Check if the server is in local.
     *
     * @return bool TRUE if it is in local mode, FALSE if not
     */
    public static function isLocalHost(): bool
    {
        $aLocalHosts = [self::LOCAL_HOSTNAME, self::LOCAL_IP, self::LOCAL_IPV6];
        $sServerName = self::normalizeHost(self::getName());

        return in_array($sServerName, $aLocalHosts, true);
    }

    /**
     * Check if Apache's mod_rewrite is installed.
     */
    public static function isRewriteMod(): bool
    {
        return strtolower((string)self::getVar('HTTP_MOD_REWRITE', '')) === 'on';
    }

    public static function cachedIsRewriteMod(): bool
    {
        $oCache = (new Cache())->start(
            'str/server',
            'isRewriteModStatus',
            86400
        );

        if (!$bIsEnabled = $oCache->get()) {
            $bIsEnabled = self::isRewriteMod();
            $oCache->put($bIsEnabled);
        }

        return $bIsEnabled;
    }

    /**
     * Alias method of the checkInternetConnection() function (located in ~/_protected/app/includes/helpers/misc.php).
     *
     * @return bool returns TRUE if the Internet connection is enabled, FALSE otherwise
     */
    public static function checkInternetConnection(): bool
    {
        return is_internet();
    }

    public static function isHttps(): bool
    {
        return substr(PH7_URL_PROT, 0, 5) === 'https';
    }

    /**
     * Return an explicitly configured parent cookie domain, or an empty string
     * for host-only cookies. The configured domain must contain the server name.
     */
    public static function getCookieDomain(): string
    {
        $mConfiguredDomain = getenv('PH7_COOKIE_DOMAIN');
        if (!is_string($mConfiguredDomain)) {
            return '';
        }

        $sCookieDomain = strtolower(ltrim(trim($mConfiguredDomain), '.'));
        $sCanonicalHost = defined('PH7_DOMAIN') ? PH7_DOMAIN : self::getName();
        $sServerName = self::normalizeHost($sCanonicalHost);
        if (
            preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/D', $sCookieDomain) !== 1 ||
            ($sServerName !== $sCookieDomain && !str_ends_with($sServerName, '.' . $sCookieDomain))
        ) {
            return '';
        }

        return '.' . $sCookieDomain;
    }

    private static function normalizeHost(?string $sHost): string
    {
        $sHost = strtolower(trim((string)$sHost));
        if ($sHost === '') {
            return '';
        }

        if (strpos($sHost, '[') === 0) {
            $iClosingBracketPos = strpos($sHost, ']');
            if ($iClosingBracketPos !== false) {
                $sHost = substr($sHost, 0, $iClosingBracketPos + 1);
            }
        } elseif (substr_count($sHost, ':') === 1) {
            $sHost = (string)preg_replace('/:\d+$/', '', $sHost);
        }

        return trim($sHost, '[]');
    }
}
