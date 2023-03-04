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
     * @param int $iRedirectCode Optional. Default MOVED_PERMANENTLY 301
     *
     * @return void
     */
    public static function redirect($sUrl = null, $sMessage = null, $sType = Design::SUCCESS_TYPE, int $iRedirectCode = StatusCode::MOVED_PERMANENTLY)
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
        $sSecure = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') ? 's' : '';
        $sServerProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
        $sProtocol = substr($sServerProtocol, 0, strpos($sServerProtocol, PH7_SH)) . $sSecure;

        // @var mixed $mPort (null or integer)
        $mPort = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':' . $_SERVER['SERVER_PORT']);

        return $sProtocol . '://' . $_SERVER['SERVER_NAME'] . $mPort . $_SERVER['REQUEST_URI'];
    }
}
