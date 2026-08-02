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

    public function testTemplatesDoNotDisableBrowserZoom(): void
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
