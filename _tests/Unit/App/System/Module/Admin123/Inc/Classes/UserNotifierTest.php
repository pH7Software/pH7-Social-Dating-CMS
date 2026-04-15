<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifier.php';
require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifierString.php';
require_once PH7_PATH_SYS_MOD . 'admin123/models/AdminModel.php';
require_once PH7_PATH_SYS_MOD . 'admin123/forms/processing/LoginFormProcess.php';

use PH7\AdminModel;
use PH7\LoginFormProcess;
use PH7\Framework\Error\CException\PH7RuntimeException;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Model\DbTableName;
use PH7\UserNotifier;
use Phake;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class UserNotifierTest extends TestCase
{
    private const VALID_EMAIL = 'hi@ph7.me';

    private UserNotifier $oUserNotifier;

    /** @var Mailable|Phake\IMock */
    private $oMailMock;

    /** @var Templatable|Phake\IMock */
    private $oViewMock;

    protected function setUp(): void
    {
        $this->oMailMock = Phake::mock(Mailable::class);
        $this->oViewMock = Phake::mock(Templatable::class);
        $this->oUserNotifier = new UserNotifier($this->oMailMock, $this->oViewMock);
    }

    public function testSendApprovedContent(): void
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->approvedContent()
            ->send();

        $this->assertSendMethodsCalled();
    }

    public function testSendDisapprovedContent(): void
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->disapprovedContent()
            ->send();

        $this->assertSendMethodsCalled();
    }

    public function testThrowsExceptionWhenContentStatusIsMissing(): void
    {
        $this->expectException(PH7RuntimeException::class);

        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->send();
    }

    public function testThrowsExceptionWhenEmailIsNull(): void
    {
        $this->expectException(\TypeError::class);

        $this->oUserNotifier
            ->setUserEmail(null)
            ->approvedContent()
            ->send();
    }

    public function testThrowsExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidEmailException::class);

        $this->oUserNotifier
            ->setUserEmail('pierrehenry.be')
            ->approvedContent()
            ->send();
    }

    private function assertSendMethodsCalled(): void
    {
        Phake::inOrder(
            Phake::verify($this->oViewMock)->parseMail(
                PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . UserNotifier::MAIL_TEMPLATE_FILE,
                self::VALID_EMAIL
            ),
            Phake::verify($this->oMailMock)->send(Phake::anyParameters())
        );
    }
}

final class AdminLoginFormProcessTest extends TestCase
{
    public function testUpdatePwdHashIfNeededUpdatesOutdatedAdminHash(): void
    {
        $oAdminModelMock = Phake::mock(AdminModel::class);
        $oReflection = new ReflectionClass(LoginFormProcess::class);

        /** @var LoginFormProcess $oLoginFormProcess */
        $oLoginFormProcess = $oReflection->newInstanceWithoutConstructor();

        $oAdminModelProp = $oReflection->getProperty('oAdminModel');
        $oAdminModelProp->setAccessible(true);
        $oAdminModelProp->setValue($oLoginFormProcess, $oAdminModelMock);

        $sOutdatedHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);
        $oLoginFormProcess->updatePwdHashIfNeeded('password', (string)$sOutdatedHash, 'admin@ph7.me');

        Phake::verify($oAdminModelMock)->changePassword(
            'admin@ph7.me',
            Phake::type('string'),
            DbTableName::ADMIN
        );
    }
}
