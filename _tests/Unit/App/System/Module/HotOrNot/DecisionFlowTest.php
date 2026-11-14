<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\HotOrNot;

use PHPUnit\Framework\TestCase;

final class DecisionFlowTest extends TestCase
{
    public function testDecisionActionsFollowTheExpectedLeftAndRightPattern(): void
    {
        $sTemplate = file_get_contents(
            PH7_PATH_SYS_MOD . 'hotornot/views/base/tpl/main/yes_no_buttons.inc.tpl'
        );
        $this->assertIsString($sTemplate);

        $iSkipPosition = strpos($sTemplate, 'id="hotornot_skip"');
        $iInterestedPosition = strpos($sTemplate, 'id="hotornot_interested"');
        $this->assertIsInt($iSkipPosition);
        $this->assertIsInt($iInterestedPosition);
        $this->assertLessThan($iInterestedPosition, $iSkipPosition);
        $this->assertStringContainsString('aria-keyshortcuts="ArrowLeft"', $sTemplate);
        $this->assertStringContainsString('aria-keyshortcuts="ArrowRight"', $sTemplate);
        $this->assertStringContainsString('role="group"', $sTemplate);
    }

    public function testKeyboardDecisionsIgnoreEditableOrModifiedInput(): void
    {
        $sScript = file_get_contents(
            dirname(PH7_PATH_PROTECTED) . '/templates/system/modules/hotornot/themes/base/js/script.js'
        );
        $this->assertIsString($sScript);

        $this->assertStringContainsString("on('keydown.ph7HotOrNot'", $sScript);
        $this->assertStringContainsString('oEvent.altKey || oEvent.ctrlKey || oEvent.metaKey || oEvent.shiftKey', $sScript);
        $this->assertStringContainsString("is('a, input, textarea, select, button')", $sScript);
        $this->assertStringContainsString("closest('[contenteditable]')", $sScript);
        $this->assertStringContainsString("oEvent.key === 'ArrowLeft'", $sScript);
        $this->assertStringContainsString("oEvent.key === 'ArrowRight'", $sScript);
        $this->assertStringContainsString('oEvent.preventDefault();', $sScript);
        $this->assertStringContainsString('oAction.click();', $sScript);
    }
}
