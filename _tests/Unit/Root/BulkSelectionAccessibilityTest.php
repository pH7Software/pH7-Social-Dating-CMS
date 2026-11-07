<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class BulkSelectionAccessibilityTest extends TestCase
{
    public function testBulkSelectionCheckboxesHaveAccessibleNames(): void
    {
        $iCheckboxCount = 0;

        foreach ($this->templateFiles() as $oFile) {
            $sContents = file_get_contents($oFile->getPathname());
            $this->assertIsString($sContents);

            preg_match_all('/<input\b.*?\/>/is', $sContents, $aMatches);

            foreach ($aMatches[0] as $sCheckbox) {
                if (!preg_match('/\bname="(?:all_action|action\[\])"/i', $sCheckbox)) {
                    continue;
                }

                ++$iCheckboxCount;
                $this->assertMatchesRegularExpression(
                    '/\baria-label="[^"]+"/i',
                    $sCheckbox,
                    $oFile->getPathname()
                );
            }
        }

        $this->assertGreaterThan(0, $iCheckboxCount);
    }

    /** @return iterable<SplFileInfo> */
    private function templateFiles(): iterable
    {
        $sProjectRoot = dirname(PH7_PATH_PROTECTED);

        foreach (['_protected/app', 'templates'] as $sDirectory) {
            $oFiles = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $sProjectRoot . '/' . $sDirectory,
                    FilesystemIterator::SKIP_DOTS
                )
            );

            /** @var SplFileInfo $oFile */
            foreach ($oFiles as $oFile) {
                if ($oFile->isFile() && $oFile->getExtension() === 'tpl') {
                    yield $oFile;
                }
            }
        }
    }
}
