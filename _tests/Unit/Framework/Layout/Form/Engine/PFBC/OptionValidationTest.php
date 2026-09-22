<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC;

use PFBC\Element\Checkbox;
use PFBC\Element\Radio;
use PFBC\Element\Select;
use PFBC\Form;
use PFBC\Validation\Option;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

final class OptionValidationTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];
    }

    /**
     * Posting enable=on to the membership form reached an ENUM('1','0') column, which strict
     * MySQL rejected, so the admin got a server error instead of a validation message.
     */
    #[DataProvider('enableValues')]
    public function testRadioAcceptsOnlyItsOptions(string $sValue, bool $bIsValid): void
    {
        $oForm = new Form('option_radio');
        $oForm->addElement(new Radio('Enable', 'enable', ['1' => 'Yes', '0' => 'No']));
        $oForm->render(true);
        $_POST['enable'] = $sValue;

        self::assertSame($bIsValid, Form::isValid('option_radio'));
    }

    public static function enableValues(): array
    {
        return [
            'offered yes' => ['1', true],
            'offered no' => ['0', true],
            'nothing chosen' => ['', true],
            'crafted value' => ['on', false]
        ];
    }

    #[DataProvider('interestValues')]
    public function testEachValueOfAMultiValueElementMustBeAnOption(array $aValues, bool $bIsValid): void
    {
        $oForm = new Form('option_checkbox');
        $oForm->addElement(new Checkbox('Interests', 'interests', ['music' => 'Music', 'travel' => 'Travel']));
        $oForm->render(true);
        $_POST['interests'] = $aValues;

        self::assertSame($bIsValid, Form::isValid('option_checkbox'));
    }

    public static function interestValues(): array
    {
        return [
            'all offered' => [['music', 'travel'], true],
            'one crafted' => [['music', 'hacking'], false]
        ];
    }

    public function testNumericKeysAndLabelValuesAreCompared(): void
    {
        $oForm = new Form('option_select');
        $oForm->addElement(new Select('Height', 'height', [120 => '120 cm', 122 => '122 cm']));
        $oForm->addElement(new Select('Reason', 'reason', ['Too busy', "I'm leaving"]));
        $oForm->render(true);

        $_POST = ['height' => '122', 'reason' => "I'm leaving"];
        self::assertTrue(Form::isValid('option_select'));

        $_POST = ['height' => '121', 'reason' => "I'm leaving"];
        self::assertFalse(Form::isValid('option_select'));
    }

    public function testElementWithoutOptionsHasNothingToCheck(): void
    {
        self::assertTrue((new Option([]))->isValid('anything'));
        self::assertFalse((new Option(['1', '0']))->isValid(['1', ['0']]));
    }
}
