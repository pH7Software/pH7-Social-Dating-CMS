<?php
/**
 * @title            Header Url Class
 * @desc             Header URL methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Url
 * @version          1.2
 */

declare(strict_types=1);

namespace PH7\Framework\Url;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\JustHttp\StatusCode;

class Header
{
    /**
     * Allows a redirection URL respecting the HTTP status code for search engines friendly.
     *
     * @param string $sUrl Default NULL, so it's the current URL.
     * @param string $sMessage Default NULL, so no message.
     * @param string $sType Type of message: "Design::SUCCESS_TYPE", "Design::INFO_TYPE", "Design::WARNING_TYPE" or "Design::ERROR_TYPE"
     * @param int $iRedirectCode Optional. Default FOUND 302
     *
     * @return void
     */
    public static function redirect($sUrl = null, $sMessage = null, $sType = Design::SUCCESS_TYPE, int $iRedirectCode = StatusCode::FOUND)
    {
        Http::setHeadersByCode($iRedirectCode);

        $oHttpRequest = new HttpRequest;
        $sUrl = $sUrl !== null ? $sUrl : $oHttpRequest->currentUrlForHeader();
        $sUrl = $oHttpRequest->pH7Url($sUrl);
        if (!self::isSafeRedirectUrl($sUrl)) {
            $sUrl = PH7_URL_ROOT;
        }
        unset($oHttpRequest);

        if ($sMessage !== null) {
            (new Design)->setFlashMsg($sMessage, $sType);
        }

        header('Location: ' . $sUrl);
        exit;
    }

    private static function isSafeRedirectUrl(string $sUrl): bool
    {
        if (preg_match('/[\r\n]/', $sUrl)) {
            return false;
        }

        $sScheme = strtolower((string)parse_url($sUrl, PHP_URL_SCHEME));
        if (!in_array($sScheme, ['http', 'https'], true)) {
            return false;
        }

        $sHost = Http::getHostName($sUrl);
        $sCurrentHost = Http::getHostName(PH7_URL_ROOT);
        if (empty($sHost) || empty($sCurrentHost)) {
            return false;
        }

        return $sHost === $sCurrentHost;
    }

    /**
     * Gets the self URL.
     *
     * @return string The URL.
     */
    public static function selfUrl(): string
    {
        $sRequestUri = (string)($_SERVER['REQUEST_URI'] ?? PH7_SH);
        $sRequestUri = str_replace(["\r", "\n"], '', $sRequestUri);
        if (!str_starts_with($sRequestUri, PH7_SH)) {
            $sRequestUri = PH7_SH;
        }

        return rtrim(PH7_URL_PROT . PH7_DOMAIN, PH7_SH) . $sRequestUri;
    }
}
