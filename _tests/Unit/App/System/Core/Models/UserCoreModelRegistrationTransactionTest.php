<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Models;

use PHPUnit\Framework\TestCase;

final class UserCoreModelRegistrationTransactionTest extends TestCase
{
    private const REPOSITORY_ROOT = __DIR__ . '/../../../../../..';

    public function testSharedRegistrationBoundaryOwnsOnlyOutermostTransaction(): void
    {
        $sModel = $this->readRepositoryFile(
            '_protected/app/system/core/models/UserCoreModel.php'
        );

        $this->assertStringContainsString('$bOwnsTransaction = !$oDb->inTransaction()', $sModel);
        $this->assertStringContainsString('if ($bOwnsTransaction && !$oDb->beginTransaction())', $sModel);
        $this->assertStringContainsString('if ($bOwnsTransaction && !$oDb->commit())', $sModel);
        $this->assertStringContainsString('if ($bOwnsTransaction && $oDb->inTransaction())', $sModel);
        $this->assertStringContainsString("\$this->setKeyId('0');", $sModel);
        $this->assertStringNotContainsString('$bTransactionStarted', $sModel);
    }

    public function testCoreAndFrontendMemberRegistrationsUseTheSharedBoundary(): void
    {
        $sCoreModel = $this->readRepositoryFile(
            '_protected/app/system/core/models/UserCoreModel.php'
        );
        $sFrontendModel = $this->readRepositoryFile(
            '_protected/app/system/modules/user/models/UserModel.php'
        );

        $this->assertStringContainsString('return $this->runRegistrationTransaction(', $sCoreModel);
        $this->assertStringContainsString('!$this->setInfoFields($aData)', $sCoreModel);
        $this->assertStringContainsString('!$this->setDefaultPrivacySetting()', $sCoreModel);
        $this->assertStringContainsString('!$this->setDefaultNotification()', $sCoreModel);
        $this->assertStringContainsString('|| !$this->updateMembership(', $sCoreModel);

        $this->assertStringContainsString('return $this->runRegistrationTransaction(', $sFrontendModel);
        $this->assertStringContainsString('!$this->setInfoFields([])', $sFrontendModel);
        $this->assertStringContainsString('!$this->setDefaultPrivacySetting()', $sFrontendModel);
        $this->assertStringContainsString('!$this->setDefaultNotification()', $sFrontendModel);
        $this->assertStringContainsString('|| !$this->updateMembership(', $sFrontendModel);
    }

    public function testCoreAndFrontendAffiliateRegistrationsUseTheSharedBoundary(): void
    {
        $sCoreModel = $this->readRepositoryFile(
            '_protected/app/system/core/models/AffiliateCoreModel.php'
        );
        $sFrontendModel = $this->readRepositoryFile(
            '_protected/app/system/modules/affiliate/models/AffiliateModel.php'
        );

        $this->assertStringContainsString('return $this->runRegistrationTransaction(', $sCoreModel);
        $this->assertStringContainsString('if (!$this->setInfoFields($aData))', $sCoreModel);
        $this->assertStringContainsString('return $this->runRegistrationTransaction(', $sFrontendModel);
        $this->assertStringContainsString('if (!$this->join2($aData))', $sFrontendModel);
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(self::REPOSITORY_ROOT . '/' . $sPath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
