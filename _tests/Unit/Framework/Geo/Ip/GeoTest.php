<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Geo\Ip;

use InvalidArgumentException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use PH7\Framework\Geo\Ip\Geo;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class GeoTest extends TestCase
{
    private string $sSandboxPath;
    private string $sOriginalErrorLog;

    protected function setUp(): void
    {
        $this->sSandboxPath = sys_get_temp_dir() . '/ph7-geo-lookup-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->sSandboxPath, 0700));
        // Load the real class next to a disposable database, never altering the application's copy.
        self::assertTrue(copy(PH7_PATH_FRAMEWORK . 'Geo/Ip/Geo.class.php', $this->sSandboxPath . '/Geo.class.php'));
        require $this->sSandboxPath . '/Geo.class.php';
        $this->sOriginalErrorLog = (string)ini_set('error_log', $this->sSandboxPath . '/error.log');
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->sOriginalErrorLog);
        foreach (glob($this->sSandboxPath . '/*') as $sPath) {
            unlink($sPath);
        }
        rmdir($this->sSandboxPath);
    }

    public function testMissingDatabaseReturnsUnknownLocationWithRecoveryMessage(): void
    {
        $this->assertUnknownLocation();
        self::assertStringContainsString('Restore GeoLite2-City.mmdb', file_get_contents($this->sSandboxPath . '/error.log'));
    }

    public function testUnreadableDatabaseReturnsUnknownLocationWithRecoveryMessage(): void
    {
        $sPath = $this->sSandboxPath . '/GeoLite2-City.mmdb';
        file_put_contents($sPath, 'unreadable database');
        chmod($sPath, 0000);
        if (is_readable($sPath)) {
            self::markTestSkipped('This user can read files regardless of permission bits.');
        }

        $this->assertUnknownLocation();
        self::assertStringContainsString('Restore GeoLite2-City.mmdb', file_get_contents($this->sSandboxPath . '/error.log'));
    }

    public function testCorruptDatabaseReturnsUnknownLocation(): void
    {
        file_put_contents($this->sSandboxPath . '/GeoLite2-City.mmdb', 'not a MaxMind database');
        $this->assertUnknownLocation();
    }

    public function testRequiredCountryLookupRejectsAnUnavailableDatabase(): void
    {
        $this->expectException(InvalidDatabaseException::class);
        Geo::getCountryCode('8.8.8.8', true);
    }

    public function testRequiredCountryLookupRejectsACorruptDatabase(): void
    {
        file_put_contents($this->sSandboxPath . '/GeoLite2-City.mmdb', 'not a database');
        $this->expectException(InvalidDatabaseException::class);
        Geo::getCountryCode('8.8.8.8', true);
    }

    public function testValidDatabaseStillResolvesAddresses(): void
    {
        $this->copyBundledDatabase();
        self::assertSame('GB', Geo::getCountryCode('81.2.69.142'));
        self::assertNotEmpty(Geo::getCity('81.2.69.142'));
        self::assertNull(Geo::getCountryCode('127.0.0.1'));
    }

    public function testInvalidAddressIsNotMistakenForAnUnavailableDatabase(): void
    {
        $this->copyBundledDatabase();
        $this->expectException(InvalidArgumentException::class);
        Geo::getCountryCode('not-an-ip');
    }

    private function assertUnknownLocation(): void
    {
        foreach (['getCountryCode', 'getCountry', 'getCity', 'getState', 'getZipCode'] as $sMethod) {
            self::assertNull(Geo::$sMethod('8.8.8.8'));
        }
    }

    private function copyBundledDatabase(): void
    {
        self::assertTrue(copy(
            PH7_PATH_FRAMEWORK . 'Geo/Ip/GeoLite2-City.mmdb',
            $this->sSandboxPath . '/GeoLite2-City.mmdb'
        ));
    }
}
