<?php
/**
 * @title            CDateTime Class
 * @desc             Some useful methods of dates, formatting managements of time relative to the language.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Date
 * @version          1.2
 */

namespace PH7\Framework\Date;

defined('PH7') or exit('Restricted access');

use DateTime;
use DateTimeZone;
use PH7\Framework\Config\Config;

class CDateTime
{
    const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var Config */
    private $oConfig;

    /** @var DateTime */
    private $oDateTime;

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
     */
    public function get($mTime = null, $mTimeZone = null)
    {
        $sSetTime = $mTime !== null ? date(self::DEFAULT_DATE_FORMAT, (!is_numeric($mTime) ? strtotime($mTime) : $mTime)) : 'now';
        $sSetTimeZone = $mTimeZone !== null ? $mTimeZone : $this->oConfig->values['language.application']['timezone'];
        $this->oDateTime = new DateTime($sSetTime, new DateTimeZone($sSetTimeZone));

        return $this;
    }

    /**
     * Get the date + time (e.g. 05-29-2017 10:25:00).
     *
     * @param string $sFormat
     *
     * @return string Date time format
     */
    public function dateTime($sFormat = null)
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['date_time_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }

    /**
     * Get the date (e.g. 05-29-2017).
     *
     * @param string $sFormat
     *
     * @return string Date format
     */
    public function date($sFormat = null)
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['date_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }

    /**
     * Get the time (e.g. 10:26:35).
     *
     * @param string $sFormat
     *
     * @return string Time format
     */
    public function time($sFormat = null)
    {
        $sFormat = $sFormat === null ? $this->oConfig->values['language.application']['time_format'] : $sFormat;

        return $this->oDateTime->format($sFormat);
    }
}
