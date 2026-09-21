<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\LostPassword\Inc\Classes;

use PH7\PasswordResetToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_SYS_MOD . 'lost-password/inc/class/PasswordResetToken.php';

final class PasswordResetTokenTest extends TestCase
{
    public function testTokensAreRandomCurrentAndFitTheExistingColumn(): void
    {
        $sToken = PasswordResetToken::create();
        self::assertMatchesRegularExpression('/^r[0-9a-f]{39}$/D', $sToken);
        self::assertTrue(PasswordResetToken::isCurrent($sToken));
        self::assertNotSame($sToken, PasswordResetToken::create());
        $sHash = PasswordResetToken::hash($sToken, 'password-hash');
        self::assertSame(40, strlen($sHash));
        self::assertNotSame($sToken, $sHash);
        self::assertNotSame($sHash, PasswordResetToken::hash($sToken, 'changed-password-hash'));
    }

    #[DataProvider('provideInvalidTokens')]
    public function testInvalidAndLegacyTokensAreRejected(string $sToken): void
    {
        self::assertFalse(PasswordResetToken::isCurrent($sToken));
    }

    public static function provideInvalidTokens(): array
    {
        return [[''], [str_repeat('a', 40)], ['r' . str_repeat('a', 39)], ['r123'], ['r1234567' . str_repeat('b', 32) . "\n"]];
    }

    #[DataProvider('provideTokenAges')]
    public function testTimeBoundary(int $iAge, bool $bExpected): void
    {
        $sToken = 'r' . sprintf('%07x', intdiv(time() - $iAge, 60)) . str_repeat('a', 32);
        self::assertSame($bExpected, PasswordResetToken::isCurrent($sToken));
    }

    public static function provideTokenAges(): array
    {
        return [[0, true], [3480, true], [3600, false], [7200, false], [-120, false]];
    }
}
