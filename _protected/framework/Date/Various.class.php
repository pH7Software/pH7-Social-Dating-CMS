<?php
/**
 * @title            Various Date Class
 * @desc             Useful date methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Date
 */

namespace PH7\Framework\Date;

defined('PH7') or exit('Restricted access');

use DateTime;

class Various
{
    /**
     * Get the Unix timestamp representing the date.
     *
     * @param string $sTime A date/time string valid formats (http://php.net/manual/en/datetime.formats.php). Default: 'now'
     *
     * @return integer
     */
    public static function getTime($sTime = 'now')
    {
        return (new DateTime($sTime))->getTimestamp();
    }

    /**
     * Add or Remove Time from the current date.
     *
     * @param string $sTime A date/time string. EX: Add one month '+1 month' | Remove one month '-1 month'
     *
     * @return integer The Unix timestamp representing including the time modification.
     */
    public static function setTime($sTime)
    {
        $oDate = new DateTime;
        $oDate->modify($sTime);
        $iNewTime = $oDate->getTimestamp();
        unset($oDate);

        return $iNewTime;
    }

    /**
     * Convert the time (e.g. hour:minutes:seconds) to seconds.
     *
     * @param integer $iHMS Hours/Minutes/Seconds
     *
     * @return integer
     */
    public static function timeToSec($iHMS)
    {
        list($iH, $iM, $iS) = explode(':', $iHMS);
        $iSeconds = 0;
        $iSeconds += ((int)$iH * 3600);
        $iSeconds += ((int)$iM * 60);
        $iSeconds += (int)$iS;

        return $iSeconds;
    }

    /**
     * Convert the seconds to time.
     *
     * @param integer $iSeconds
     *
     * @return string Example: 00:00
     */
    public static function secToTime($iSeconds)
    {
        $iSeconds = (int)$iSeconds;

        $iTime1 = floor($iSeconds / 60);
        $iTime2 = ($iSeconds % 60);

        return static::checkSecToTime($iTime1) . ':' . static::checkSecToTime($iTime2);
    }

    /**
     * Creates the text of the timestamp.
     *
     * @param integer|string Unix timestamp or string date format.
     *
     * @return string Returns the text of the time stamp.
     */
    public static function textTimeStamp($mTime)
    {
        if (is_string($mTime)) {
            // Converting the date string format into timeStamp
            $mTime = strtotime($mTime);
        }

        $iSeconds = time() - $mTime;

        $iMinutes = round($iSeconds / 60);
        $iHours = round($iSeconds / 3600);
        $iDays = round($iSeconds / 86400);
        $iWeeks = round($iSeconds / 604800);
        $iMonths = round($iSeconds / 2419200);
        $iYears = round($iSeconds / 29030400);

        if ($iSeconds === 0)
            $sTxt = t('%0% seconds ago.', 0.5);
        elseif ($iSeconds < 60)
            $sTxt = t('%0% seconds ago.', $iSeconds);
        elseif ($iMinutes < 60)
            $sTxt = $iMinutes === 1 ? t('one minute ago.') : t('%0% minutes ago.', $iMinutes);
        elseif ($iHours < 24)
            $sTxt = $iHours === 1 ? t('one hour ago.') : t('%0% hours ago.', $iHours);
        else
            if ($iDays < 7)
                $sTxt = $iDays === 1 ? t('one day ago.') : t('%0% days ago.', $iDays);
            elseif ($iWeeks < 4)
                $sTxt = $iWeeks === 1 ? t('one week ago.') : t('%0% weeks ago.', $iWeeks);
            elseif ($iMonths < 12)
                $sTxt = $iMonths === 1 ? t('one month ago.') : t('%0% months ago.', $iMonths);
            else
                $sTxt = $iYears === 1 ? t('one year ago.') : t('%0% years ago.', $iYears);

        return $sTxt;
    }

    /**
     * Checks the value format 00:00 of the conversion of seconds to the time.
     *
     * @see self::secToTime()
     *
     * @param integer $iVal
     *
     * @return integer
     */
    protected static function checkSecToTime($iVal)
    {
        return strlen($iVal) === 1 ? 0 . $iVal : $iVal;
    }
}
