<?php
/**
 * @title            Header Url Class
 * @desc             Header URL methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Url
 * @version          1.2
 */

namespace PH7\Framework\Url;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Layout\Html\Design,
PH7\Framework\Http\Http,
PH7\Framework\Mvc\Request\Http as HttpRequest;

class Header
{

    /**
     * Allows a redirection URL respecting the HTTP status code for search engines friendly.
     *
     * @static
     * @param string $sUrl Default NULL, so it's the current URL.
     * @param string $sMessage Default NULL, so no message.
     * @param string $sType Type of message: "Design::SUCCESS_TYPE", "Design::INFO_TYPE", "Design::WARNING_TYPE" or "Design::ERROR_TYPE"
     * @param integer $iRedirectCode Default NULL, so the redirect code will be "301".
     * @return void
     */
    public static function redirect($sUrl = null, $sMessage = null, $sType = Design::SUCCESS_TYPE, $iRedirectCode = null)
    {
        if (!Http::getStatusCodes($iRedirectCode)) $iRedirectCode = 301;
        Http::setHeadersByCode(Http::getStatusCodes($iRedirectCode));

        $oHttpRequest = new HttpRequest;
        $sUrl = (!empty($sUrl)) ? $sUrl : $oHttpRequest->currentUrl();
        $sUrl = $oHttpRequest->pH7Url($sUrl);
        unset($oHttpRequest);

        if (!empty($sMessage))
            (new \PH7\Framework\Layout\Html\Design)->setFlashMsg($sMessage, $sType);

        header('Location: ' . $sUrl);
        exit;
    }

    /**
     * Gets the self URL.
     *
     * @static
     * @return string The URL.
     */
    public static function selfUrl()
    {
        $sSecure = (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 's' : '';
        $sServerProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
        $sProtocol = substr($sServerProtocol, 0, strpos($sServerProtocol, PH7_SH)) . $sSecure;

        // @var mixed $mPort (null or integer)
        $mPort = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':' . $_SERVER['SERVER_PORT']);

        return $sProtocol . '://' . $_SERVER['SERVER_NAME'] . $mPort . $_SERVER['REQUEST_URI'];
    }

}
