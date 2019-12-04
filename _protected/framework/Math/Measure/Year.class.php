<?php
/**
 * @title          Year Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;

defined('PH7') or exit('Restricted access');

class Year implements Measurable
{
    /** @var int */
    private $iYear;

    /** @var int */
    private $iMonth;

    /** @var int */
    private $iDay;

    /** @var int */
    private $iTimestamp;

    /**
     * Calculating the age of a user relative to their date of birth.
     *
     * @param int $iBirthYear
     * @param int $iBirthMonth
     * @param int $iBirthDay
     * @param int|null $iTimestamp You can optionally set another date for the calculation of the age. By default age is checked against the current date.
     */
    public function __construct($iBirthYear, $iBirthMonth, $iBirthDay, $iTimestamp = null)
    {
        $this->iYear = $iBirthYear;
        $this->iMonth = $iBirthMonth;
        $this->iDay = $iBirthDay;

        // If you want to check the current date (default)
        $this->iTimestamp = !empty($iTimestamp) ? $iTimestamp : time();
    }

    /**
     * Get User's age.
     *
     * @return int The age of the user.
     *
     * @internal Don't use strict comparisons since some integer values got their types as a strict.
     */
    public function get()
    {
        // We estimate the age, one year in excess
        $iAge = date('Y', $this->iTimestamp) - $this->iYear;
        $iCurrentMonth = date('n', $this->iTimestamp);

        // Taken out a year if the birthday is not over yet
        if ($this->iMonth > $iCurrentMonth ||
            ($this->iMonth === $iCurrentMonth && $this->iDay > date('j', $this->iTimestamp))
        ) {
            $iAge--;
        }

        return (int)$iAge;
    }
}
