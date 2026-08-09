<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

require_once PH7_PATH_SYS_MOD . 'forum/inc/class/FormHelper.php';

use PFBC\Element\Textarea;
use PH7\FormHelper;
use PHPUnit\Framework\TestCase;

final class LegacyEditorRetirementWiringTest extends TestCase
{
    public function testForumAlwaysUsesPlainTextareaWithoutReadingLegacySetting(): void
    {
        $this->assertSame(Textarea::class, FormHelper::getEditorPfbcClassName());

        $sSource = $this->readProjectFile('_protected/app/system/modules/forum/inc/class/FormHelper.php');
        $this->assertStringNotContainsString('wysiwygEditorForum', $sSource);
        $this->assertStringNotContainsString('CKEditor', $sSource);
    }

    public function testDynamicEditorFieldsMapToPlainTextarea(): void
    {
        $sSource = $this->readProjectFile('_protected/app/system/core/forms/DynamicFieldCoreForm.php');
        $iEditorBranch = strpos($sSource, "strstr(\$this->sColumn, 'editor')");

        $this->assertNotFalse($iEditorBranch);
        $this->assertStringContainsString("\$sType = 'Textarea';", substr($sSource, $iEditorBranch, 120));
    }

    public function testAdminNoLongerOffersOrUpdatesLegacyForumToggle(): void
    {
        $sForm = $this->readProjectFile('_protected/app/system/modules/admin123/forms/SettingForm.php');
        $sProcess = $this->readProjectFile('_protected/app/system/modules/admin123/forms/processing/SettingFormProcess.php');

        $this->assertStringNotContainsString('wysiwyg_editor_forum', $sForm);
        $this->assertStringNotContainsString('wysiwyg_editor_forum', $sProcess);
    }

    public function testLegacyPublicEditorBundlesAreAbsent(): void
    {
        $sProjectRoot = dirname(__DIR__, 6);

        $this->assertDirectoryDoesNotExist($sProjectRoot . '/static/PFBC/ckeditor');
        $this->assertDirectoryDoesNotExist($sProjectRoot . '/static/PFBC/tiny_mce');
    }

    private function readProjectFile(string $sPath): string
    {
        $sContents = file_get_contents(dirname(__DIR__, 6) . '/' . $sPath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
