<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Tools\Cli\Health;

use PH7\Cli\Health\HealthReport;
use PHPUnit\Framework\TestCase;

final class HealthReportTest extends TestCase
{
    private string $sProjectRoot;

    protected function setUp(): void
    {
        $this->sProjectRoot = sys_get_temp_dir() . '/ph7-health-' . bin2hex(random_bytes(8));
        foreach ([
            '_protected/app/configs',
            '_protected/data/cache',
            '_protected/data/log',
            '_protected/data/tmp',
            '_protected/data/backup',
            'data'
        ] as $sDirectory) {
            self::assertTrue(mkdir($this->sProjectRoot . '/' . $sDirectory, 0775, true));
        }
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->sProjectRoot);
    }

    public function testHealthyInstallationReportsOperationalChecksWithoutSecrets(): void
    {
        $this->writeConfig('production');
        $oReport = new HealthReport(
            $this->sProjectRoot,
            static fn (array $aConfig): array => [
                'version' => '8.4.7',
                'schema' => '1.6.7',
                'cron_secret' => 'a-unique-cron-secret'
            ],
            'null://null',
            1_800_000_000
        );

        $aChecks = $oReport->collect();

        self::assertSame('pass', $this->checkStatus($aChecks, 'Production mode'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Installer lockdown'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Runtime paths'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'MySQL runtime'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Database schema'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Cron configuration'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Email transport'));
        self::assertSame('pass', $this->checkStatus($aChecks, 'Cache age'));
        self::assertStringNotContainsString('database-password', json_encode($aChecks, JSON_THROW_ON_ERROR));
    }

    public function testUnsafeOrOutdatedInstallationIsReportedWithoutChangingIt(): void
    {
        $this->writeConfig('development');
        self::assertTrue(mkdir($this->sProjectRoot . '/_install'));
        self::assertTrue(touch($this->sProjectRoot . '/_protected/data/cache/old.cache', 1_700_000_000));

        $oReport = new HealthReport(
            $this->sProjectRoot,
            static fn (array $aConfig): array => [
                'version' => '5.7.44',
                'schema' => '1.6.6',
                'cron_secret' => 'change_this_secret_cron_word_by_yours'
            ],
            '',
            1_800_000_000
        );

        $aChecks = $oReport->collect();

        self::assertSame('fail', $this->checkStatus($aChecks, 'Production mode'));
        self::assertSame('fail', $this->checkStatus($aChecks, 'Installer lockdown'));
        self::assertSame('fail', $this->checkStatus($aChecks, 'MySQL runtime'));
        self::assertSame('fail', $this->checkStatus($aChecks, 'Database schema'));
        self::assertSame('fail', $this->checkStatus($aChecks, 'Cron configuration'));
        self::assertSame('warning', $this->checkStatus($aChecks, 'Cache age'));
        self::assertTrue(HealthReport::hasFailures($aChecks));
        self::assertFileExists($this->sProjectRoot . '/_protected/data/cache/old.cache');
    }

    private function writeConfig(string $sEnvironment): void
    {
        $sConfig = <<<INI
[mode]
environment = $sEnvironment

[database]
hostname = "127.0.0.1"
username = "health"
password = "database-password"
name = "health"
prefix = "ph7_"
charset = "utf8mb4"
port = 3306
INI;
        self::assertIsInt(
            file_put_contents($this->sProjectRoot . '/_protected/app/configs/config.ini', $sConfig)
        );
    }

    private function checkStatus(array $aChecks, string $sName): string
    {
        foreach ($aChecks as $aCheck) {
            if ($aCheck['name'] === $sName) {
                return $aCheck['status'];
            }
        }

        self::fail('Health check not found: ' . $sName);
    }

    private function removeDirectory(string $sDirectory): void
    {
        if (!is_dir($sDirectory)) {
            return;
        }

        $oIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sDirectory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($oIterator as $oItem) {
            $oItem->isDir() ? rmdir($oItem->getPathname()) : unlink($oItem->getPathname());
        }
        rmdir($sDirectory);
    }
}
