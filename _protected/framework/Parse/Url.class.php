<?php
/**
 * @title            Url Parser Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str;

class Url
{
    const REGEX_SPACE = '/[\s]+/';
    const REGEX_URL_FORMAT = '#(^https?://|www\.|\.[a-z]{2,4}/?$)#i';

    const UNWANTED_SPECIAL_CHARS = [
        '«',
        '»',
        '"',
        '~',
        '#',
        '$',
        '@',
        '`',
        '§',
        '€',
        '£',
        'µ',
        '\\',
        '[',
        ']',
        '<',
        '>',
        '%',
        '*',
        '{',
        '}'
    ];

    const MINOR_SPECIAL_CHARS = [
        '.',
        '^',
        ',',
        ':',
        ';',
        '!'
    ];

    /**
     * Clean URL.
     *
     * @param string $sUrl
     * @param bool $bFullClean Also removes points, puts characters to lowercase, etc.
     *
     * @return string The new clean URL
     */
    public static function clean($sUrl, $bFullClean = true)
    {
        $sUrl = preg_replace(self::REGEX_SPACE, '-', $sUrl);
        $sUrl = str_replace(self::UNWANTED_SPECIAL_CHARS, '-', $sUrl);

        if ($bFullClean) {
            $sUrl = str_replace(self::MINOR_SPECIAL_CHARS, '', $sUrl);
            $oStr = new Str;
            $sUrl = $oStr->lower($sUrl);
            $sUrl = $oStr->escape($sUrl, true);
            unset($oStr);
        }

        return $sUrl;
    }

    /**
     * Gets the name of a URL.
     *
     * @param string $sLink The link
     *
     * @return string The name of the domain with the first letter capitalized.
     */
    public static function name($sLink)
    {
        $oStr = new Str;
        $sUrlName = preg_replace(self::REGEX_URL_FORMAT, '', $oStr->lower($sLink));
        $sLink = $oStr->upperFirst($sUrlName);
        unset($oStr);

        return $sLink;
    }
}
