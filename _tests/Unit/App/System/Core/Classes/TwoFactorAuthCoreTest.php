<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

use PH7\Framework\Session\Session;
use PH7\RememberMeCore;
use PH7\TwoFactorAuthCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class TwoFactorAuthCoreTest extends TestCase
{
    #[DataProvider('provideModules')]
    public function testChallengeIsBoundToItsRoleAndConsumed(string $sModule): void
    {
        $oSession = (new ReflectionClass(Session::class))->newInstanceWithoutConstructor();
        TwoFactorAuthCore::beginChallenge($oSession, $sModule, 42);
        self::assertSame(42, TwoFactorAuthCore::getChallengeProfileId($oSession, $sModule));
        foreach (['user', 'affiliate', PH7_ADMIN_MOD, 'invalid'] as $sOtherModule) {
            if ($sModule !== $sOtherModule) {
                self::assertNull(TwoFactorAuthCore::getChallengeProfileId($oSession, $sOtherModule));
            }
        }
        $oSession->set(RememberMeCore::STAY_LOGGED_IN_REQUESTED, 1);
        TwoFactorAuthCore::clearChallenge($oSession);
        self::assertNull(TwoFactorAuthCore::getChallengeProfileId($oSession, $sModule));
        self::assertFalse($oSession->exists(TwoFactorAuthCore::PROFILE_ID_SESS_NAME));
        self::assertFalse($oSession->exists(RememberMeCore::STAY_LOGGED_IN_REQUESTED));
    }

    public static function provideModules(): array
    {
        return [['user'], ['affiliate'], [PH7_ADMIN_MOD]];
    }

    public function testExpiredAndLegacyChallengesRequireAnotherLogin(): void
    {
        $oSession = (new ReflectionClass(Session::class))->newInstanceWithoutConstructor();
        $oSession->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, 42);
        self::assertNull(TwoFactorAuthCore::getChallengeProfileId($oSession, PH7_ADMIN_MOD));

        TwoFactorAuthCore::beginChallenge($oSession, PH7_ADMIN_MOD, 42);
        $oSession->set('2fa_expires', time() - 1);
        self::assertNull(TwoFactorAuthCore::getChallengeProfileId($oSession, PH7_ADMIN_MOD));
        TwoFactorAuthCore::clearChallenge($oSession);
    }

    public function testInvalidRoleCannotCreateAChallenge(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TwoFactorAuthCore::beginChallenge((new ReflectionClass(Session::class))->newInstanceWithoutConstructor(), 'invalid', 42);
    }
}
