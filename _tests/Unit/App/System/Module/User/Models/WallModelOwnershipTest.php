<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\User\Models;

use PHPUnit\Framework\TestCase;

final class WallModelOwnershipTest extends TestCase
{
    private const REPOSITORY_ROOT = __DIR__ . '/../../../../../../..';

    public function testWallEditsRequireBothTheOwnerAndTheWallPost(): void
    {
        $sModel = $this->readRepositoryFile(
            '_protected/app/system/modules/user/models/WallModel.php'
        );

        $this->assertStringContainsString(
            'WHERE profileId = :profileId AND wallId = :wallId',
            $sModel
        );
        $this->assertStringContainsString("bindValue(':wallId', \$iWallId, \\PDO::PARAM_INT)", $sModel);
        $this->assertStringContainsString('$rStmt->rowCount() === 1', $sModel);
    }

    public function testEveryWallEditCallerPassesTheRequestedWallPost(): void
    {
        $sAjax = $this->readRepositoryFile(
            '_protected/app/system/modules/user/assets/ajax/WallAjax.php'
        );
        $sFormProcess = $this->readRepositoryFile(
            '_protected/app/system/modules/user/forms/processing/EditWallFormProcess.php'
        );
        $sJavascript = $this->readRepositoryFile('static/js/Wall.js');

        $this->assertStringContainsString("post('wall_id')", $sAjax);
        $this->assertStringContainsString("post('wall_id')", $sFormProcess);
        $this->assertStringContainsString("'wall_id': iWallId", $sJavascript);
    }

    public function testWallDeletionReportsOnlyAnOwnedDeletedPostAsSuccess(): void
    {
        $sModel = $this->readRepositoryFile(
            '_protected/app/system/modules/user/models/WallModel.php'
        );

        $this->assertStringContainsString(
            'WHERE profileId = :profileId AND wallId = :wallId',
            $sModel
        );
        $this->assertSame(2, substr_count($sModel, '$rStmt->rowCount() === 1'));
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(self::REPOSITORY_ROOT . '/' . $sPath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
