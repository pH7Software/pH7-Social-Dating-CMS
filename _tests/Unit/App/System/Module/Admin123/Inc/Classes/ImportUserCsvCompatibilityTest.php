<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

use PH7\MemberMigrationCsv;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/MemberMigrationCsv.php';

final class ImportUserCsvCompatibilityTest extends TestCase
{
    /**
     * Each member must be built from their own row, not from the header row,
     * and an empty or absent column must fall back to the default value.
     */
    public function testRowValuesComeFromTheRowWithDefaultsForGaps(): void
    {
        $oCsv = new MemberMigrationCsv();
        $aColumns = $oCsv->mapHeaders(['User_Name', 'E-mail', 'Country', 'City', 'activation_status']);
        $aDefaults = [
            'username' => 'pH7Builder',
            'email' => '',
            'country' => 'US',
            'city' => 'Virginia',
            'state' => 'Doswell',
            'is_active' => '1'
        ];
        $aRow = ['cara', 'cara@example.test', 'NZ', '', '0'];

        $this->assertSame('cara', $oCsv->getValue($aRow, $aColumns, $aDefaults, 'username'));
        $this->assertSame('NZ', $oCsv->getValue($aRow, $aColumns, $aDefaults, 'country'));
        $this->assertSame('Virginia', $oCsv->getValue($aRow, $aColumns, $aDefaults, 'city'));
        $this->assertSame('Doswell', $oCsv->getValue($aRow, $aColumns, $aDefaults, 'state'));
        $this->assertSame('0', $oCsv->getValue($aRow, $aColumns, $aDefaults, 'is_active'));
    }

    public function testDialectRejectsUnexpectedControlCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new MemberMigrationCsv())->assertDialect("\n", '"');
    }

    public function testMapsAnEmailHeaderWithUtf8ByteOrderMark(): void
    {
        $aColumns = (new MemberMigrationCsv())->mapHeaders(["\xEF\xBB\xBFemail", 'first_name']);

        $this->assertSame(0, $aColumns['email']);
        $this->assertSame(1, $aColumns['first_name']);
    }
}
