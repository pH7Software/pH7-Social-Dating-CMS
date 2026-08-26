<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

use PHPUnit\Framework\TestCase;

final class ImportUserCsvCompatibilityTest extends TestCase
{
    public function testCsvReaderKeepsItsEscapeCharacterExplicit(): void
    {
        $sSource = file_get_contents(
            dirname(__DIR__, 8) . '/_protected/app/system/modules/admin123/inc/class/ImportUser.php'
        );
        $sExplicitEscapeArgument = <<<'SOURCE'
$sEnclosure, '\\')
SOURCE;

        $this->assertIsString($sSource);
        $this->assertSame(2, substr_count($sSource, 'fgetcsv('));
        $this->assertSame(2, substr_count($sSource, $sExplicitEscapeArgument));
    }
}
