<?php
/**
 * @title            Ip Class
 * @desc             Helper for the IP Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Ip
 */

namespace PH7\Framework\Ip;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Server\Server;

class Ip
{
    public const DEFAULT_IP = '127.0.0.1';

    /**
     * Get IP address.
     *
     * @param string|null $sIp Allows to specify another IP address than the client one.
     *
     * @return string IP address. If the IP format is invalid, returns the default local IP.
     */
    public static function get(?string $sIp = null): string
    {
        if ($sIp === null) {
            $sIp = self::getClientIp();
        }

        if (static::isPrivate($sIp)) {
            $sIp = static::DEFAULT_IP; // Avoid invalid local IP for GeoIp
        }

        return filter_var($sIp, FILTER_VALIDATE_IP) ? $sIp : static::DEFAULT_IP;
    }

    /**
     * Returns the API IP with the IP address.
     *
     * @param string|null $sIp IP address. Allows to specify a specific IP.
     *
     * @return string API URL with the IP address.
     */
    public static function api(?string $sIp = null): string
    {
        $sIp = $sIp === null ? static::get() : $sIp;

        return DbConfig::getSetting('ipApi') . $sIp;
    }

    /**
     * Check if it's a local machine IP or not.
     *
     * @param string $sIp The IP address.
     *
     * @return bool Returns TRUE if it is a private or invalid IP, FALSE otherwise.
     */
    public static function isPrivate(string $sIp): bool
    {
        return filter_var(
            $sIp,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) ? false : true;
    }

    /**
     * Returns the client IP address, only trusting proxy headers if the request comes from a trusted proxy.
     *
     * SECURITY NOTE FOR ADMINS/DEPLOYERS:
     *   - Only add IPs of infrastructure you fully control (e.g., your nginx, HAProxy, or CDN edge nodes) to $trustedProxies.
     *   - DO NOT add wildcards or public IPs you do not control, or you risk allowing attackers to spoof their IP via headers.
     *   - If unsure, leave only localhost entries (default) and do not trust proxy headers.
     *   - See https://cheatsheetseries.owasp.org/cheatsheets/Proxy_Header_Security_Cheat_Sheet.html for best practices.
     */
    private static function getClientIp(): string
    {
        $trustedProxies = [
            '127.0.0.1', '::1', // localhost
            '::ffff:127.0.0.1',
            // Add your reverse proxy IPs here (e.g., Cloudflare, nginx, etc)
        ];

        $remoteAddr = (string)Server::getVar(Server::REMOTE_ADDR, '');
        $isTrustedProxy = in_array($remoteAddr, $trustedProxies, true);

        $ipHeaderVars = $isTrustedProxy
            ? [
                'HTTP_CF_CONNECTING_IP',
                'HTTP_TRUE_CLIENT_IP',
                'HTTP_X_REAL_IP',
                Server::HTTP_CLIENT_IP,
                Server::HTTP_X_FORWARDED_FOR,
                Server::REMOTE_ADDR
            ]
            : [Server::REMOTE_ADDR];

        foreach ($ipHeaderVars as $header) {
            $ip = (string)Server::getVar($header, '');
            if ($ip === '') {
                continue;
            }
            $parsedIp = static::extractClientIp($ip);
            if ($parsedIp !== null) {
                return $parsedIp;
            }
        }
        return '';
    }

    private static function extractClientIp(string $sIpList): ?string
    {
        $aCandidates = preg_split('/\s*,\s*/', trim($sIpList));
        if (!is_array($aCandidates) || $aCandidates === []) {
            return null;
        }

        $sFirstValidIp = null;
        foreach ($aCandidates as $sCandidate) {
            if (!filter_var($sCandidate, FILTER_VALIDATE_IP)) {
                continue;
            }

            if (!static::isPrivate($sCandidate)) {
                return $sCandidate;
            }

            if ($sFirstValidIp === null) {
                $sFirstValidIp = $sCandidate;
            }
        }

        return $sFirstValidIp;
    }
}
