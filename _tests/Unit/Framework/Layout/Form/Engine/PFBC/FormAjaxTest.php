<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Element\Button;
use PFBC\Form;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FormAjaxTest extends TestCase
{
    #[DataProvider('buttonProviders')]
    public function testAjaxRendersAHandlerWithRetryAfterFailure(bool $bUseWidget): void
    {
        $oForm = new Form('ajax_retry');
        $oForm->configure([
            'ajax' => true,
            'action' => '/submit',
            'prevent' => $bUseWidget ? [] : ['jQueryUIButtons']
        ]);
        $oForm->addElement(new Button('Save'));

        $sHtml = $oForm->render(true);

        $this->assertStringNotContainsString('.on("submit", {', $sHtml);
        $this->assertSame(2, substr_count($sHtml, '.on("submit", function() {'));
        $this->assertStringContainsString('error: function()', $sHtml);
        $this->assertStringContainsString('"role": "alert"', $sHtml);
        $this->assertStringContainsString('Unable to submit the form. Please try again.', $sHtml);

        $sComplete = substr($sHtml, strpos($sHtml, 'complete: function()'));
        if ($bUseWidget) {
            $this->assertStringContainsString('.button("enable")', $sComplete);
            $this->assertStringContainsString('.find("img.pfbc-loading").remove()', $sComplete);
        } else {
            $this->assertStringContainsString('.removeAttr("disabled")', $sComplete);
            $this->assertStringNotContainsString('.button(', $sHtml);
        }
    }

    public static function buttonProviders(): array
    {
        return ['jQuery UI' => [true], 'plain HTML' => [false]];
    }
}
