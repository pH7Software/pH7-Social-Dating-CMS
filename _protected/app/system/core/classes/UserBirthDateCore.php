<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Math\Measure\Year as YearMeasure;

class UserBirthDateCore
{
    const DEFAULT_AGE = 30;
    const BIRTHDATE_DELIMITER = '-';
    const NUMBER_ARRAY_ELEMENTS = 3;

    /**
     * @param string $sBirthDate YYYY-MM-DD format.
     *
     * @return int
     */
    public static function getAgeFromBirthDate($sBirthDate)
    {
        $aAge = explode(self::BIRTHDATE_DELIMITER, $sBirthDate);

        if (self::isInvalidBirthDate($aAge)) {
            return self::DEFAULT_AGE;
        }

        return (new YearMeasure($aAge[0], $aAge[1], $aAge[2]))->get();
    }

    /**
     * @param array $aAge
     *
     * @return bool
     */
    private static function isInvalidBirthDate(array $aAge)
    {
        $iAgeElements = count($aAge);

        return $iAgeElements < self::NUMBER_ARRAY_ELEMENTS ||
            $iAgeElements > self::NUMBER_ARRAY_ELEMENTS;
    }
}
