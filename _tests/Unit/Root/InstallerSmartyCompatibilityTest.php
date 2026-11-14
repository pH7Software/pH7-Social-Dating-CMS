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

    private function createSmarty(string $sTemplateDirectory, string $sCompileDirectory): Smarty
    {
        $oSmarty = new Smarty();
        $oSmarty->setUseSubDirs(true);
        $oSmarty->setTemplateDir($sTemplateDirectory);
        $oSmarty->setCompileDir($sCompileDirectory);

        return $oSmarty;
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
