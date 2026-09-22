<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Forms
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

use PFBC\Form;
use PH7\DynamicFieldCoreForm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';
require_once PH7_PATH_APP . 'includes/classes/HtmlForm.php';
require_once PH7_PATH_SYS . 'core/forms/DynamicFieldCoreForm.php';

final class DynamicFieldCoreFormTest extends TestCase
{
    /**
     * Affiliates sign up without a description, yet their edit form required one, so they
     * could not save any other change until they wrote it.
     */
    #[DataProvider('descriptionRequirements')]
    public function testEmptyDescriptionIsOnlyRejectedWhenRequired(bool $bIsRequired): void
    {
        $this->submitDescription($bIsRequired, '');

        self::assertSame(!$bIsRequired, Form::isValid('dynamic_description'));
    }

    public static function descriptionRequirements(): array
    {
        return ['member description' => [true], 'affiliate description' => [false]];
    }

    public function testOptionalDescriptionKeepsItsLengthRule(): void
    {
        $this->submitDescription(false, 'Too short');

        self::assertFalse(Form::isValid('dynamic_description'));
    }

    private function submitDescription(bool $bIsRequired, string $sDescription): void
    {
        $oForm = new Form('dynamic_description');
        (new DynamicFieldCoreForm($oForm, 'description', '', $bIsRequired))->generate();
        $oForm->render(true);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['description' => $sDescription];
    }
}
