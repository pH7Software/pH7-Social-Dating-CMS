<?php
/**
 * @title            CDateTime Class
 * @desc             Some useful methods of dates, formatting managements of time relative to the language.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Date
 * @version          1.2
 */

namespace PH7\Framework\Date;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;

class CDateTime
{

    private $_oConfig, $_oDateTime;

    public function __construct()
    {
        $this->_oConfig = Config::getInstance();
    }

    /**
     * Get, initialization method.
     *
     * @param string $sTime If specified, you must enter a date/time string else return the current time.
     * @return void $this
     */
    public function get($sTime = null)
    {
        $sSetTime = (!empty($sTime)) ? date('Y-m-d', strtotime($sTime)) : 'now';
        $this->_oDateTime = new \DateTime($sSetTime, new \DateTimeZone($this->_oConfig->values['language.application']['timezone']));
        return $this;
    }

    /**
     * Get Date Time.
     *
     * @param string $sFormat
     * @return string Date time format
     */
    public function dateTime($sFormat = null)
    {
        $sFormat = (empty($sFormat)) ? $this->_oConfig->values['language.application']['date_time_format'] : $sFormat;
        return $this->_oDateTime->format($sFormat);
    }

    /**
     * Get Date.
     *
     * @param string $sFormat
     * @return string Date format
     */
    public function date($sFormat = null)
    {
        $sFormat = (empty($sFormat)) ? $this->_oConfig->values['language.application']['date_format'] : $sFormat;
        return $this->_oDateTime->format($sFormat);
    }

    /**
     * Get Time.
     *
     * @param string $sFormat
     * @return string Time format
     */
    public function time($sFormat = null)
    {
        $sFormat = (empty($sFormat)) ? $this->_oConfig->values['language.application']['time_format'] : $sFormat;
        return $this->_oDateTime->format($sFormat);
    }

    public function __destruct()
    {
        unset($this->_oConfig, $this->_oDateTime);
    }

}
