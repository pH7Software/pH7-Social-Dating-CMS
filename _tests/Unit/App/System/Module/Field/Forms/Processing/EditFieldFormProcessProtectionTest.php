<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Field\Forms\Processing;

use PHPUnit\Framework\TestCase;

final class EditFieldFormProcessProtectionTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../../../../../../..';

    public function testBothCurrentAndSubmittedProtectedNamesAreRejectedBeforeSchemaChange(): void
    {
        $sProcess = file_get_contents(
            self::PROJECT_ROOT . '/_protected/app/system/modules/field/forms/processing/EditFieldFormProcess.php'
        );

        $this->assertIsString($sProcess);

        $iCurrentNameCheck = strpos($sProcess, 'Field::unmodifiable($sMod, $sCurrentName)');
        $iSubmittedNameCheck = strpos($sProcess, 'Field::unmodifiable($sMod, $sName)');
        $iSchemaUpdate = strpos($sProcess, 'new FieldModel(');

        $this->assertNotFalse($iCurrentNameCheck);
        $this->assertNotFalse($iSubmittedNameCheck);
        $this->assertNotFalse($iSchemaUpdate);
        $this->assertLessThan($iSchemaUpdate, $iCurrentNameCheck);
        $this->assertLessThan($iSchemaUpdate, $iSubmittedNameCheck);
    }
}
