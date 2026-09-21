<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Forms\Processing;

use PH7\Framework\Config\Config;
use PH7\Framework\Session\Session;
use PH7\PhoneNumberFormProcess;
use PH7\RememberMeCore;
use PH7\SmsVerificationCore;
use PH7\Verification;
use PH7\VerificationFormProcess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class VerificationFlowTest extends TestCase
{
    private string $sSessionPath;

    protected function setUp(): void
    {
        class_alias(SmsFlowFormStub::class, 'PH7\\Form');
        class_alias(SmsFlowModelStub::class, 'PH7\\SmsVerificationModel');
        class_alias(SmsFlowUserStub::class, 'PH7\\UserCore');
        class_alias(SmsFlowGatewayStub::class, 'PH7\\SmsGatewayFactory');
        class_alias(SmsFlowHeaderStub::class, 'PH7\\Framework\\Url\\Header');
        class_alias(SmsFlowUriStub::class, 'PH7\\Framework\\Mvc\\Router\\Uri');
        class_alias(SmsFlowPfbcStub::class, 'PFBC\\Form');
        require PH7_PATH_SYS_MOD . 'sms-verification/inc/class/Verification.php';
        require PH7_PATH_SYS_MOD . 'sms-verification/forms/processing/PhoneNumberFormProcess.php';
        require PH7_PATH_SYS_MOD . 'sms-verification/forms/processing/VerificationFormProcess.php';
        Config::getInstance()->values['module.setting'] = ['verification_code.length' => 6, 'default_sms_gateway' => 'test'];
        $this->sSessionPath = sys_get_temp_dir() . '/ph7-sms-session-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->sSessionPath, 0700));
        session_save_path($this->sSessionPath);
        SmsFlowFormStub::$oSession = new Session();
        $_SESSION = [];
        SmsVerificationCore::beginChallenge(SmsFlowFormStub::$oSession, 42);
        SmsFlowFormStub::$oSession->set(RememberMeCore::STAY_LOGGED_IN_REQUESTED, 1);
        SmsFlowHttpStub::$aPost = ['phone_number' => '+12025550123'];
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            SmsFlowFormStub::$oSession->destroy();
        }
        foreach (glob($this->sSessionPath . '/*') as $sFile) {
            unlink($sFile);
        }
        rmdir($this->sSessionPath);
    }

    public function testSuccessfulDeliveryStoresTheExactSentCodeForVerification(): void
    {
        try {
            new PhoneNumberFormProcess();
            self::fail('Expected the verification page.');
        } catch (SmsFlowRedirect $oException) {
            self::assertSame('sms-verification/main/verification', SmsFlowHeaderStub::$sUrl);
        }
        self::assertSame('+12025550123', SmsFlowGatewayStub::$sPhone);
        self::assertSame(1, preg_match('/^(\d{6}) is your verification code/', SmsFlowGatewayStub::$sMessage, $aMatches));
        self::assertSame(hash('sha256', $aMatches[1]), SmsFlowFormStub::$oSession->get(SmsVerificationCore::CODE_SESS_NAME)['hash']);
        self::assertTrue(Verification::consumeCode(SmsFlowFormStub::$oSession, $aMatches[1]));
    }

    #[DataProvider('provideDeliveryFailures')]
    public function testDeliveryFailureLeavesNoUsableCodeOrNumber(bool $bThrow): void
    {
        SmsFlowGatewayStub::$bSuccess = false;
        SmsFlowGatewayStub::$bThrow = $bThrow;
        $sLogPath = tempnam(sys_get_temp_dir(), 'ph7-sms-log-');
        $sOldLog = ini_set('error_log', $sLogPath);
        try {
            new PhoneNumberFormProcess();
            self::assertFalse(SmsFlowFormStub::$oSession->exists(SmsVerificationCore::CODE_SESS_NAME));
            self::assertFalse(SmsFlowFormStub::$oSession->exists(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
            self::assertStringContainsString('Could not send a code', SmsFlowPfbcStub::$sError);
            self::assertStringNotContainsString('provider-private-detail', file_get_contents($sLogPath));
        } finally {
            ini_set('error_log', $sOldLog);
            unlink($sLogPath);
        }
    }

    public static function provideDeliveryFailures(): array
    {
        return [[false], [true]];
    }

    public function testResendLimitPreventsAnotherProviderCall(): void
    {
        Verification::issueCode(SmsFlowFormStub::$oSession, '+12025550123');
        new PhoneNumberFormProcess();
        self::assertSame(0, SmsFlowGatewayStub::$iSends);
        self::assertStringContainsString('wait one minute', SmsFlowPfbcStub::$sError);
    }

    #[DataProvider('provideUnavailableChallenges')]
    public function testUnavailableAccountOrChallengeCannotSendSms(bool $bExpired): void
    {
        if ($bExpired) {
            SmsFlowFormStub::$oSession->set('sms_verification_expires', time() - 1);
        } else {
            SmsFlowModelStub::$bPending = false;
        }
        try {
            new PhoneNumberFormProcess();
            self::fail('Expected sign-in restart.');
        } catch (SmsFlowRedirect $oException) {
            self::assertSame('user/main/login', SmsFlowHeaderStub::$sUrl);
        }
        self::assertSame(0, SmsFlowGatewayStub::$iSends);
        self::assertNull(SmsVerificationCore::getChallengeProfileId(SmsFlowFormStub::$oSession));
        self::assertFalse(SmsFlowFormStub::$oSession->exists(RememberMeCore::STAY_LOGGED_IN_REQUESTED));
    }

    public static function provideUnavailableChallenges(): array
    {
        return [[false], [true]];
    }

    #[DataProvider('provideActivationOutcomes')]
    public function testActivationAlwaysReturnsToNormalSignIn(bool $bActivated): void
    {
        SmsFlowModelStub::$bActivated = $bActivated;
        SmsFlowHttpStub::$aPost['verification_code'] = Verification::issueCode(SmsFlowFormStub::$oSession, '+12025550123');
        try {
            new VerificationFormProcess();
            self::fail('Expected sign-in page.');
        } catch (SmsFlowRedirect $oException) {
            self::assertSame('user/main/login', SmsFlowHeaderStub::$sUrl);
        }
        self::assertSame([42, '+12025550123'], SmsFlowModelStub::$aActivation);
        self::assertSame($bActivated, SmsFlowUserStub::$bCacheCleared);
        self::assertSame($bActivated, SmsFlowUserStub::$bInfoCacheCleared);
        self::assertFalse(SmsFlowFormStub::$oSession->exists('member_id'));
        self::assertFalse(SmsFlowFormStub::$oSession->exists(RememberMeCore::STAY_LOGGED_IN_REQUESTED));
        self::assertNull(SmsVerificationCore::getChallengeProfileId(SmsFlowFormStub::$oSession));
        self::assertFalse(SmsFlowFormStub::$oSession->exists(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
    }

    public static function provideActivationOutcomes(): array
    {
        return [[true], [false]];
    }

    public function testUnsentCodeCannotActivateAnAccount(): void
    {
        SmsFlowHttpStub::$aPost['verification_code'] = '123456';
        new VerificationFormProcess();
        self::assertSame([], SmsFlowModelStub::$aActivation);
        self::assertStringContainsString('invalid, expired', SmsFlowPfbcStub::$sError);
    }
}

class SmsFlowFormStub
{
    public static Session $oSession;
    protected Session $session;
    protected SmsFlowHttpStub $httpRequest;
    protected Config $config;

    public function __construct()
    {
        $this->session = self::$oSession;
        $this->httpRequest = new SmsFlowHttpStub();
        $this->config = Config::getInstance();
    }
}

final class SmsFlowHttpStub
{
    public static array $aPost;

    public function post(string $sName): mixed
    {
        return self::$aPost[$sName] ?? '';
    }
}

final class SmsFlowModelStub
{
    public static bool $bPending = true;
    public static bool $bActivated = true;
    public static array $aActivation = [];

    public function isPending(int $iProfileId): bool
    {
        return self::$bPending;
    }

    public function activate(int $iProfileId, string $sPhone): bool
    {
        self::$aActivation = [$iProfileId, $sPhone];

        return self::$bActivated;
    }
}

final class SmsFlowUserStub
{
    public static bool $bCacheCleared = false;
    public static bool $bInfoCacheCleared = false;

    public function clearReadProfileCache(int $iProfileId): void
    {
        self::$bCacheCleared = true;
    }

    public function clearInfoFieldCache(int $iProfileId): void
    {
        self::$bInfoCacheCleared = true;
    }
}

final class SmsFlowGatewayStub
{
    public static bool $bSuccess = true;
    public static bool $bThrow = false;
    public static int $iSends = 0;
    public static string $sPhone;
    public static string $sMessage;

    public static function create(string $sProvider): self
    {
        return new self();
    }

    public function send(string $sPhone, string $sMessage): bool
    {
        ++self::$iSends;
        self::$sPhone = $sPhone;
        self::$sMessage = $sMessage;
        if (self::$bThrow) {
            throw new \RuntimeException('provider-private-detail');
        }

        return self::$bSuccess;
    }
}

final class SmsFlowHeaderStub
{
    public static string $sUrl;

    public static function redirect(string $sUrl, ...$aArguments): void
    {
        self::$sUrl = $sUrl;
        throw new SmsFlowRedirect();
    }
}

final class SmsFlowUriStub
{
    public static function get(string ...$aParts): string
    {
        return implode('/', $aParts);
    }
}

final class SmsFlowRedirect extends \RuntimeException
{
}

final class SmsFlowPfbcStub
{
    public static string $sError = '';

    public static function setError(string $sForm, string $sMessage): void
    {
        self::$sError = $sMessage;
    }
}
