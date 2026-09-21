<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\TwoFactorAuth\Forms\Processing;

use PH7\Framework\Session\Session;
use PH7\RememberMeCore;
use PH7\TwoFactorAuthCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class VerificationCodeFormProcessTest extends TestCase
{
    private string $sSessionPath;

    protected function setUp(): void
    {
        // Keep challenge validation, TOTP and account/session creation real; isolate persistence and redirects.
        class_alias(VerificationFormStub::class, 'PH7\\Form');
        class_alias(VerificationProfileModelStub::class, 'PH7\\UserCoreModel');
        class_alias(VerificationProfileModelStub::class, 'PH7\\AffiliateCoreModel');
        class_alias(VerificationProfileModelStub::class, 'PH7\\AdminCoreModel');
        class_alias(VerificationFactorModelStub::class, 'PH7\\TwoFactorAuthModel');
        class_alias(VerificationRememberMeStub::class, 'PH7\\RememberMeCore');
        class_alias(VerificationSecurityStub::class, 'PH7\\Framework\\Mvc\\Model\\Security');
        class_alias(VerificationSettingsStub::class, 'PH7\\Framework\\Mvc\\Model\\DbConfig');
        class_alias(VerificationHeaderStub::class, 'PH7\\Framework\\Url\\Header');
        class_alias(VerificationUriStub::class, 'PH7\\Framework\\Mvc\\Router\\Uri');
        class_alias(VerificationPfbcStub::class, 'PFBC\\Form');
        require PH7_PATH_SYS_MOD . 'two-factor-auth/forms/processing/VerificationCodeFormProcess.php';
        $this->sSessionPath = sys_get_temp_dir() . '/ph7-2fa-session-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->sSessionPath, 0700));
        session_save_path($this->sSessionPath);
        $_SERVER['HTTP_USER_AGENT'] = 'pH7Builder regression test';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        VerificationFormStub::$oSession = new Session();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            VerificationFormStub::$oSession->destroy();
        }
        foreach (glob($this->sSessionPath . '/*') as $sFile) {
            unlink($sFile);
        }
        rmdir($this->sSessionPath);
    }

    #[DataProvider('provideProfiles')]
    public function testFinalAuthenticationUsesCurrentAccountState(string $sModule, int $iActive, int $iBan, bool $bAllowed): void
    {
        $this->prepareChallenge($sModule, $iActive, $iBan);
        $this->verify($sModule);
        $sSessionKey = $sModule === PH7_ADMIN_MOD ? 'admin_id' : ($sModule === 'affiliate' ? 'affiliate_id' : 'member_id');
        self::assertSame($bAllowed, VerificationFormStub::$oSession->exists($sSessionKey));
        self::assertSame($bAllowed && $sModule === 'user', VerificationRememberMeStub::$bEnabled);
        self::assertFalse(VerificationFormStub::$oSession->exists(TwoFactorAuthCore::PROFILE_ID_SESS_NAME));
        self::assertFalse(VerificationFormStub::$oSession->exists(RememberMeCore::STAY_LOGGED_IN_REQUESTED));
    }

    public static function provideProfiles(): array
    {
        return [
            'member' => ['user', 1, 0, true],
            'banned member' => ['user', 1, 1, false],
            'inactive member' => ['user', 3, 0, false],
            'affiliate' => ['affiliate', 1, 0, true],
            'banned affiliate' => ['affiliate', 1, 1, false],
            'inactive affiliate' => ['affiliate', 2, 0, false],
            'admin' => [PH7_ADMIN_MOD, 1, 0, true],
            'banned admin' => [PH7_ADMIN_MOD, 1, 1, false]
        ];
    }

    #[DataProvider('provideInvalidChallenges')]
    public function testInvalidChallengeCannotReachAuthentication(string $sCase): void
    {
        $this->prepareChallenge('user', 1, 0);
        $sModule = 'user';
        switch ($sCase) {
            case 'expired':
                VerificationFormStub::$oSession->set('2fa_expires', time() - 1);
                break;
            case 'role mismatch':
                $sModule = PH7_ADMIN_MOD;
                break;
            case 'deleted account':
                VerificationFactorModelStub::$oProfile = false;
                break;
            case 'disabled two factor':
                VerificationFactorModelStub::$oProfile->isTwoFactorAuth = '0';
                break;
            case 'missing secret':
                VerificationFactorModelStub::$oProfile->twoFactorAuthSecret = null;
                break;
            case 'invalid secret':
                VerificationFactorModelStub::$oProfile->twoFactorAuthSecret = 'invalid secret';
                break;
        }
        $this->verify($sModule);
        self::assertFalse(VerificationFormStub::$oSession->exists('member_id'));
        self::assertFalse(VerificationFormStub::$oSession->exists('admin_id'));
        self::assertFalse(VerificationRememberMeStub::$bEnabled);
        self::assertFalse(VerificationFormStub::$oSession->exists(TwoFactorAuthCore::PROFILE_ID_SESS_NAME));
        if (in_array($sCase, ['missing secret', 'invalid secret'], true)) {
            self::assertStringContainsString('contact the site administrator', VerificationHeaderStub::$sMessage);
        }
    }

    public static function provideInvalidChallenges(): array
    {
        return [['expired'], ['role mismatch'], ['deleted account'], ['disabled two factor'], ['missing secret'], ['invalid secret']];
    }

    public function testWrongCodeDoesNotAuthenticateOrConsumeChallenge(): void
    {
        $this->prepareChallenge('user', 1, 0);
        VerificationHttpStub::$sCode = 'not-a-code';
        new \PH7\VerificationCodeFormProcess('user');
        self::assertFalse(VerificationFormStub::$oSession->exists('member_id'));
        self::assertFalse(VerificationRememberMeStub::$bEnabled);
        self::assertSame(42, TwoFactorAuthCore::getChallengeProfileId(VerificationFormStub::$oSession, 'user'));
        self::assertSame(1, VerificationSecurityStub::$iAttempts);
        self::assertTrue(VerificationPfbcStub::$bError);
    }

    public function testLockedOutRequestCannotUseACorrectCode(): void
    {
        $this->prepareChallenge('user', 1, 0);
        VerificationSecurityStub::$bAllowed = false;
        new \PH7\VerificationCodeFormProcess('user');
        self::assertFalse(VerificationFormStub::$oSession->exists('member_id'));
        self::assertFalse(VerificationRememberMeStub::$bEnabled);
        self::assertTrue(VerificationPfbcStub::$bError);
    }

    private function prepareChallenge(string $sModule, int $iActive, int $iBan): void
    {
        TwoFactorAuthCore::beginChallenge(VerificationFormStub::$oSession, $sModule, 42);
        VerificationFormStub::$oSession->set(RememberMeCore::STAY_LOGGED_IN_REQUESTED, 1);
        $oAuthenticator = TwoFactorAuthCore::createAuthenticator();
        $sSecret = $oAuthenticator->createSecret();
        VerificationFactorModelStub::$oProfile = (object)[
            'profileId' => 42, 'active' => $iActive, 'ban' => $iBan,
            'email' => 'test@example.invalid', 'username' => 'test', 'firstName' => 'Test',
            'sex' => 'male', 'groupId' => 1, 'isTwoFactorAuth' => '1', 'twoFactorAuthSecret' => $sSecret
        ];
        VerificationHttpStub::$sCode = $oAuthenticator->getCode($sSecret);
    }

    private function verify(string $sModule): void
    {
        try {
            new \PH7\VerificationCodeFormProcess($sModule);
            self::fail('Expected a redirect after verification.');
        } catch (VerificationRedirect $oException) {
            self::assertSame('Local redirect', $oException->getMessage());
        }
    }
}

class VerificationFormStub
{
    public static Session $oSession;
    protected Session $session;
    protected VerificationHttpStub $httpRequest;
    protected stdClass $view;

    public function __construct()
    {
        $this->session = self::$oSession;
        $this->httpRequest = new VerificationHttpStub();
        $this->view = new stdClass();
    }

    public static function loginAttemptsExceededMsg(int $iMinutes): string
    {
        return 'Please wait before trying again.';
    }
}

final class VerificationHttpStub
{
    public static string $sCode;

    public function post(string $sName): string
    {
        return self::$sCode;
    }
}

final class VerificationProfileModelStub
{
    public function getEmail(...$aArguments): string
    {
        return 'test@example.invalid';
    }

    public function setLastActivity(...$aArguments): void
    {
    }
}

final class VerificationFactorModelStub
{
    public static stdClass|false $oProfile;

    public function __construct(string $sModule)
    {
    }

    public function getAuthProfile(int $iProfileId): stdClass|false
    {
        return self::$oProfile;
    }
}

final class VerificationRememberMeStub
{
    public const STAY_LOGGED_IN_REQUESTED = 'stayed_logged_requested';
    public static bool $bEnabled = false;

    public function isEligible(Session $oSession): bool
    {
        return $oSession->exists(self::STAY_LOGGED_IN_REQUESTED);
    }

    public function enableSession(stdClass $oProfile): void
    {
        self::$bEnabled = true;
    }
}

final class VerificationSecurityStub
{
    public static bool $bAllowed = true;
    public static int $iAttempts = 0;

    public function checkLoginAttempt(...$aArguments): bool
    {
        return self::$bAllowed;
    }

    public function addLoginAttempt(...$aArguments): void
    {
        ++self::$iAttempts;
    }

    public function clearLoginAttempts(...$aArguments): void
    {
    }

    public function addLoginLog(...$aArguments): void
    {
    }

    public function addSessionLog(...$aArguments): void
    {
    }
}

final class VerificationSettingsStub
{
    public static function getSetting(string $sName): int
    {
        return $sName === 'maxUserLoginAttempts' ? 5 : 1;
    }
}

final class VerificationHeaderStub
{
    public static string $sMessage = '';

    public static function redirect(...$aArguments): void
    {
        self::$sMessage = $aArguments[1] ?? '';
        throw new VerificationRedirect('Local redirect');
    }
}

final class VerificationUriStub
{
    public static function get(...$aArguments): string
    {
        return '/test-redirect';
    }
}

final class VerificationRedirect extends RuntimeException
{
}

final class VerificationPfbcStub
{
    public static bool $bError = false;

    public static function setError(string $sForm, string $sMessage): void
    {
        self::$bError = true;
    }
}
