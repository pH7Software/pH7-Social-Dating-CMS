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

final class ManualUpgradeAssetTest extends TestCase
{
    private string $sUpgradeAsset;

    protected function setUp(): void
    {
        $sContents = file_get_contents(
            dirname(__DIR__, 3) . '/_protected/app/system/core/assets/file/UpgradeCoreFile.php'
        );

        $this->assertIsString($sContents);
        $this->sUpgradeAsset = $sContents;
    }

    public function testManualUpgradeNoticePreservesAdministratorAuthentication(): void
    {
        $this->assertStringContainsString('AdminCore::auth()', $this->sUpgradeAsset);
        $this->assertStringContainsString('http_response_code(403)', $this->sUpgradeAsset);
        $this->assertStringContainsString(
            'You must be logged in as administrator to upgrade your site.',
            $this->sUpgradeAsset
        );
    }

    public function testAutomaticDownloadAndExtractionAreUnavailable(): void
    {
        $this->assertStringNotContainsString('getUrlContents(', $this->sUpgradeAsset);
        $this->assertStringNotContainsString('zipExtract(', $this->sUpgradeAsset);
        $this->assertStringNotContainsString('submit_upgrade', $this->sUpgradeAsset);
        $this->assertStringNotContainsString('REMOTE_URL', $this->sUpgradeAsset);
    }

    public function testManualNoticeLinksToOfficialSources(): void
    {
        $this->assertStringContainsString("Kernel::SOFTWARE_GIT_REPO_URL . '/releases'", $this->sUpgradeAsset);
        $this->assertStringContainsString('Version::UPGRADE_DOC_URL', $this->sUpgradeAsset);
    }
}
