<?php
/**
 * @title            Browser Class
 * @desc             Useful Browser methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Framework\Navigation;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Server\Server;
use PH7\Framework\Str\Str;
use PH7\Framework\Translate\Lang;

/**
 * @internal In this class, there're some yoda conditions.
 */
class Browser
{
    private const FAVICON_GENERATOR_URL = 'https://www.google.com/s2/favicons?domain=';

    private const DEFAULT_BROWSER_HEX_CODES = [
        '#000',
        '#000000'
    ];

    /**
     * Detect the user's preferred language.
     *
     * @param bool $bFullLangCode If TRUE, returns the full lang code (e.g., en-us, en-gb, en-ie, en-au, fr-fr, fr-be, fr-ca, fr-ch, ...),
     *     otherwise returns the two letters of the client browser's language (e.g., en, it, fr, ru, ...).
     *
     * @return string Client's Language Code (in lowercase).
     */
    public function getLanguage(bool $bFullLangCode = false): string
    {
        $oStr = new Str;
        $sLang = explode(',', Server::getVar(Server::HTTP_ACCEPT_LANGUAGE))[0];

        $iFullLangCode = $bFullLangCode ? 5 : Lang::ISO_LANG_CODE_LENGTH;

        // The rtrim function is slightly faster than chop function
        return $oStr->escape($oStr->lower(substr(rtrim($sLang), 0, $iFullLangCode)));
    }

    /**
     * Active browser cache.
     *
     * @return Browser
     */
    public function cache(): self
    {
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600 * 24 * 30) . ' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: public'); // HTTP 1.0

        return $this;
    }

    /**
     * Prevent caching in the browser.
     */
    public function noCache(): self
    {
        $sNow = gmdate('D, d M Y H:i:s') . ' GMT';
        header('Expires: ' . $sNow);
        header('Last-Modified: ' . $sNow);
        unset($sNow);

        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache'); // HTTP 1.0

        return $this;
    }

    /**
     * Are we capable to receive gzipped data?
     *
     * @return string|bool Returns the encoding if it is accepted, false otherwise. Maybe additional check for Mac OS...
     */
    public function encoding()
    {
        if (headers_sent() || connection_aborted()) {
            return false;
        }

        $sEncoding = Server::getVar(Server::HTTP_ACCEPT_ENCODING);

        if (false !== strpos($sEncoding, 'x-gzip')) {
            return 'x-gzip';
        }

        if (false !== strpos($sEncoding, 'gzip')) {
            return 'gzip';
        }

        return false;
    }

    /**
     * Check if the user is from a mobile device or desktop.
     *
     * @return bool TRUE if mobile device, FALSE otherwise.
     */
    public function isMobile(): bool
    {
        if (null !== Server::getVar(Server::HTTP_X_WAP_PROFILE) ||
            null !== Server::getVar(Server::HTTP_PROFILE)
        ) {
            return true;
        }

        $sHttpAccept = Server::getVar(Server::HTTP_ACCEPT);
        if (null !== $sHttpAccept) {
            $sHttpAccept = strtolower($sHttpAccept);

            if (false !== strpos($sHttpAccept, 'wap')) {
                return true;
            }
        }

        $sUserAgent = $this->getUserAgent();
        if (null !== $sUserAgent) {
            // For most mobile/tablet browsers
            if (false !== strpos($sUserAgent, 'Mobile')) {
                return true;
            }

            // Mainly for (i)Phone
            if (false !== strpos($sUserAgent, 'Phone')) {
                return true;
            }

            // For Android
            if (false !== strpos($sUserAgent, 'Android')) {
                return true;
            }

            if (false !== strpos($sUserAgent, 'Opera Mini')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string|null The HTTP User Agent is it exists, otherwise the NULL value.
     */
    public function getUserAgent(): ?string
    {
        return Server::getVar(Server::HTTP_USER_AGENT);
    }

    /**
     * @return string|null The HTTP Referer is it exists, otherwise the NULL value.
     */
    public function getHttpReferer(): ?string
    {
        return Server::getVar(Server::HTTP_REFERER);
    }

    public function getIfModifiedSince(): ?string
    {
        $sIfModifiedSinceHttp = Server::getVar(Server::HTTP_IF_MODIFIED_SINCE);

        if (!empty($sIfModifiedSinceHttp)) {
            return substr($sIfModifiedSinceHttp, 0, 29);
        }

        return null;
    }

    public function isAjaxRequest(): bool
    {
        return array_key_exists(Server::HTTP_X_REQUESTED_WITH, Server::getVar());
    }

    public static function isDefaultBrowserHexCodeFound(string $sValue): bool
    {
        return in_array($sValue, self::DEFAULT_BROWSER_HEX_CODES, true);
    }

    /**
     * Get favicon from a URL.
     *
     * @param string $sUrl
     *
     * @return string The favicon image.
     */
    public static function favicon(string $sUrl): string
    {
        $sDomainName = Http::getHostName($sUrl);

        return static::FAVICON_GENERATOR_URL . $sDomainName;
    }
}
