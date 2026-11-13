<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;
use Smarty\Smarty;

require_once dirname(PH7_PATH_PROTECTED) . '/_install/vendor/autoload.php';

final class InstallerSmartyCompatibilityTest extends TestCase
{
    public function testEveryInstallerTemplateCompilesWithLockedSmarty(): void
    {
        $sTemplateDirectory = dirname(PH7_PATH_PROTECTED) . '/_install/views/base';
        $sCompileDirectory = sys_get_temp_dir() . '/ph7-smarty-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($sCompileDirectory, 0700));

        try {
            $oSmarty = $this->createSmarty($sTemplateDirectory, $sCompileDirectory);
            $iTemplateCount = 0;

            $oTemplates = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sTemplateDirectory, \FilesystemIterator::SKIP_DOTS)
            );

            /** @var \SplFileInfo $oTemplate */
            foreach ($oTemplates as $oTemplate) {
                if (!$oTemplate->isFile() || $oTemplate->getExtension() !== 'tpl') {
                    continue;
                }

                $sRelativePath = substr($oTemplate->getPathname(), strlen($sTemplateDirectory) + 1);
                $oSmarty->createTemplate($sRelativePath)->compileTemplateSource();
                ++$iTemplateCount;
            }

            self::assertGreaterThan(0, $iTemplateCount);

            $iCompiledTemplateCount = 0;
            $oCompiledTemplates = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sCompileDirectory, \FilesystemIterator::SKIP_DOTS)
            );

            /** @var \SplFileInfo $oCompiledTemplate */
            foreach ($oCompiledTemplates as $oCompiledTemplate) {
                if ($oCompiledTemplate->isFile() && $oCompiledTemplate->getExtension() === 'php') {
                    ++$iCompiledTemplateCount;
                }
            }

            self::assertGreaterThanOrEqual($iTemplateCount, $iCompiledTemplateCount);
        } finally {
            $this->removeDirectory($sCompileDirectory);
        }
    }

    public function testInstallerLandingPageRendersWithLockedSmartyFiveRuntime(): void
    {
        $sProjectDirectory = dirname(PH7_PATH_PROTECTED);
        $sTemplateDirectory = $sProjectDirectory . '/_install/views/base';
        $sCompileDirectory = sys_get_temp_dir() . '/ph7-smarty-render-' . bin2hex(random_bytes(8));
        self::assertTrue(mkdir($sCompileDirectory, 0700));

        $aLock = json_decode(
            file_get_contents($sProjectDirectory . '/_install/composer.lock'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $aPackages = array_column($aLock['packages'], null, 'name');
        $sLockedVersion = ltrim($aPackages['smarty/smarty']['version'], 'v');
        $sController = file_get_contents($sProjectDirectory . '/_install/library/Controller.class.php');

        self::assertSame($sLockedVersion, Smarty::SMARTY_VERSION);
        self::assertStringStartsWith('5.', Smarty::SMARTY_VERSION);
        self::assertIsString($sController);
        self::assertStringContainsString('use Smarty\\Smarty;', $sController);
        self::assertStringNotContainsString('use Smarty;', $sController);

        if (!defined('PH7_URL_INSTALL')) {
            define('PH7_URL_INSTALL', PH7_URL_ROOT . '_install/');
        }
        if (!defined('PH7_URL_SLUG_INSTALL')) {
            define('PH7_URL_SLUG_INSTALL', PH7_URL_INSTALL . '?a=');
        }

        try {
            $oSmarty = $this->createSmarty($sTemplateDirectory, $sCompileDirectory);
            $oSmarty->assign([
                'LANG' => $this->installerLanguageFixture(),
                'software_name' => 'pH7Builder',
                'software_version' => '20.0.0',
                'software_website' => 'https://ph7builder.com',
                'software_author' => 'Pierre-Henry Soria',
                'software_copyright' => 'Copyright 2026',
                'tpl_name' => 'base',
                'current_year' => '2026',
                'total_install_steps' => 7,
                'action_token' => str_repeat('a', 64),
                'install_access_required' => false,
                'install_access_configured' => false,
                'install_access_error' => '',
                'lang_select' => '<option value="?l=en">English</option>',
                'sept_number' => null,
                'csrf_error' => ''
            ]);

            $sHtml = $oSmarty->fetch('index.tpl');

            self::assertStringContainsString('<!DOCTYPE html>', $sHtml);
            self::assertStringContainsString('pH7Builder', $sHtml);
            self::assertStringContainsString(PH7_URL_SLUG_INSTALL . 'license', $sHtml);
            self::assertStringContainsString('static/js/common.js', $sHtml);
            self::assertStringNotContainsString('{include', $sHtml);
            self::assertStringNotContainsString('{$', $sHtml);

            $oSmarty->assign([
                'install_access_required' => true,
                'install_access_configured' => true
            ]);
            $sLockedHtml = $oSmarty->fetch('index.tpl');

            self::assertStringContainsString('name="install_access_token"', $sLockedHtml);
            self::assertStringContainsString('name="action_token" value="' . str_repeat('a', 64) . '"', $sLockedHtml);
            self::assertStringContainsString('action="' . PH7_URL_SLUG_INSTALL . 'index"', $sLockedHtml);
        } finally {
            $this->removeDirectory($sCompileDirectory);
        }
    }

    private function createSmarty(string $sTemplateDirectory, string $sCompileDirectory): Smarty
    {
        $oSmarty = new Smarty();
        $oSmarty->setUseSubDirs(true);
        $oSmarty->setTemplateDir($sTemplateDirectory);
        $oSmarty->setCompileDir($sCompileDirectory);

        return $oSmarty;
    }

    private function installerLanguageFixture(): array
    {
        return [
            'lang' => 'en',
            'charset' => 'utf-8',
            'welcome' => 'Welcome to the installation of',
            'version' => 'version',
            'CMS_desc' => '<p>Welcome to the pH7Builder installer.</p>',
            'choose_install_lang' => 'Choose an installation language',
            'requirements_desc' => 'Review the server requirements.',
            'requirements2_desc' => 'Prepare an empty MySQL database.',
            'go' => 'Next step',
            'warning_no_js' => 'JavaScript is required.',
            'welcome_to_installer' => 'Installation of',
            'step' => 'Step',
            'powered' => 'Powered by',
            'install_access_intro' => 'Enter the installer access token.',
            'install_access_not_configured' => 'Configure an installer access token.',
            'install_access_token' => 'Installer access token',
            'next' => 'Next'
        ];
    }

    private function removeDirectory(string $sDirectory): void
    {
        if (!is_dir($sDirectory)) {
            return;
        }

        $oFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sDirectory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \SplFileInfo $oFile */
        foreach ($oFiles as $oFile) {
            if ($oFile->isDir()) {
                rmdir($oFile->getPathname());
            } else {
                unlink($oFile->getPathname());
            }
        }

        rmdir($sDirectory);
    }
}
