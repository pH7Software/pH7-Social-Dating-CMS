<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC / Element
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Element;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Element\Checksort;
use PHPUnit\Framework\TestCase;

final class ChecksortTest extends TestCase
{
    public function testOptionTextCannotInjectHtmlOrJavaScript(): void
    {
        $sPayload = 'Choice\');alert(document.domain);//<img src=x onerror=alert(1)>';
        $oElement = new Checksort(
            'Choices',
            'choices',
            ['choice' => $sPayload],
            ['id' => 'choices', 'value' => ['choice']]
        );

        ob_start();
        $oElement->render();
        $sHtml = ob_get_clean();

        $this->assertIsString($sHtml);
        $this->assertStringContainsString('updateChecksort(this, &quot;Choice\\u0027);alert', $sHtml);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $sHtml);
        $this->assertStringNotContainsString('<img src=x', $sHtml);
        $this->assertStringNotContainsString("Choice');alert", $sHtml);
    }

    public function testDynamicListUsesDomTextNodeInsteadOfHtmlConcatenation(): void
    {
        $oElement = new Checksort('Choices', 'choices', ['choice' => 'Choice'], ['id' => 'choices']);

        ob_start();
        $oElement->renderJS();
        $sJavaScript = ob_get_clean();

        $this->assertIsString($sJavaScript);
        $this->assertStringContainsString('document.createTextNode(text)', $sJavaScript);
        $this->assertStringNotContainsString("+ text +", $sJavaScript);
    }
}
