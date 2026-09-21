<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

final class TwoFactorAuthCore
{
    public const PROFILE_ID_SESS_NAME = '2fa_profile_id';
    private const MODULE_SESS_NAME = '2fa_module';
    private const EXPIRES_SESS_NAME = '2fa_expires';
    private const CHALLENGE_LIFETIME = 600;

    public static function beginChallenge(Session $oSession, string $sModule, int $iProfileId): void
    {
        if (!in_array($sModule, ['user', 'affiliate', PH7_ADMIN_MOD], true) || $iProfileId < 1) {
            throw new \InvalidArgumentException('Invalid two-factor authentication challenge.');
        }

        $oSession->regenerateId();
        $oSession->set([
            self::PROFILE_ID_SESS_NAME => $iProfileId,
            self::MODULE_SESS_NAME => $sModule,
            self::EXPIRES_SESS_NAME => time() + self::CHALLENGE_LIFETIME
        ]);
    }

    public static function getChallengeProfileId(Session $oSession, string $sModule): ?int
    {
        $iProfileId = $oSession->get(self::PROFILE_ID_SESS_NAME);
        $iExpires = $oSession->get(self::EXPIRES_SESS_NAME);
        if (!in_array($sModule, ['user', 'affiliate', PH7_ADMIN_MOD], true)
            || $oSession->get(self::MODULE_SESS_NAME) !== $sModule
            || !is_int($iProfileId) || $iProfileId < 1 || !is_int($iExpires) || $iExpires <= time()) {
            return null;
        }

        return $iProfileId;
    }

    public static function clearChallenge(Session $oSession): void
    {
        $oSession->remove([
            self::PROFILE_ID_SESS_NAME,
            self::MODULE_SESS_NAME,
            self::EXPIRES_SESS_NAME,
            RememberMeCore::STAY_LOGGED_IN_REQUESTED
        ]);
    }

    public static function getLoginUrl(string $sModule): string
    {
        return Uri::get(
            in_array($sModule, ['affiliate', PH7_ADMIN_MOD], true) ? $sModule : 'user',
            $sModule === 'affiliate' ? 'home' : 'main',
            'login'
        );
    }

    /**
     * QR codes are rendered locally (SVG, no PHP extension required), so the TOTP
     * secret is never sent to a third-party QR image service.
     */
    public static function createAuthenticator(?string $sIssuer = null): TwoFactorAuth
    {
        return new TwoFactorAuth(
            new BaconQrCodeProvider(format: 'svg'),
            $sIssuer
        );
    }
}
