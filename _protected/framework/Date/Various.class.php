<?php
/**
 * @title            Various Date Class
 * @desc             Useful date methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Date
 */

declare(strict_types=1);

namespace PH7\Framework\Date;

defined('PH7') or exit('Restricted access');

use DateTimeImmutable;

class Various
{
    /**
     * Get the Unix timestamp representing the date.
     *
     * @param string $sTime A date/time string valid formats (http://php.net/manual/en/datetime.formats.php).
     *
     * @return int
     * @throws \Exception
     */
    public static function getTime(string $sTime = 'now'): int
    {
        return (new DateTimeImmutable($sTime))->getTimestamp();
    }

    /**
     * Add or Remove Time from the current date.
     *
     * @param string $sTime A date/time string. EX: Add one month '+1 month' | Remove one month '-1 month'
     *
     * @return int The Unix timestamp representing including the time modification.
     */
    public static function setTime(string $sTime): int
    {
        return (new DateTimeImmutable())->modify($sTime)->getTimestamp();
    }

    /**
     * Convert the time (e.g. hour:minutes:seconds) to seconds.
     *
     * @param string $sHMS Hours:Minutes:Seconds e.g., 08:02:11
     *
     * @return int
     */
    public static function timeToSec(string $sHMS): int
    {
        if (!str_contains($sHMS, ':')) {
            return 0;
        }

        [$iHours, $iMinutes, $iSeconds] = array_pad(explode(':', $sHMS, 3), 3, 0);

        return ((int)$iHours * 3600) + ((int)$iMinutes * 60) + (int)$iSeconds;
    }

    /**
     * Convert the seconds to time.
     *
     * @param int $iSeconds
     *
     * @return string Example: 09:23
     */
    public static function secToTime(int|float|string $iSeconds): string
    {
        $iSeconds = (int)$iSeconds;

        $iMinutes = intdiv($iSeconds, 60);
        $iRemainingSeconds = $iSeconds % 60;

        return static::checkSecToTime($iMinutes) . ':' . static::checkSecToTime($iRemainingSeconds);
    }

    /**
     * Creates the text of the timestamp.
     *
     * @param int|string Unix timestamp or string date format.
     *
     * @return string Returns the text of the time stamp.
     */
    public static function textTimeStamp(int|string $mTime): string
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

        return match (true) {
            $iSeconds === 0 => t('%0% seconds ago.', 0.5),
            $iSeconds < 60 => t('%0% seconds ago.', $iSeconds),
            $iMinutes < 60 => $iMinutes === 1 ? t('one minute ago.') : t('%0% minutes ago.', $iMinutes),
            $iHours < 24 => $iHours === 1 ? t('one hour ago.') : t('%0% hours ago.', $iHours),
            $iDays < 7 => $iDays === 1 ? t('one day ago.') : t('%0% days ago.', $iDays),
            $iWeeks < 4 => $iWeeks === 1 ? t('one week ago.') : t('%0% weeks ago.', $iWeeks),
            $iMonths < 12 => $iMonths === 1 ? t('one month ago.') : t('%0% months ago.', $iMonths),
            default => $iYears === 1 ? t('one year ago.') : t('%0% years ago.', $iYears),
        };
    }

    /**
     * Checks the value format 00:00 of the conversion of seconds to the time.
     *
     * @see self::secToTime()
     */
    private static function checkSecToTime(int $iVal): string
    {
        return str_pad((string)$iVal, 2, '0', STR_PAD_LEFT);
    }
}
