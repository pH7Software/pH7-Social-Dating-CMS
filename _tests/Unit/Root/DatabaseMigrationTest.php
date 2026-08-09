<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class DatabaseMigrationTest extends TestCase
{
    private const SQL_SCHEMA_VERSION = '1.6.6';

    public function testFreshSchemaUsesUtf8mb4ForWallPosts(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertStringNotContainsString('post text CHARACTER SET armscii8', $sSchema);
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS ph7_members_wall \(.*?post text,.*?\) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4/s',
            $sSchema
        );
    }

    public function testFreshSchemaRecordsCurrentSqlSchemaVersion(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertStringContainsString(
            "('pH7Builder', 'SQL System Schema', '" . self::SQL_SCHEMA_VERSION . "', 1)",
            $sSchema
        );
    }

    public function testFreshSchemaUsesInnoDbForErrorLogs(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS ph7_log_error \(.*?\) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4/s',
            $sSchema
        );
    }

    public function testFreshSchemaPersistsIdempotentPaymentNotifications(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS ph7_payment_transactions', $sSchema);
        $this->assertStringContainsString('UNIQUE KEY payment_checkout_reference (checkout_reference_hash)', $sSchema);
        $this->assertStringContainsString(
            'UNIQUE KEY payment_provider_transaction (provider, provider_transaction_id)',
            $sSchema
        );
        $this->assertStringContainsString('KEY payment_status_created (status, created_at)', $sSchema);
    }

    public function testUpgradeMigrationAppliesSupportedSchemaChanges(): void
    {
        $sMigration = $this->readProjectFile(
            '_repository/upgrade/18.5.1-18.6.0/data/sql/MySQL/upgrade.sql'
        );

        $this->assertStringContainsString(
            'ALTER TABLE ph7_members_wall MODIFY post TEXT CHARACTER SET utf8mb4;',
            $sMigration
        );
        $this->assertStringContainsString('ALTER TABLE ph7_log_error ENGINE=InnoDB;', $sMigration);
        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS ph7_payment_transactions',
            $sMigration
        );
        $this->assertStringNotContainsString('DROP TABLE', strtoupper($sMigration));
        $this->assertStringContainsString(
            "UPDATE ph7_modules SET version = '" . self::SQL_SCHEMA_VERSION . "'",
            $sMigration
        );
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(dirname(__DIR__, 3) . '/' . $sRelativePath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
