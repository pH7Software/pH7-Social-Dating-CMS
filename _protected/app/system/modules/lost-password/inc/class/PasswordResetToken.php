<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Pattern\Statik;

final class PasswordResetToken
{
    use Statik;

    private const LIFETIME = 3600;

    public static function create(): string
    {
        // Minute precision keeps a purpose marker and 128 random bits within 40 characters.
        return 'r' . sprintf('%07x', intdiv(time(), 60)) . bin2hex(random_bytes(16));
    }

    public static function isCurrent(string $sToken): bool
    {
        if (!preg_match('/^r[0-9a-f]{39}$/D', $sToken)) {
            return false;
        }

        $iIssuedAt = (int)hexdec(substr($sToken, 1, 7)) * 60;
        $iAge = time() - $iIssuedAt;

        return $iAge >= 0 && $iAge < self::LIFETIME;
    }

    public static function hash(string $sToken, string $sPasswordHash): string
    {
        // Fit the legacy column and invalidate the link after any password change.
        return substr(hash('sha256', $sToken . $sPasswordHash), 0, UserCoreModel::HASH_VALIDATION_LENGTH);
    }
}
