<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Element;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Element\CKEditor;
use PFBC\Element\Textarea;
use PFBC\Element\TinyMCE;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LegacyEditorRetirementTest extends TestCase
{
    #[DataProvider('legacyEditorProvider')]
    public function testLegacyEditorAliasRendersPlainRequiredTextarea(string $sClass): void
    {
        $oElement = new $sClass('Message', 'message', [
            'id' => 'message',
            'required' => 1,
            'value' => '<b>Stored text</b>'
        ]);

        $this->assertInstanceOf(Textarea::class, $oElement);
        $this->assertNull($oElement->getJSFiles());

        ob_start();
        $oElement->renderJS();
        $oElement->jQueryDocumentReady();
        $sEditorScript = (string)ob_get_clean();
        $this->assertSame('', $sEditorScript);

        ob_start();
        $oElement->render();
        $sHtml = (string)ob_get_clean();
        $this->assertStringContainsString('<textarea', $sHtml);
        $this->assertStringContainsString('required="required"', $sHtml);
        $this->assertStringNotContainsString('CKEDITOR', $sHtml);
        $this->assertStringNotContainsString('tinyMCE', $sHtml);
    }

    public static function legacyEditorProvider(): array
    {
        return [
            'CKEditor compatibility alias' => [CKEditor::class],
            'TinyMCE compatibility alias' => [TinyMCE::class]
        ];
    }
}
