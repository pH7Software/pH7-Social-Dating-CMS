<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / App / System / Module / Comment
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Comment;

use PHPUnit\Framework\TestCase;

final class CommentMutationSafetyTest extends TestCase
{
    private const REPOSITORY_ROOT = __DIR__ . '/../../../../../..';

    public function testCommentEditsRequireAuthenticationAndTheRequestedIdentity(): void
    {
        $sPermission = $this->readRepositoryFile(
            '_protected/app/system/modules/comment/config/Permission.php'
        );
        $sController = $this->readRepositoryFile(
            '_protected/app/system/modules/comment/controllers/CommentController.php'
        );

        self::assertStringContainsString("['add', 'edit', 'delete']", $sPermission);
        self::assertStringContainsString('if (!$this->isRequestedComment($oComment))', $sController);
        self::assertStringContainsString("Uri::get('error', 'http', 'index', '404')", $sController);
        self::assertStringContainsString("Uri::get('error', 'http', 'index', '403')", $sController);
    }

    public function testCommentMutationSuccessRequiresOneAffectedRow(): void
    {
        $sModel = $this->readRepositoryFile(
            '_protected/app/system/modules/comment/models/CommentModel.php'
        );

        self::assertSame(2, substr_count($sModel, '$rStmt->rowCount() === 1'));
    }

    public function testAdministratorsCanUseTheEditActionShownInTheInterface(): void
    {
        $sProcess = $this->readRepositoryFile(
            '_protected/app/system/modules/comment/forms/processing/EditCommentFormProcess.php'
        );

        self::assertStringContainsString('return AdminCore::auth()', $sProcess);
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(self::REPOSITORY_ROOT . '/' . $sPath);

        self::assertIsString($sContents);

        return $sContents;
    }
}
