<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use FilesystemIterator;
use GeoIp2\Database\Reader as CityReader;
use MaxMind\Db\Reader as MetadataReader;
use PH7\Framework\Geo\Ip\Geo;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The bundled GeoLite2-City database must stay the last build MaxMind published under
 * CC BY-SA 4.0. Later builds are only available to MaxMind account holders under the
 * GeoLite EULA, which requires destroying them within 30 days of the next release.
 */
final class BundledGeoIpDatabaseTest extends TestCase
{
    private const GEOIP_DIRECTORY = '/_protected/framework/Geo/Ip/';

    /** 30 December 2019 00:00 UTC, when MaxMind's GeoLite EULA replaced the Creative Commons licence */
    private const GEOLITE_EULA_START_EPOCH = 1577664000;

    private const BUNDLED_BUILD_DATE = '2019-12-24';

    private const BUNDLED_BUILD_LABEL = '24 December 2019';

    private string $sRootPath;

    protected function setUp(): void
    {
        $this->sRootPath = dirname(PH7_PATH_PROTECTED);
    }

    public function testBundledDatabaseIsTheLastCreativeCommonsGeoLite2CityBuild(): void
    {
        $oReader = new MetadataReader($this->geoIpPath(Geo::DATABASE_FILENAME));
        $oMetadata = $oReader->metadata();
        $oReader->close();

        self::assertSame('GeoLite2-City', $oMetadata->databaseType);
        self::assertLessThan(self::GEOLITE_EULA_START_EPOCH, $oMetadata->buildEpoch);
        self::assertSame(self::BUNDLED_BUILD_DATE, gmdate('Y-m-d', $oMetadata->buildEpoch));
    }

    public function testBundledDatabaseResolvesCityLevelData(): void
    {
        $oReader = new CityReader($this->geoIpPath(Geo::DATABASE_FILENAME));
        $oCity = $oReader->city('81.2.69.142');
        $oReader->close();

        self::assertSame('GB', $oCity->country->isoCode);
        self::assertNotEmpty($oCity->city->name);
    }

    public function testMaxMindNoticesShipWithTheDatabase(): void
    {
        $sLicenseNotice = $this->readFile($this->geoIpPath('LICENSE.txt'));
        self::assertStringContainsString('Creative Commons Attribution-ShareAlike 4.0 International License', $sLicenseNotice);
        self::assertStringContainsString('GeoNames', $sLicenseNotice);

        self::assertStringContainsString(
            'Database and Contents Copyright (c) 2019 MaxMind, Inc.',
            $this->readFile($this->geoIpPath('COPYRIGHT.txt'))
        );
        self::assertFileExists($this->geoIpPath('README.txt'));

        $sLicenseText = $this->readFile($this->geoIpPath('Maxmind-GeoLite2.license.txt'));
        self::assertStringStartsWith('Attribution-ShareAlike 4.0 International', $sLicenseText);
        self::assertStringNotContainsString('Attribution-ShareAlike 3.0', $sLicenseText);
    }

    public function testProjectNoticesDescribeTheBundledBuild(): void
    {
        $sCopyright = $this->readFile($this->sRootPath . '/COPYRIGHT.md');
        self::assertStringContainsString(self::BUNDLED_BUILD_LABEL, $sCopyright);
        self::assertStringContainsString('Database and Contents Copyright (c) 2019 MaxMind, Inc.', $sCopyright);
        self::assertStringContainsString('https://creativecommons.org/licenses/by-sa/4.0/', $sCopyright);

        $sInstructions = $this->readFile($this->geoIpPath('update-geo-database-version.txt'));
        self::assertStringContainsString(self::BUNDLED_BUILD_LABEL, $sInstructions);
        self::assertStringContainsString('install geoip db', $sInstructions);
    }

    public function testMaintenanceScriptInstallsTheDatabaseWithoutMaxMindCredentials(): void
    {
        $sScript = $this->readFile($this->sRootPath . '/_tools/pH7.sh');

        self::assertStringContainsString('"install geoip db"|"update geoip db") install-geoip-db;;', $sScript);
        self::assertStringContainsString('bundled_db_sha256="a253d9cd68fe17b00087da24375f31f07cd4bb3852dc5fe3afe37b8f59e5abd0"', $sScript);
        self::assertStringNotContainsString('MAXMIND_LICENSE_KEY', $sScript);
        self::assertStringNotContainsString('license_key=', $sScript);
        self::assertStringNotContainsString('geolite2/signup', $sScript);
    }

    public function testMaintenanceScriptVerifiesTheBundledDatabaseWithoutCredentials(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('The maintenance script requires a Unix shell.');
        }

        // Choose the command, then leave the source path empty to verify the bundled build
        [$iExitCode, $sOutput] = $this->runMaintenanceScript($this->sRootPath, "install geoip db\n\n");

        self::assertSame(0, $iExitCode, $sOutput);
        self::assertStringContainsString('is already installed at', $sOutput);
        self::assertStringContainsString('GeoLite2-City database built on ' . self::BUNDLED_BUILD_LABEL, $sOutput);
        self::assertStringNotContainsString('Downloading', $sOutput);
    }

    public function testBareMmdbInstallAsksForMaxMindNoticeFiles(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('The maintenance script requires a Unix shell.');
        }

        // Install into a throwaway copy of the project, so the repository's own database is never replaced
        $sSandboxPath = $this->createScriptSandbox();
        $sSourceDatabasePath = $this->geoIpPath(Geo::DATABASE_FILENAME);

        try {
            [$iExitCode, $sOutput] = $this->runMaintenanceScript(
                $sSandboxPath,
                "install geoip db\n" . $sSourceDatabasePath . "\n"
            );

            self::assertSame(0, $iExitCode, $sOutput);
            self::assertStringContainsString("A .mmdb file comes without MaxMind's notice files", $sOutput);
            self::assertSame(
                hash_file('sha256', $sSourceDatabasePath),
                hash_file('sha256', $sSandboxPath . self::GEOIP_DIRECTORY . Geo::DATABASE_FILENAME)
            );
        } finally {
            $this->removeScriptSandbox($sSandboxPath);
        }
    }

    public function testInstallWithAnUnknownBuildDateNamesBothLicences(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('The maintenance script requires a Unix shell.');
        }

        // The sandbox has no PHP dependencies, so the script can't read the bundled build's date
        $sSandboxPath = $this->createScriptSandbox();

        try {
            [$iExitCode, $sOutput] = $this->runMaintenanceScript(
                $sSandboxPath,
                "install geoip db\n" . $this->geoIpPath(Geo::DATABASE_FILENAME) . "\n"
            );

            self::assertSame(0, $iExitCode, $sOutput);
            self::assertStringContainsString('build date is unknown', $sOutput);
            self::assertStringContainsString('before 30 December 2019 are licensed under CC BY-SA 4.0', $sOutput);
            self::assertStringNotContainsString('This build is governed by', $sOutput);
        } finally {
            $this->removeScriptSandbox($sSandboxPath);
        }
    }

    /**
     * @return array{int, string} The exit code and the combined standard output and error.
     */
    private function runMaintenanceScript(string $sWorkingDirectory, string $sInput): array
    {
        $aPipes = [];
        $rProcess = proc_open(
            ['bash', '_tools/pH7.sh'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $aPipes,
            $sWorkingDirectory,
            ['PATH' => (string)getenv('PATH'), 'HOME' => (string)getenv('HOME')]
        );
        self::assertIsResource($rProcess);

        fwrite($aPipes[0], $sInput);
        fclose($aPipes[0]);
        $sOutput = stream_get_contents($aPipes[1]) . stream_get_contents($aPipes[2]);
        fclose($aPipes[1]);
        fclose($aPipes[2]);

        return [proc_close($rProcess), $sOutput];
    }

    /**
     * Create a temporary project root holding only the maintenance script and an empty GeoIP directory.
     * Without _protected/vendor, the script can't read a database's build date.
     */
    private function createScriptSandbox(): string
    {
        $sSandboxPath = sys_get_temp_dir() . '/ph7-geoip-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($sSandboxPath . self::GEOIP_DIRECTORY, 0700, true));
        self::assertTrue(mkdir($sSandboxPath . '/_tools', 0700));
        self::assertTrue(copy($this->sRootPath . '/_tools/pH7.sh', $sSandboxPath . '/_tools/pH7.sh'));

        return $sSandboxPath;
    }

    private function removeScriptSandbox(string $sSandboxPath): void
    {
        $oEntries = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sSandboxPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($oEntries as $oEntry) {
            $oEntry->isDir() ? rmdir($oEntry->getPathname()) : unlink($oEntry->getPathname());
        }
        rmdir($sSandboxPath);
    }

    private function geoIpPath(string $sFilename): string
    {
        return $this->sRootPath . self::GEOIP_DIRECTORY . $sFilename;
    }

    private function readFile(string $sPath): string
    {
        $sContents = file_get_contents($sPath);
        self::assertIsString($sContents);

        return $sContents;
    }
}
