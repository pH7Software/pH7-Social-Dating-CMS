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
        $sUrl = $sUrl !== null ? $sUrl : $oHttpRequest->currentUrl();
        $sUrl = $oHttpRequest->pH7Url($sUrl);
        unset($oHttpRequest);

        if ($sMessage !== null) {
            (new Design)->setFlashMsg($sMessage, $sType);
        }

        header('Location: ' . $sUrl);
        exit;
    }

    /**
     * Gets the self URL.
     *
     * @return string The URL.
     */
    public static function selfUrl(): string
    {
        $sHttps = strtolower((string)($_SERVER['HTTPS'] ?? ''));
        $sForwardedProto = strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $bSecure = in_array($sHttps, ['on', '1'], true) || $sForwardedProto === 'https';

        $sServerProtocol = strtolower((string)($_SERVER['SERVER_PROTOCOL'] ?? 'http/1.1'));
        $iProtocolSeparatorPos = strpos($sServerProtocol, PH7_SH);
        $sScheme = $iProtocolSeparatorPos !== false ? substr($sServerProtocol, 0, $iProtocolSeparatorPos) : 'http';
        $sProtocol = $sScheme . ($bSecure ? 's' : '');

        $sHost = (string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
        $iPort = (int)($_SERVER['SERVER_PORT'] ?? 0);
        $sRequestUri = (string)($_SERVER['REQUEST_URI'] ?? PH7_SH);

        $bIsStandardPort = ($bSecure && $iPort === 443) || (!$bSecure && $iPort === 80) || $iPort === 0;
        $sPort = $bIsStandardPort ? '' : ':' . $iPort;

        return $sProtocol . '://' . $sHost . $sPort . $sRequestUri;
    }
}
