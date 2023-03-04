<?php
/**
 * @title            Url Parser Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Parse
 */

declare(strict_types=1);

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str;

class Url
{
    private const REGEX_SPACE = '/[\s]+/';
    private const UNWANTED_SPECIAL_CHARS = [
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
    public static function clean(string $sUrl, bool $bFullClean = true): string
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
}
