<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\TwoFactorAuth\Controllers;

use PH7\Framework\Date\CDateTime;
use PH7\MainController;
use PH7\TwoFactorAuthCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SetupKeyBackupTest extends TestCase
{
    #[DataProvider('provideOtherRoles')]
    public function testSetupRequiresAuthenticationForTheSelectedRole(string $sAuthenticated, string $sTarget): void
    {
        class_alias(BackupUserStub::class, 'PH7\\UserCore');
        class_alias(BackupAffiliateStub::class, 'PH7\\AffiliateCore');
        class_alias(BackupAdminStub::class, 'PH7\\AdminCore');
        class_alias(BackupHeaderStub::class, 'PH7\\Framework\\Url\\Header');
        class_alias(BackupUriStub::class, 'PH7\\Framework\\Mvc\\Router\\Uri');
        BackupUserStub::$sAuthenticated = $sAuthenticated;
        require PH7_PATH_SYS_MOD . 'two-factor-auth/controllers/MainController.php';
        $oController = (new ReflectionClass(MainController::class))->newInstanceWithoutConstructor();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Sign in as the selected role');
        $oController->setup($sTarget);
    }

    public static function provideOtherRoles(): array
    {
        return [['user', PH7_ADMIN_MOD], ['user', 'affiliate'], [PH7_ADMIN_MOD, 'user'], ['affiliate', PH7_ADMIN_MOD]];
    }

    public function testBackupRestoresAnAuthenticatorWithoutATemporaryCode(): void
    {
        require PH7_PATH_SYS_MOD . 'two-factor-auth/controllers/MainController.php';
        $oAuthenticator = TwoFactorAuthCore::createAuthenticator();
        $sSecret = $oAuthenticator->createSecret();
        $oController = (new ReflectionClass(MainController::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty($oController, 'sMod'))->setValue($oController, 'user');
        (new ReflectionProperty($oController, 'dateTime'))->setValue($oController, new BackupDateTimeStub());
        $sDownload = (new ReflectionMethod($oController, 'getSetupKeyBackupMessage'))->invoke($oController, $sSecret);
        self::assertSame(1, preg_match('/Setup key: ([A-Z2-7]+)/', $sDownload, $aMatches));
        self::assertSame($sSecret, $aMatches[1]);
        self::assertStringContainsString('not a one-time recovery code', $sDownload);
        self::assertStringContainsString('never share', $sDownload);
        self::assertStringNotContainsString('BACKUP VERIFICATION CODE', $sDownload);
        $oRestoredAuthenticator = TwoFactorAuthCore::createAuthenticator();
        foreach ([300, 86400, 31536000] as $iDelay) {
            $iTime = 1800000000 + $iDelay;
            $sCode = $oRestoredAuthenticator->getCode($aMatches[1], $iTime);
            self::assertTrue($oAuthenticator->verifyCode($sSecret, $sCode, 1, $iTime));
        }
    }
}

final class BackupDateTimeStub extends CDateTime
{
    public function __construct()
    {
    }

    public function get($mTime = null, $mTimeZone = null): self
    {
        return $this;
    }

    public function date(?string $sFormat = null): string
    {
        return '2026-09-21';
    }
}

final class BackupUserStub
{
    public static string $sAuthenticated;

    public static function auth(): bool
    {
        return self::$sAuthenticated === 'user';
    }
}

final class BackupAffiliateStub
{
    public static function auth(): bool
    {
        return BackupUserStub::$sAuthenticated === 'affiliate';
    }
}

final class BackupAdminStub
{
    public static function auth(): bool
    {
        return BackupUserStub::$sAuthenticated === PH7_ADMIN_MOD;
    }
}

final class BackupHeaderStub
{
    public static function redirect(...$aArguments): void
    {
        throw new \RuntimeException('Sign in as the selected role');
    }
}

final class BackupUriStub
{
    public static function get(...$aArguments): string
    {
        return '/test-login';
    }
}
