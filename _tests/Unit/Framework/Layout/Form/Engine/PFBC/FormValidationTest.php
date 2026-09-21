<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC;

use PFBC\Element\Checkbox;
use PFBC\Element\Textbox;
use PFBC\Form;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

final class FormValidationTest extends TestCase
{
    private array $aPost;
    private array $aSession;
    private array $aServer;

    protected function setUp(): void
    {
        $this->aPost = $_POST;
        $this->aSession = $_SESSION ?? [];
        $this->aServer = $_SERVER;
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void
    {
        $_POST = $this->aPost;
        $_SESSION = $this->aSession;
        $_SERVER = $this->aServer;
    }

    #[DataProvider('malformedIds')]
    public function testInvalidFormIdentifiersAreRejected(mixed $mId): void
    {
        self::assertFalse(Form::isValid($mId));
    }

    public static function malformedIds(): array
    {
        return [[[]], [['form']], [null], [false], [42], ['']];
    }

    #[DataProvider('malformedValues')]
    public function testMalformedTextValuesAreRejected(mixed $mValue): void
    {
        $oForm = new Form('text_validation');
        $oForm->addElement(new Textbox('Name', 'name', ['required' => 1]));
        $oForm->render(true);
        $_POST['name'] = $mValue;

        self::assertFalse(Form::isValid('text_validation'));
        self::assertArrayNotHasKey('name', Form::getSessionValues('text_validation'));
    }

    public static function malformedValues(): array
    {
        return [[[]], [['value']], [[['nested']]], [42], [false]];
    }

    public function testMultipleValuesKeepTheirSubmittedKeys(): void
    {
        $oForm = new Form('multiple_validation');
        $oForm->addElement(new Checkbox('Interests', 'interests', ['music' => 'Music', 'travel' => 'Travel']));
        $oForm->render(true);
        $_POST['interests'] = [2 => 'music', 5 => 'travel'];

        self::assertTrue(Form::isValid('multiple_validation', false));
        self::assertSame($_POST['interests'], Form::getSessionValues('multiple_validation')['interests']);
    }

    public function testNestedMultipleValuesAreRejected(): void
    {
        $oForm = new Form('nested_validation');
        $oForm->addElement(new Checkbox('Interests', 'interests', ['music' => 'Music']));
        $oForm->render(true);
        $_POST['interests'] = [['music']];

        self::assertFalse(Form::isValid('nested_validation'));
    }

    public function testExpectedFormRetainsItsValidationRules(): void
    {
        $oForm = new Form('expected_validation');
        $oForm->addElement(new Textbox('Name', 'name', ['required' => 1]));
        $oForm->render(true);
        (new Form('other_validation'))->render(true);
        $_POST['submit_form'] = 'other_validation';

        self::assertFalse(Form::isValid('expected_validation'));
        $_POST['name'] = 'Valid name';
        self::assertTrue(Form::isValid('expected_validation'));
    }
}
