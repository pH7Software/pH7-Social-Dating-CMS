<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Session\Session;

final class SmsVerificationCore
{
    public const PROFILE_ID_SESS_NAME = 'sms_verification_profile_id';
    public const PHONE_NUMBER_SESS_NAME = 'user_phone_number';
    public const CODE_SESS_NAME = 'sms_verification_code';
    private const EXPIRES_SESS_NAME = 'sms_verification_expires';
    private const CHALLENGE_LIFETIME = 900;

    public static function beginChallenge(Session $oSession, int $iProfileId): void
    {
        if ($iProfileId < 1) {
            throw new \InvalidArgumentException('Invalid SMS verification profile.');
        }

        self::clearChallenge($oSession);
        $oSession->regenerateId();
        $oSession->set([
            self::PROFILE_ID_SESS_NAME => $iProfileId,
            self::EXPIRES_SESS_NAME => time() + self::CHALLENGE_LIFETIME
        ]);
    }

    public static function getChallengeProfileId(Session $oSession): ?int
    {
        $iProfileId = $oSession->get(self::PROFILE_ID_SESS_NAME);
        $iExpires = $oSession->get(self::EXPIRES_SESS_NAME);

        return is_int($iProfileId) && $iProfileId > 0 && is_int($iExpires) && $iExpires > time()
            ? $iProfileId
            : null;
    }

    public static function clearChallenge(Session $oSession): void
    {
        $oSession->remove([
            self::PROFILE_ID_SESS_NAME,
            self::PHONE_NUMBER_SESS_NAME,
            self::CODE_SESS_NAME,
            self::EXPIRES_SESS_NAME
        ]);
    }
}
