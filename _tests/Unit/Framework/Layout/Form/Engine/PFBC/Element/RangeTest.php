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

use PFBC\Element\Range;
use PFBC\Form;
use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase
{
    public function testCountersUseEachFieldsActualId(): void
    {
        $oForm = new Form('range_test');
        $oAge = new Range('Age', 'age', ['value' => 30, 'min' => 18, 'max' => 99]);
        $oDistance = new Range('Distance', 'distance', ['id' => 'distance', 'value' => 10]);
        $oForm->addElement($oAge);
        $oForm->addElement($oDistance);
        $sHtml = $oForm->render(true);

        $this->assertSame(2, substr_count($sHtml, 'type="range"'));
        $this->assertStringNotContainsString('type="text"', $sHtml);
        $this->assertStringContainsString('min="18" max="99"', $sHtml);
        foreach ([$oAge->getID(), $oDistance->getID()] as $sId) {
            $this->assertStringContainsString('id="' . $sId . '_output" for="' . $sId . '"', $sHtml);
            $this->assertStringContainsString('document.getElementById("' . $sId . '")', $sHtml);
            $this->assertStringContainsString('document.getElementById("' . $sId . '_output")', $sHtml);
        }
        $this->assertStringNotContainsString('rangeInput', $sHtml);
        $this->assertStringNotContainsString('rangeOutput', $sHtml);
    }
}
