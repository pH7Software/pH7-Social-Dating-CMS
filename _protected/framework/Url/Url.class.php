<?php
/**
 * @title            Url Class
 * @desc             Useful URL methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Url
 */

namespace PH7\Framework\Url;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Statik;

class Url
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * URL-encodes string.
     *
     * @static
     * @param string $sUrl
     * @return string
     */
    public static function encode($sUrl)
    {
        return urlencode($sUrl);
    }

    /**
     * Decodes URL-encoded string.
     *
     * @static
     * @param string $sUrl
     * @return string
     */
    public static function decode($sUrl)
    {
        return urldecode($sUrl);
    }

    /**
     * Clean a URL.
     *
     * @static
     * @param string $sUrl
     * @return string
     */
    public static function clean($sUrl)
    {
        return str_replace(array(' ', '&'), array('%20', '&amp;'), $sUrl);
    }

    /**
     * Generate URL-encoded query string.
     *
     * N.B.: We recreate our own function with default parameters (because the default parameters of PHP we do not like;))
     *
     * @static
     * @param array $aParams
     * @param string $sNumericPrefix Default NULL
     * @param string $sArgSeparator Default '&amp;
     * @param integer $iEncType Default PHP_QUERY_RFC1738
     * @return string Returns a URL-encoded string.
     */
    public static function httpBuildQuery(array $aParams, $sNumericPrefix = null, $sArgSeparator = '&amp;', $iEncType = PHP_QUERY_RFC1738)
    {
        return http_build_query($aParams, $sNumericPrefix, $sArgSeparator, $iEncType);
    }
}
