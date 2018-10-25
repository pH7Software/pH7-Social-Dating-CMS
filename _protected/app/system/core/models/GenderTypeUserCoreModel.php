<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

final class GenderTypeUserCoreModel
{
    const CONSIDER_COUPLE_GENDER = true;
    const IGNORE_COUPLE_GENDER = false;

    const FEMALE = 'female';
    const MALE = 'male';
    const COUPLE = 'couple';

    const GENDERS = [
        self::FEMALE,
        self::MALE,
        self::COUPLE
    ];

    /**
     * @param string $sGender
     * @param bool $bIncludeCoupleGender
     *
     * @return bool
     */
    public static function isGenderValid($sGender, $bIncludeCoupleGender = self::CONSIDER_COUPLE_GENDER)
    {
        $aGenders = self::GENDERS;

        if (!$bIncludeCoupleGender) {
            unset($aGenders[self::COUPLE]);
        }

        return in_array($sGender, self::GENDERS, true);
    }
}
