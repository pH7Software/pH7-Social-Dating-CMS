<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Xml / Inc / Class
 */

namespace PH7;

use PH7\Framework\Date\Exception;

class DateFormat
{
    const RFC_2822_FORMAT = 'r';
    const ISO_8601_FORMAT = 'c';

    const AVAILABLE_FORMATS = [
        self::RFC_2822_FORMAT,
        self::ISO_8601_FORMAT
    ];

    /**
     * Private constructor to prevent instantiation of class because it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Get date format for RSS feed.
     *
     * @param string|null $sDate
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getRss($sDate = null)
    {
        return self::get(self::RFC_2822_FORMAT, $sDate);
    }

    /**
     * Get date format for sitemap.
     *
     * @param string|null $sDate
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getSitemap($sDate = null)
    {
        return self::get(self::ISO_8601_FORMAT, $sDate);
    }

    /**
     * @param string $sFormat
     * @param string $sDate
     *
     * @return string
     *
     * @throws Exception If the date format is incorrect.
     */
    private static function get($sFormat, $sDate)
    {
        if (!in_array($sFormat, self::AVAILABLE_FORMATS, true)) {
            throw new Exception(self::getExceptionMessage());
        }

        $iTime = !empty($sDate) ? strtotime($sDate) : time();

        return date($sFormat, $iTime);
    }

    /**
     * @param string $sFormat
     *
     * @return string
     */
    private static function getExceptionMessage($sFormat)
    {
        $sDateFormats = implode('", "', self::AVAILABLE_FORMATS);

        return sprintf('Wrong "%s" date format! You can only choose between "%s"', $sFormat, $sDateFormats);
    }
}
