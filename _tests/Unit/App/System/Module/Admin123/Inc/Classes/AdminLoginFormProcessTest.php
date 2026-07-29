<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/models/AdminModel.php';
require_once PH7_PATH_SYS_MOD . 'admin123/forms/processing/LoginFormProcess.php';

use PH7\AdminModel;
use PH7\DbTableName;
use PH7\LoginFormProcess;
use Phake;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class AdminLoginFormProcessTest extends TestCase
{
    public function testUpdatePwdHashIfNeededUpdatesOutdatedAdminHash(): void
    {
        $oAdminModelMock = Phake::mock(AdminModel::class);
        $oReflection = new ReflectionClass(LoginFormProcess::class);

        /** @var LoginFormProcess $oLoginFormProcess */
        $oLoginFormProcess = $oReflection->newInstanceWithoutConstructor();

        $oAdminModelProp = $oReflection->getProperty('oAdminModel');
        $oAdminModelProp->setValue($oLoginFormProcess, $oAdminModelMock);

        $sOutdatedHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]);
        $oLoginFormProcess->updatePwdHashIfNeeded('password', (string)$sOutdatedHash, 'admin@ph7.me');

        $sPasswordSentToChangePassword = '';
        Phake::verify($oAdminModelMock)->changePassword(
            'admin@ph7.me',
            Phake::capture($sPasswordSentToChangePassword),
            DbTableName::ADMIN
        );
        $this->assertSame('password', $sPasswordSentToChangePassword);
    }
}
