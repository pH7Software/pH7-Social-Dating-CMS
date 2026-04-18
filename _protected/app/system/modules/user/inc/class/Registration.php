<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Inc / Class
 */

namespace PH7;

use PH7\Framework\Security\Security;

class Registration extends RegistrationCore
{
    public const SIGNUP_RECOVERY_PROFILE_ID_PARAM = 'signup_profile_id';
    public const SIGNUP_RECOVERY_TOKEN_PARAM = 'signup_recovery_token';

    public static function buildSignupRecoveryToken(int $iProfileId, string $sHashValidation, string $sStep): string
    {
        return hash('sha256', $iProfileId . '|' . $sHashValidation . '|' . $sStep . '|' . Security::PREFIX_SALT);
    }
}
