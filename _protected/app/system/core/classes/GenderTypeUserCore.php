<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

final class GenderTypeUserCore
{
    public const CONSIDER_COUPLE_GENDER = true;
    public const IGNORE_COUPLE_GENDER = false;

    public const FEMALE = 'female';
    public const MALE = 'male';
    public const COUPLE = 'couple';

    public const GENDERS = [
        self::FEMALE => self::FEMALE,
        self::MALE => self::MALE,
        self::COUPLE => self::COUPLE
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

        return in_array($sGender, $aGenders, true);
    }
}
