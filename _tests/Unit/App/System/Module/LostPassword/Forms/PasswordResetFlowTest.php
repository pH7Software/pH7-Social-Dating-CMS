<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\LostPassword\Forms;

use PH7\ForgotPasswordFormProcess;
use PH7\Framework\Session\Session;
use PH7\MainController;
use PH7\ResetPasswordForm;
use PH7\ResetPasswordFormProcess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class PasswordResetFlowTest extends TestCase
{
    private string $sSessionPath;

    protected function setUp(): void
    {
        class_alias(ResetControllerStub::class, 'PH7\\Controller');
        class_alias(ResetFormStub::class, 'PH7\\Form');
        class_alias(ResetModelStub::class, 'PH7\\PasswordResetModel');
        class_alias(ResetUserStub::class, 'PH7\\UserCore');
        class_alias(ResetUserModelStub::class, 'PH7\\UserCoreModel');
        class_alias(ResetMailStub::class, 'PH7\\Framework\\Mail\\Mail');
        class_alias(ResetHeaderStub::class, 'PH7\\Framework\\Url\\Header');
        class_alias(ResetUriStub::class, 'PH7\\Framework\\Mvc\\Router\\Uri');
        class_alias(ResetConfigStub::class, 'PH7\\Framework\\Mvc\\Model\\DbConfig');
        require PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';
        require PH7_PATH_SYS_MOD . 'lost-password/controllers/MainController.php';
        require PH7_PATH_SYS_MOD . 'lost-password/forms/ResetPasswordForm.php';
        require PH7_PATH_SYS_MOD . 'lost-password/forms/processing/ResetPasswordFormProcess.php';
        require PH7_PATH_SYS_MOD . 'lost-password/forms/processing/ForgotPasswordFormProcess.php';
        $this->sSessionPath = sys_get_temp_dir() . '/ph7-reset-session-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->sSessionPath, 0700));
        session_save_path($this->sSessionPath);
        ResetFormStub::$oSession = new Session();
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'Password reset regression test';
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            ResetFormStub::$oSession->destroy();
        }
        foreach (glob($this->sSessionPath . '/*') as $sFile) {
            unlink($sFile);
        }
        rmdir($this->sSessionPath);
    }

    public function testOpeningTheLinkOnlyDisplaysTheForm(): void
    {
        $oController = new MainController();
        $oController->reset('user', 'owner@example.test', 'test-token');
        self::assertTrue($oController->bOutput);
        self::assertSame('user', $oController->view->reset_mod);
        self::assertSame('owner@example.test', $oController->view->reset_email);
        self::assertSame('test-token', $oController->view->reset_token);
        self::assertSame(['owner@example.test', 'test-token', 'members'], ResetModelStub::$aValidation);
        self::assertSame([], ResetModelStub::$aReset);
    }

    public function testInvalidLinkOffersANewRequestForTheSameAccountType(): void
    {
        ResetModelStub::$bValid = false;
        try {
            (new MainController())->reset('affiliate', 'owner@example.test', 'expired');
            self::fail('Expected a new reset request.');
        } catch (ResetRedirect $oException) {
            self::assertSame('lost-password/main/forgot/affiliate', ResetHeaderStub::$sUrl);
            self::assertStringContainsString('invalid or expired', ResetHeaderStub::$sMessage);
        }
        self::assertSame([], ResetModelStub::$aReset);
    }

    public function testFormRendersCsrfAndPasswordManagerFriendlyInputs(): void
    {
        $sHtml = $this->renderForm();
        self::assertSame(2, substr_count($sHtml, 'type="password"'));
        self::assertSame(2, substr_count($sHtml, 'autocomplete="new-password"'));
        self::assertStringContainsString('name="security_token"', $sHtml);
        self::assertStringContainsString('Save new password', $sHtml);
        self::assertStringNotContainsString('owner@example.test', $sHtml);
        self::assertStringNotContainsString('test-token', $sHtml);
    }

    #[DataProvider('provideRejectedSubmissions')]
    public function testCsrfAndLengthValidationPreventPasswordUpdates(bool $bValidCsrf, string $sPassword): void
    {
        $sHtml = $this->renderForm();
        preg_match('/name="security_token"[^>]*value="([^"]+)"/', $sHtml, $aMatches);
        self::assertNotEmpty($aMatches[1]);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit_reset_password' => 'another_form',
            'security_token' => $bValidCsrf ? $aMatches[1] : 'invalid',
            'new_password' => $sPassword,
            'new_password2' => $sPassword
        ];
        try {
            ResetPasswordForm::display('user', 'owner@example.test', 'test-token');
            self::fail('Expected the form redirect.');
        } catch (ResetRedirect $oException) {
            self::assertSame([], ResetModelStub::$aReset);
        }
        self::assertNotEmpty($_SESSION['pfbc']['form_reset_password']['errors']);
    }

    public static function provideRejectedSubmissions(): array
    {
        return [[false, 'new-password'], [true, 'short'], [true, str_repeat('a', 65)]];
    }

    #[DataProvider('provideAccounts')]
    public function testSuccessfulPostReturnsToNormalSignIn(string $sMod, string $sTable, string $sLoginUrl): void
    {
        $sHtml = $this->renderForm();
        preg_match('/name="security_token"[^>]*value="([^"]+)"/', $sHtml, $aMatches);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'submit_reset_password' => 'form_reset_password',
            'security_token' => $aMatches[1],
            'new_password' => 'new<password>&',
            'new_password2' => 'new<password>&'
        ];
        try {
            ResetPasswordForm::display($sMod, 'owner@example.test', 'test-token');
            self::fail('Expected normal sign-in.');
        } catch (ResetRedirect $oException) {
            self::assertSame($sLoginUrl, ResetHeaderStub::$sUrl, json_encode($_SESSION['pfbc']['form_reset_password']['errors'] ?? []));
        }
        self::assertSame(['owner@example.test', 'test-token', 'new<password>&', $sTable], ResetModelStub::$aReset);
        self::assertSame([42, $sTable], ResetUserStub::$aClearedCache);
        self::assertArrayNotHasKey('member_id', $_SESSION);
        self::assertArrayNotHasKey('affiliate_id', $_SESSION);
        self::assertArrayNotHasKey('admin_id', $_SESSION);
    }

    public static function provideAccounts(): array
    {
        return [
            ['user', 'members', 'user/main/index'],
            ['affiliate', 'affiliates', 'affiliate/home/login'],
            ['admin123', 'admins', 'admin123/main/login']
        ];
    }

    public function testMismatchLeavesThePasswordUnchanged(): void
    {
        $_POST = ['new_password' => 'new-password', 'new_password2' => 'different-password'];
        new ResetPasswordFormProcess('user', 'owner@example.test', 'test-token');
        self::assertSame([], ResetModelStub::$aReset);
        self::assertNotEmpty($_SESSION['pfbc']['form_reset_password']['errors']);
    }

    public function testConcurrentConsumptionDoesNotReportSuccess(): void
    {
        ResetModelStub::$iProfileId = null;
        $_POST = ['new_password' => 'new-password', 'new_password2' => 'new-password'];
        new ResetPasswordFormProcess('user', 'owner@example.test', 'test-token');
        self::assertSame([], ResetUserStub::$aClearedCache);
        self::assertNotEmpty($_SESSION['pfbc']['form_reset_password']['errors']);
    }

    public function testEmailContainsTheLinkButNotTheStoredDigest(): void
    {
        $_POST = ['mail' => 'owner@example.test'];
        new ForgotPasswordFormProcess('members');
        self::assertStringContainsString('/owner@example.test/test-token', ResetMailStub::$sHtml);
        self::assertStringNotContainsString('stored-digest', ResetMailStub::$sHtml);
        self::assertStringNotContainsString('<img', ResetMailStub::$sHtml);
        self::assertStringContainsString('expires in one hour', ResetMailStub::$sHtml);
        self::assertSame([], ResetModelStub::$aReset);
    }

    public function testDeliveryFailureDoesNotChangeThePassword(): void
    {
        ResetMailStub::$bDelivered = false;
        $_POST = ['mail' => 'owner@example.test'];
        new ForgotPasswordFormProcess('members');
        self::assertSame([], ResetModelStub::$aReset);
        self::assertNotEmpty($_SESSION['pfbc']['form_forgot_password']['errors']);
    }

    #[DataProvider('provideMalformedEmails')]
    public function testMalformedEmailDoesNotReachTheAccountModel(mixed $mEmail): void
    {
        $_POST = ['mail' => $mEmail];
        new ForgotPasswordFormProcess('members');
        self::assertFalse(isset(ResetMailStub::$sHtml));
        self::assertNotEmpty($_SESSION['pfbc']['form_forgot_password']['errors']);
    }

    public static function provideMalformedEmails(): array
    {
        return [[null], [''], [['owner@example.test']]];
    }

    public function testForgotFormCannotSelectAnotherFormsValidation(): void
    {
        $sCode = file_get_contents(PH7_PATH_SYS_MOD . 'lost-password/forms/ForgotPasswordForm.php');
        self::assertStringContainsString("\\PFBC\\Form::isValid('form_forgot_password')", $sCode);
        self::assertStringNotContainsString("\\PFBC\\Form::isValid(\$_POST[", $sCode);
    }

    private function renderForm(): string
    {
        ob_start();
        ResetPasswordForm::display('user', 'owner@example.test', 'test-token');

        return ob_get_clean();
    }
}

class ResetControllerStub
{
    public \stdClass $view;
    public \stdClass $registry;
    public bool $bOutput = false;

    public function __construct()
    {
        $this->view = new \stdClass();
        $this->registry = (object)['site_url' => 'https://example.test/'];
    }

    protected function output(): void
    {
        $this->bOutput = true;
    }
}

class ResetFormStub
{
    public static Session $oSession;
    protected Session $session;
    protected ResetHttpStub $httpRequest;
    protected ResetMailViewStub $view;
    protected ResetDesignStub $design;

    public function __construct()
    {
        $this->session = self::$oSession;
        $this->httpRequest = new ResetHttpStub();
        $this->view = new ResetMailViewStub();
        $this->design = new ResetDesignStub();
    }

    public static function errorSendingEmail(): string
    {
        return 'Unable to send email. Please try again.';
    }
}

final class ResetHttpStub
{
    public function post(string $sName, mixed $mClean = null): mixed
    {
        return $_POST[$sName] ?? '';
    }

    public function get(string $sName): string
    {
        return 'user';
    }
}

final class ResetModelStub
{
    public static bool $bValid = true;
    public static ?int $iProfileId = 42;
    public static array $aValidation = [];
    public static array $aReset = [];

    public function isValid(string ...$aArguments): bool
    {
        self::$aValidation = $aArguments;

        return self::$bValid;
    }

    public function reset(string ...$aArguments): ?int
    {
        self::$aReset = $aArguments;

        return self::$iProfileId;
    }

    public function issue(string $sEmail, string $sTable): string
    {
        return 'test-token';
    }
}

final class ResetUserModelStub
{
    public function getId(string $sEmail, mixed $mUsername, string $sTable): int
    {
        return 42;
    }

    public function readProfile(int $iProfileId, string $sTable): \stdClass
    {
        return (object)['email' => 'owner@example.test', 'username' => '<img src=x>', 'hashValidation' => 'stored-digest'];
    }
}

final class ResetMailViewStub
{
    public string $content;

    public function parseMail(string $sTemplate, string $sEmail): string
    {
        return $this->content;
    }
}

final class ResetDesignStub
{
    public function ip(mixed $mIp, bool $bLink): string
    {
        return '127.0.0.1';
    }
}

final class ResetMailStub
{
    public static bool $bDelivered = true;
    public static string $sHtml;

    public function send(array $aInfo, string $sHtml): bool
    {
        self::$sHtml = $sHtml;

        return self::$bDelivered;
    }
}

final class ResetUserStub
{
    public static array $aClearedCache = [];

    public function clearReadProfileCache(int $iProfileId, string $sTable): void
    {
        self::$aClearedCache = [$iProfileId, $sTable];
    }
}

final class ResetHeaderStub
{
    public static ?string $sUrl;
    public static ?string $sMessage;

    public static function redirect(?string $sUrl = null, ?string $sMessage = null, ...$aArguments): void
    {
        self::$sUrl = $sUrl;
        self::$sMessage = $sMessage;
        throw new ResetRedirect();
    }
}

final class ResetUriStub
{
    public static function get(string ...$aParts): string
    {
        return implode('/', $aParts);
    }
}

final class ResetConfigStub
{
    public static function getSetting(string $sName): int
    {
        return $sName === 'maxPasswordLength' ? 64 : 8;
    }
}

final class ResetRedirect extends \RuntimeException
{
}
