<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Security\Validate\Validate;

final class UserSignupInputValidator
{
    private const LOCATION_MIN_LENGTH = 2;
    private const LOCATION_MAX_LENGTH = 150;
    private const POSTAL_CODE_MIN_LENGTH = 2;
    private const POSTAL_CODE_MAX_LENGTH = 15;
    private const DESCRIPTION_MIN_LENGTH = 20;
    private const DESCRIPTION_MAX_LENGTH = 4000;

    public function __construct(private Validate $oValidate)
    {
    }

    public function isValid(array $aData, array $aAllowedCountryCodes): bool
    {
        return $this->oValidate->name($aData['first_name'])
            && $this->oValidate->name($aData['last_name'])
            && GenderTypeUserCore::isGenderValid($aData['sex'])
            && $this->areMatchGendersValid($aData['match_sex'])
            && in_array($aData['country'], $aAllowedCountryCodes, true)
            && $this->oValidate->str(
                $aData['city'],
                self::LOCATION_MIN_LENGTH,
                self::LOCATION_MAX_LENGTH
            )
            && $this->oValidate->str(
                $aData['state'],
                self::LOCATION_MIN_LENGTH,
                self::LOCATION_MAX_LENGTH
            )
            && $this->oValidate->str(
                $aData['zip_code'],
                self::POSTAL_CODE_MIN_LENGTH,
                self::POSTAL_CODE_MAX_LENGTH
            )
            && $this->oValidate->str(
                $aData['description'],
                self::DESCRIPTION_MIN_LENGTH,
                self::DESCRIPTION_MAX_LENGTH
            );
    }

    private function areMatchGendersValid(array $aMatchGenders): bool
    {
        if ($aMatchGenders === []) {
            return false;
        }

        foreach ($aMatchGenders as $sMatchGender) {
            if (!GenderTypeUserCore::isGenderValid($sMatchGender)) {
                return false;
            }
        }

        return true;
    }
}
