<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class ReleaseDeploymentTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testRootReleaseLockIsIncludedAndInstallerDependencyIsConstrained(): void
    {
        $sAttributes = $this->readFile('.gitattributes');
        $aComposer = json_decode($this->readFile('composer.json'), true, 512, JSON_THROW_ON_ERROR);
        $aInstallerComposer = json_decode(
            $this->readFile('_install/composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertStringNotContainsString('composer.lock export-ignore', $sAttributes);
        $this->assertSame('>=4.5.7 <4.5.8', $aInstallerComposer['require']['smarty/smarty']);
        $this->assertSame(
            '@php _protected/app/includes/InstallerDependencies.php',
            $aComposer['scripts']['install-installer-dependencies']
        );
        $this->assertContains('@install-installer-dependencies', $aComposer['scripts']['post-install-cmd']);
        $this->assertContains('@install-installer-dependencies', $aComposer['scripts']['post-update-cmd']);
    }

    public function testReleaseVersionIsSynchronizedAcrossRuntimeInstallerAndGuides(): void
    {
        $sFramework = $this->readFile('_protected/framework/Security/Version.class.php');
        $sInstaller = $this->readFile('_install/library/Controller.class.php');

        $this->assertSame(
            1,
            preg_match("/KERNEL_VERSION = '([^']+)'/", $sFramework, $aFrameworkVersion)
        );
        $this->assertSame(
            1,
            preg_match("/SOFTWARE_VERSION = '([^']+)'/", $sInstaller, $aInstallerVersion)
        );
        $this->assertSame($aFrameworkVersion[1], $aInstallerVersion[1]);

        $sVersion = $aFrameworkVersion[1];
        $sReleaseUrl = "releases/download/v{$sVersion}/pH7Builder-v{$sVersion}.zip";
        $this->assertStringContainsString($sReleaseUrl, $this->readFile('README.md'));
        $this->assertStringContainsString($sReleaseUrl, $this->readFile('docs/QUICK_START.md'));
        $this->assertStringContainsString(
            "ph7software/ph7builder:{$sVersion}",
            $this->readFile('installation-instructions-(start-here).txt')
        );
        $this->assertFileExists(self::PROJECT_ROOT . "/docs/RELEASE_NOTES_{$sVersion}.md");
    }

    public function testReleasePackagerDoesNotMutateOrCreateWorldWritableSourceFiles(): void
    {
        $sPackager = $this->readFile('_tools/packaging.sh');
        $sQuickStart = $this->readFile('docs/QUICK_START.md');

        $this->assertStringContainsString('git -C "$PROJECT_ROOT" archive', $sPackager);
        $this->assertStringContainsString('mktemp -d', $sPackager);
        $this->assertStringContainsString('ls-files --error-unmatch composer.lock', $sPackager);
        $this->assertStringNotContainsString('_install/composer.lock', $sPackager);
        $this->assertStringContainsString('status --porcelain --untracked-files=all', $sPackager);
        $this->assertStringContainsString('find "$PACKAGE_DIRECTORY/_install" -type d -exec chmod 0775', $sPackager);
        $this->assertStringContainsString('chmod 0775 "$PACKAGE_DIRECTORY/_protected/app/configs"', $sPackager);
        $this->assertStringNotContainsString(
            'find "$PACKAGE_DIRECTORY/_protected/app/configs" -type',
            $sPackager
        );
        $this->assertStringContainsString('_protected/app/configs/banned', $sPackager);
        $this->assertStringContainsString('_protected/app/configs/suggestions', $sPackager);
        $this->assertStringContainsString('_protected/app/configs/routes', $sPackager);
        $this->assertStringContainsString('_protected/app/system/global/views/base/tpl/mail', $sPackager);
        $this->assertStringContainsString('_protected/app/system/modules/page/views/base', $sPackager);
        $this->assertStringContainsString('$PACKAGE_DIRECTORY/templates/themes', $sPackager);
        $this->assertGreaterThan(
            strrpos($sPackager, 'find "$sWritableDirectory" -type d -exec chmod 0775'),
            strrpos($sPackager, 'chmod 2775 "$PACKAGE_DIRECTORY/_install/data/caches"')
        );
        $this->assertStringContainsString('MUTABLE_MODULE_CONFIGS', $sPackager);
        $this->assertStringContainsString('-type f -exec chmod 0664', $sPackager);
        $this->assertStringContainsString('SHA-256 file:', $sPackager);
        $this->assertStringNotContainsString('self-update', $sPackager);
        $this->assertStringNotContainsString('chmod 666', $sPackager);
        $this->assertStringNotContainsString('chmod 777', $sPackager);
        $this->assertStringNotContainsString('sudo ', $sPackager);
        $this->assertStringNotContainsString(
            '/_protected/app/configs /var/www/ph7builder/_protected/data',
            $sQuickStart
        );
        $this->assertStringContainsString(
            'sudo chmod 0755 /var/www/ph7builder/_protected/app/configs',
            $sQuickStart
        );
        $this->assertStringContainsString(
            'Executable configuration PHP',
            $sQuickStart
        );
    }

    public function testApacheDeniesInternalPathsAndNonFrontControllerPhpFiles(): void
    {
        $sConfig = $this->readFile('.htaccess');
        $sSampleConfig = $this->readFile('sample.htaccess');

        $this->assertStringContainsString('_protected|_repository|_tests|_tools|docker', $sConfig);
        $this->assertStringContainsString('_install/(?:data|vendor)', $sConfig);
        $this->assertStringContainsString('(?!(?:index|_install/index)\\.php$)', $sConfig);
        $this->assertStringContainsString('\\.well-known/security\\.txt', $sConfig);
        $this->assertStringContainsString('^composer\\.(json|lock)$', $sConfig);
        $this->assertStringContainsString('^Dockerfile$', $sConfig);
        $this->assertSame($sConfig, $sSampleConfig);
        $this->assertStringContainsString('Require all denied', $this->readFile('_install/data/.htaccess'));
    }

    public function testDockerKeepsInstallerTokenReadableByPhpFpm(): void
    {
        $sDockerfile = $this->readFile('Dockerfile');
        $this->assertGreaterThan(
            strrpos($sDockerfile, 'chown -R www-data:www-data /var/www'),
            strrpos($sDockerfile, 'chmod 2775 /var/www/_install/data/caches')
        );
        $this->assertStringContainsString('--with-webp', $sDockerfile);
        $this->assertStringContainsString('docker/php/uploads.ini', $sDockerfile);
        $this->assertStringContainsString('upload_max_filesize = 50M', $this->readFile('docker/php/uploads.ini'));
    }

    public function testNginxConfigurationsExposeOnlyFrontControllerPhpFiles(): void
    {
        foreach (['nginx.conf', 'docker/nginx/default.conf'] as $sConfigFile) {
            $sConfig = $this->readFile($sConfigFile);

            $this->assertStringContainsString('_protected|_repository|_tests|_tools|docker', $sConfig);
            $this->assertStringContainsString('^/_install/(?:data|vendor)', $sConfig);
            $this->assertStringContainsString('location = /index.php', $sConfig);
            $this->assertStringContainsString('location = /_install/index.php', $sConfig);
            $this->assertStringContainsString('location ~ \\.php$', $sConfig);
            $this->assertSame(2, substr_count($sConfig, 'fastcgi_param HTTP_MOD_REWRITE On;'));
            $this->assertStringContainsString('^/data/.+\\.', $sConfig);
            $this->assertStringNotContainsString('|swf', $sConfig);
            $this->assertStringContainsString('composer\\.(?:json|lock)', $sConfig);
            $this->assertStringContainsString('\\.well-known/security\\.txt', $sConfig);
        }

        $sProductionConfig = $this->readFile('nginx.conf');
        $this->assertStringContainsString('listen 80 default_server;', $sProductionConfig);
        $this->assertStringContainsString('return 444;', $sProductionConfig);
        $this->assertStringNotContainsString('|swf', $this->readFile('data/.htaccess'));
    }

    private function readFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . '/' . $sRelativePath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
