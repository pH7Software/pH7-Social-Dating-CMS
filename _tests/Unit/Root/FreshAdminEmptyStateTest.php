<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class FreshAdminEmptyStateTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testFreshAdminListsRenderUsefulEmptyStatesInsteadOfNotFoundPages(): void
    {
        $aControllers = [
            '_protected/app/system/modules/mail/controllers/AdminController.php',
            '_protected/app/system/modules/admin123/controllers/UserController.php',
            '_protected/app/system/modules/affiliate/controllers/AdminController.php',
            '_protected/app/system/modules/newsletter/controllers/AdminController.php'
        ];

        foreach ($aControllers as $sControllerPath) {
            $sController = $this->readFile($sControllerPath);

            self::assertStringNotContainsString('setNotFoundPage()', $sController, $sControllerPath);
        }

        self::assertStringContainsString(
            'No member messages yet. New conversations will appear here.',
            $this->readFile($aControllers[0])
        );
        self::assertStringContainsString(
            '$this->view->browse = $oBrowse;',
            $this->readFile($aControllers[1])
        );
        self::assertStringContainsString(
            'No affiliates yet. New affiliate registrations will appear here.',
            $this->readFile($aControllers[2])
        );
        self::assertStringContainsString(
            'No subscribers yet. Newsletter signups will appear here.',
            $this->readFile($aControllers[3])
        );
    }

    public function testFreshAdminListTemplatesProvideAnActionableEmptyState(): void
    {
        $aTemplates = [
            '_protected/app/system/modules/admin123/views/base/tpl/user/browse.tpl',
            '_protected/app/system/modules/affiliate/views/base/tpl/admin/browse.tpl',
            '_protected/app/system/modules/newsletter/views/base/tpl/admin/browse.tpl'
        ];

        foreach ($aTemplates as $sTemplatePath) {
            $sTemplate = $this->readFile($sTemplatePath);

            self::assertStringContainsString('{if empty($browse)}', $sTemplate, $sTemplatePath);
            self::assertStringContainsString('alert alert-info center', $sTemplate, $sTemplatePath);
            self::assertStringContainsString('btn btn-primary', $sTemplate, $sTemplatePath);
        }
    }

    private function readFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . '/' . $sRelativePath);

        self::assertIsString($sContents);

        return $sContents;
    }
}
