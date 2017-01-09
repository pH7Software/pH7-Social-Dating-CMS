<?php
/**
 * @title          Year Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;
defined('PH7') or exit('Restricted access');

class Year implements IMeasure
{
    protected $iYear, $iMonth, $iDay, $iTimestamp;

    /**
     * Calculating the age of a user relative to their date of birth.
     *
     * @param integer $iBirthYear
     * @param integer $iBirthMonth
     * @param integer $iBirthDay
     * @param integer $iTimestamp You can optionally set another date for the calculation of the age. By default age is checked against the current date.
     */
    public function __construct($iBirthYear, $iBirthMonth, $iBirthDay, $iTimestamp = null)
    {
        $this->iYear = $iBirthYear;
        $this->iMonth = $iBirthMonth;
        $this->iDay = $iBirthDay;

        // If you want to check the current date (default)
        $this->iTimestamp = (!empty($iTimestamp)) ? $iTimestamp : time();
    }

    /**
     * Get User's age.
     *
     * @return integer The age of the user.
     */
    public function get()
    {
        // We estimate the age, one year in excess
        $iAge = date('Y', $this->iTimestamp) - $this->iYear;

        // Taken out a year if the birthday is not over yet
        if($this->iMonth > date('n', $this->iTimestamp) || ($this->iMonth == date('n', $this->iTimestamp) && $this->iDay > date('j', $this->iTimestamp)))
        $iAge--;

        return (int) $iAge;
    }
}
