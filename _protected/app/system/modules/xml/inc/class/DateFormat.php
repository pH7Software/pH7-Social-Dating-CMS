<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Xml / Inc / Class
 */
namespace PH7;

class DateFormat
{

    /**
     * Private constructor to prevent instantiation of class because it's a static class.
     */
    private function __construct() {}

    /**
     * Get date format for RSS feed.
     *
     * @param string $sDate Default NULL
     * @return string
     */
    public static function getRss($sDate = null)
    {
        return static::_get('r', $sDate);
    }

    /**
     * Get date format for sitemap.
     *
     * @param string $sDate Default NULL
     * @return string
     */
    public static function getSitemap($sDate = null)
    {
        return static::_get('c', $sDate);
    }

    /**
     * @param char $cFormat
     * @param string $sDate
     * @return string
     * @throws \PH7\Framework\Date\Exception If the date format is incorrect.
     */
    private static function _get($cFormat, $sDate)
    {
        if ('c' != $cFormat && 'r' != $cFormat) {
            throw new  \PH7\Framework\Date\Exception('Wrong format for the date! You only need to choose between "r" and "c".');
        }

        $iTime = (!empty($sDate)) ? strtotime($sDate) : time();
        return date($cFormat, $iTime);
    }

}
