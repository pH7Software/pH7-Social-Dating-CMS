<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC / Element
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Element;

// PFBC registers its own spl_autoload_register in Form.class.php
require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Element\Color;
use PFBC\Element\Number;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Validation\Str;
use PHPUnit\Framework\TestCase;

final class TextboxMaxLengthTest extends TestCase
{
    public function testTextboxDerivesMaxLengthFromStrValidator(): void
    {
        $sHtml = $this->render(new Textbox('Subject', 'subject', ['id' => 's', 'validation' => new Str(2, 45)]));

        $this->assertStringContainsString('maxlength="45"', $sHtml);
    }

    public function testExplicitMaxLengthIsNotOverridden(): void
    {
        $sHtml = $this->render(new Textbox('Subject', 'subject', ['id' => 's', 'maxlength' => 20, 'validation' => new Str(2, 45)]));

        $this->assertStringContainsString('maxlength="20"', $sHtml);
        $this->assertStringNotContainsString('maxlength="45"', $sHtml);
    }

    public function testTextboxWithoutStrValidatorHasNoMaxLength(): void
    {
        $sHtml = $this->render(new Textbox('Nickname', 'nickname', ['id' => 'n']));

        $this->assertStringNotContainsString('maxlength', $sHtml);
    }

    public function testNumberFieldDoesNotGetMaxLength(): void
    {
        // maxlength is invalid on <input type="number">; the Str max must not leak onto it
        $sHtml = $this->render(new Number('Age', 'age', ['id' => 'a', 'validation' => new Str(2, 45)]));

        $this->assertStringNotContainsString('maxlength', $sHtml);
    }

    public function testColorFieldDoesNotGetMaxLength(): void
    {
        $sHtml = $this->render(new Color('Background', 'bg', ['id' => 'c', 'validation' => new Str(2, 45)]));

        $this->assertStringNotContainsString('maxlength', $sHtml);
    }

    public function testTextareaDerivesMaxLengthAndShowsItInTheCounter(): void
    {
        $sHtml = $this->render(new Textarea('Message', 'message', ['id' => 'm', 'validation' => new Str(10, 2000)]));

        $this->assertStringContainsString('maxlength="2000"', $sHtml);
        $this->assertStringContainsString('/ 2000', $sHtml); // the "N / MAX" counter suffix
    }

    private function render($oElement): string
    {
        ob_start();
        $oElement->render();

        return ob_get_clean();
    }
}
