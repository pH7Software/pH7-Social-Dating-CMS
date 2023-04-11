<?php
/**
 * @title            Url Class
 * @desc             Useful URL methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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
     */
    public static function encode(string $sUrl): string
    {
        return urlencode($sUrl);
    }

    /**
     * Decodes URL-encoded string.
     */
    public static function decode(string $sUrl): string
    {
        return urldecode($sUrl);
    }

    public static function clean(string $sUrl): string
    {
        return str_replace([' ', '&'], ['%20', '&amp;'], $sUrl);
    }

    /**
     * Generate URL-encoded query string.
     *
     * N.B.: We recreate our own function with default parameters (because the default parameters of PHP we do not like;))
     *
     * @return string Returns a URL-encoded string.
     */
    public static function httpBuildQuery(array $aParams, string $sNumericPrefix = '', string|null $sArgSeparator = '&amp;', int $iEncType = PHP_QUERY_RFC1738): string
    {
        return http_build_query($aParams, $sNumericPrefix, $sArgSeparator, $iEncType);
    }
}
