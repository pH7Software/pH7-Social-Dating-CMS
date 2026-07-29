<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

final class TwoFactorAuthCore
{
    public const PROFILE_ID_SESS_NAME = '2fa_profile_id';

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
