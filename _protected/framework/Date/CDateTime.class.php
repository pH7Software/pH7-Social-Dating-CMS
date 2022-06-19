<?php
/**
 * @desc             Some useful methods of dates, formatting managements of time relative to the language.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Date
 * @version          1.2
 */

declare(strict_types=1);

namespace PH7\Framework\Date;

defined('PH7') or exit('Restricted access');

use DateTime;
use DateTimeZone;
use PH7\Framework\Config\Config;

class CDateTime
{
    private const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    private Config $oConfig;

    private DateTime $oDateTime;

    public function __construct()
    {
        $this->oConfig = Config::getInstance();
    }

    /**
     * Get, initialization method.
     *
     * @param string|int|null $mTime If specified, you must enter a date/timestamp, otherwise it's the current time.
     * @param string|null $mTimeZone If leave null, it will give the timezone set in the lang config.ini file.
     *
     * @return self
     * @throws \Exception Throws Exception in case of an error.
     */
    public function get($mTime = null, $mTimeZone = null): self
    {
        $sSetTime = static function () use ($mTime) {
            if ($mTime !== null) {
                $iTimestamp = !is_numeric($mTime) ? (int)strtotime($mTime) : $mTime;
                return date(self::DEFAULT_DATE_FORMAT, $iTimestamp);
            }
            return 'now';
        };

        $sSetTimeZone = $mTimeZone !== null ? $mTimeZone : $this->oConfig->values['language.application']['timezone'];
        $this->oDateTime = new DateTime($sSetTime(), new DateTimeZone($sSetTimeZone));

        return $this;
    }

    /**
     * Get the date + time (e.g. 05-29-2017 10:25:00).
     */
    public function dateTime(?string $sFormat = null): string
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['date_time_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }

    /**
     * Get the date with the right format (e.g. 05-29-2017).
     */
    public function date(?string $sFormat = null): string
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['date_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }

    /**
     * Get the time with the right format (e.g. 10:26:35).
     */
    public function time(?string $sFormat = null): string
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['time_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }
}
