<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

use PH7\ImportUser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/ImportUser.php';

final class ImportUserCsvCompatibilityTest extends TestCase
{
    /**
     * Each member must be built from their own row, not from the header row,
     * and an empty or absent column must fall back to the default value.
     */
    public function testRowValuesComeFromTheRowWithDefaultsForGaps(): void
    {
        $oReflection = new ReflectionClass(ImportUser::class);
        $oImport = $oReflection->newInstanceWithoutConstructor();
        $oReflection->getProperty('aFileData')->setValue($oImport, ['User_Name', 'E-mail', 'Country', 'City']);
        $oReflection->getProperty('aTmpData')->setValue(
            $oImport,
            ['username' => 'pH7Builder', 'email' => '', 'country' => 'US', 'city' => 'Virginia', 'state' => 'Doswell']
        );
        $oReflection->getMethod('setTmpData')->invoke($oImport);

        $oGetRowValue = $oReflection->getMethod('getRowValue');
        $fnValue = static fn(array $aRow, string $sType): string => $oGetRowValue->invoke($oImport, $aRow, $sType);
        $aRow = ['cara', 'cara@example.test', 'NZ', ''];

        $this->assertSame('cara', $fnValue($aRow, 'username'));
        $this->assertSame('NZ', $fnValue($aRow, 'country'));
        $this->assertSame('Virginia', $fnValue($aRow, 'city'), 'An empty cell uses the default, not its column.');
        $this->assertSame('Doswell', $fnValue($aRow, 'state'), 'A column the CSV lacks uses the default.');
    }

    public function testCsvReaderKeepsItsEscapeCharacterExplicit(): void
    {
        $sSource = file_get_contents(
            PH7_PATH_SYS_MOD . 'admin123/inc/class/ImportUser.php'
        );
        $sExplicitEscapeArgument = <<<'SOURCE'
$sEnclosure, '\\')
SOURCE;

        $this->assertIsString($sSource);
        $this->assertSame(2, substr_count($sSource, 'fgetcsv('));
        $this->assertSame(2, substr_count($sSource, $sExplicitEscapeArgument));
    }
}
