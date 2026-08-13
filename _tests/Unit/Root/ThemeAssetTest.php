<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use DOMDocument;
use PH7\Framework\Mvc\Router\FrontController;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

final class ThemeAssetTest extends TestCase
{
    private const SHARED_LAYOUT_ASSETS = [
        'design_system.css',
        'menu.css',
        'menu_inverse.css'
    ];

    public function testEveryThemeProvidesSharedLayoutAssets(): void
    {
        $sThemesDirectory = dirname(__DIR__, 3) . '/templates/themes';
        $aThemeDirectories = glob($sThemesDirectory . '/*', GLOB_ONLYDIR);

        $this->assertIsArray($aThemeDirectories);
        $this->assertNotEmpty($aThemeDirectories);

        foreach ($aThemeDirectories as $sThemeDirectory) {
            foreach (self::SHARED_LAYOUT_ASSETS as $sAsset) {
                $this->assertFileExists(
                    $sThemeDirectory . '/css/' . $sAsset,
                    sprintf('%s must provide css/%s for the shared layout', basename($sThemeDirectory), $sAsset)
                );
            }
        }
    }

    public function testEveryLocalCssImportResolves(): void
    {
        $sThemesDirectory = dirname(__DIR__, 3) . '/templates/themes';
        $aThemeDirectories = glob($sThemesDirectory . '/*', GLOB_ONLYDIR);

        foreach ($aThemeDirectories as $sThemeDirectory) {
            $aCssFiles = $this->findFiles($sThemeDirectory . '/css', '.css');

            foreach ($aCssFiles as $sCssFile) {
                $sCss = file_get_contents($sCssFile);
                preg_match_all('/@import\s+url\(([^)]+)\)/i', $sCss, $aImports);

                foreach ($aImports[1] as $sImport) {
                    $sImport = trim($sImport, " \t\n\r\0\x0B\"'");
                    $sImportPath = str_replace(
                        [
                            '[$url_def_tpl_css]',
                            '[$url_theme][$current_tpl_name]/css/'
                        ],
                        [
                            $sThemesDirectory . '/base/css/',
                            $sThemeDirectory . '/css/'
                        ],
                        $sImport
                    );

                    $this->assertFileExists(
                        $sImportPath,
                        sprintf('%s imports the missing file %s', $sCssFile, $sImport)
                    );
                }
            }
        }
    }

    public function testTemplatesRetainAccessibleBrowserAndSignupNavigation(): void
    {
        $sProjectRoot = dirname(__DIR__, 3);
        $aTemplateFiles = array_merge(
            $this->findFiles($sProjectRoot . '/templates', '.tpl'),
            $this->findFiles($sProjectRoot . '/_protected/app/system/modules', '.tpl')
        );

        foreach ($aTemplateFiles as $sTemplateFile) {
            $sTemplate = file_get_contents($sTemplateFile);

            $this->assertIsString($sTemplate);
            $this->assertDoesNotMatchRegularExpression(
                '/(?:maximum-scale\s*=\s*1|user-scalable\s*=\s*no)/i',
                $sTemplate,
                sprintf('%s must allow users to zoom', $sTemplateFile)
            );
        }

        $aTopMenuTemplates = [
            $sProjectRoot . '/templates/themes/base/tpl/top_menu.inc.tpl',
            $sProjectRoot . '/templates/themes/premium/tpl/top_menu.inc.tpl'
        ];

        foreach ($aTopMenuTemplates as $sTopMenuTemplate) {
            $sTemplate = file_get_contents($sTopMenuTemplate);

            $this->assertIsString($sTemplate);
            $this->assertStringContainsString(
                '$design->url(\'user\', \'signup\', \'step1\')',
                $sTemplate
            );
        }

        $oRoutes = new DOMDocument;
        $this->assertTrue($oRoutes->load($sProjectRoot . '/_protected/app/configs/routes/en.xml'));

        foreach ($oRoutes->getElementsByTagName('route') as $oRoute) {
            if (!preg_match(
                '`^' . $oRoute->getAttribute('url') . FrontController::REGEX_URL_EXTRA_OPTIONS . '$`',
                'signup'
            )) {
                continue;
            }

            $this->assertSame('user', $oRoute->getAttribute('module'));
            $this->assertSame('signup', $oRoute->getAttribute('controller'));
            $this->assertSame('step1', $oRoute->getAttribute('action'));

            return;
        }

        $this->fail('The public signup URL does not match a configured route.');
    }

    public function testCookieBarDependencyIsPinnedAndIntegrityChecked(): void
    {
        $sProjectRoot = dirname(__DIR__, 3);
        $aLayoutFiles = [
            $sProjectRoot . '/templates/themes/base/tpl/layout.tpl',
            $sProjectRoot . '/templates/themes/premium/tpl/layout.tpl'
        ];

        foreach ($aLayoutFiles as $sLayoutFile) {
            $sLayout = file_get_contents($sLayoutFile);

            $this->assertIsString($sLayout);
            $this->assertStringContainsString('cookie-bar@1.10.3/', $sLayout);
            $this->assertStringContainsString('integrity="sha384-', $sLayout);
            $this->assertStringNotContainsString('cookie-bar/cookiebar-latest', $sLayout);
        }
    }

    public function testAdminChartsUseCurrentLoaderApi(): void
    {
        $sAdminTemplateDirectory = dirname(__DIR__, 3) .
            '/_protected/app/system/modules/admin123/views/base/tpl';
        $aChartTemplates = [
            $sAdminTemplateDirectory . '/main/stat.tpl',
            $sAdminTemplateDirectory . '/tool/cache.tpl',
            $sAdminTemplateDirectory . '/tool/freespace.tpl'
        ];

        foreach ($aChartTemplates as $sChartTemplate) {
            $sTemplate = file_get_contents($sChartTemplate);

            $this->assertIsString($sTemplate);
            $this->assertStringContainsString('https://www.gstatic.com/charts/loader.js', $sTemplate);
            $this->assertStringContainsString('google.charts.load(', $sTemplate);
            $this->assertStringNotContainsString('https://www.google.com/jsapi', $sTemplate);
            $this->assertStringNotContainsString('google.load(', $sTemplate);
        }
    }

    public function testThemeFormControlsRetainResponsiveBorderBoxSizing(): void
    {
        $sProjectRoot = dirname(__DIR__, 3);
        $aFormStylesheets = [
            $sProjectRoot . '/templates/themes/base/css/form.css',
            $sProjectRoot . '/templates/themes/premium/css/form.css'
        ];

        foreach ($aFormStylesheets as $sFormStylesheet) {
            $sCss = file_get_contents($sFormStylesheet);

            $this->assertIsString($sCss);
            $this->assertStringContainsString('box-sizing: border-box;', $sCss);
            $this->assertStringNotContainsString('box-sizing: content-box;', $sCss);
        }
    }

    public function testVideoSplashKeepsItsBackgroundVisible(): void
    {
        $sCss = file_get_contents(
            dirname(__DIR__, 3) . '/templates/themes/base/css/video_splash.css'
        );

        $this->assertIsString($sCss);
        $this->assertMatchesRegularExpression(
            '/html, body\s*\{[^}]*background:\s*transparent;/s',
            $sCss
        );
        $this->assertStringContainsString(
            '#form_login_user label, #form_join_user label',
            $sCss
        );
        $this->assertStringContainsString('color: #333 !important;', $sCss);
    }

    private function findFiles(string $sDirectory, string $sSuffix): array
    {
        $aFiles = [];
        $oIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sDirectory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($oIterator as $oFile) {
            if ($oFile->isFile() && str_ends_with($oFile->getFilename(), $sSuffix)) {
                $aFiles[] = $oFile->getPathname();
            }
        }

        sort($aFiles);

        return $aFiles;
    }
}
