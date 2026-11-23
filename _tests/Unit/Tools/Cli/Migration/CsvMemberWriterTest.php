<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Tools / Cli / Migration
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Tools\Cli\Migration;

use PH7\Cli\Migration\CsvMemberWriter;
use PH7\Cli\Migration\UnaMemberMapper;
use PHPUnit\Framework\TestCase;

final class CsvMemberWriterTest extends TestCase
{
    private string $sCsvPath;

    protected function setUp(): void
    {
        $sReservedPath = tempnam(sys_get_temp_dir(), 'ph7-migration-');
        self::assertIsString($sReservedPath);
        unlink($sReservedPath);
        $this->sCsvPath = $sReservedPath . '.csv';
    }

    protected function tearDown(): void
    {
        if (is_file($this->sCsvPath)) {
            unlink($this->sCsvPath);
        }
    }

    public function testWritesCanonicalColumnsInStableOrder(): void
    {
        $oWriter = new CsvMemberWriter($this->sCsvPath);
        $aMember = array_fill_keys(UnaMemberMapper::CSV_HEADERS, '');
        $aMember['source_platform'] = 'una';
        $aMember['source_version'] = '14.0.0';
        $aMember['source_id'] = '27';
        $aMember['email'] = 'member@example.test';
        $oWriter->append($aMember);
        $oWriter->close();

        $rCsv = fopen($this->sCsvPath, 'rb');
        self::assertIsResource($rCsv);
        $aHeaders = fgetcsv($rCsv, 0, ',', '"', '\\');
        $aRow = fgetcsv($rCsv, 0, ',', '"', '\\');
        fclose($rCsv);

        $this->assertSame(UnaMemberMapper::CSV_HEADERS, $aHeaders);
        $this->assertIsArray($aRow);
        $this->assertSame('una', $aRow[0]);
        $this->assertSame('member@example.test', $aRow[3]);
        $this->assertSame('', $aRow[7], 'Source password hashes must never be exported.');
        $this->assertSame(0600, fileperms($this->sCsvPath) & 0777);
    }

    public function testRefusesToOverwriteExistingExports(): void
    {
        file_put_contents($this->sCsvPath, 'preserve this export');

        try {
            new CsvMemberWriter($this->sCsvPath);
            $this->fail('An existing migration export must not be opened.');
        } catch (\RuntimeException) {
            $this->assertSame('preserve this export', file_get_contents($this->sCsvPath));
        }
    }
}
