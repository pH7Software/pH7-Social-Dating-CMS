<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ProductionErrorReportingTest extends TestCase
{
    private const ERROR_SUPPRESSION_PATTERN = '/error_reporting\s*\(\s*0\s*\)/';

    public function testProductionLogsAllErrorsWithoutDisplayingThem(): void
    {
        $sSource = $this->readProjectFile('_protected/app/configs/environment/production.env.php');

        $this->assertStringContainsString('error_reporting(E_ALL);', $sSource);
        $this->assertStringContainsString("ini_set('log_errors', 'On');", $sSource);
        $this->assertStringContainsString("ini_set('display_errors', PH7_ENV_DISABLED);", $sSource);
        $this->assertStringContainsString("ini_set('display_startup_errors', PH7_ENV_DISABLED);", $sSource);
        $this->assertDoesNotMatchRegularExpression(self::ERROR_SUPPRESSION_PATTERN, $sSource);
    }

    public function testApplicationCodeDoesNotDisableErrorReportingForTheRequest(): void
    {
        $sAppPath = $this->projectRoot() . '/_protected/app';
        $oFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sAppPath, FilesystemIterator::SKIP_DOTS)
        );
        $aSuppressionFiles = [];

        foreach ($oFiles as $oFile) {
            if (!$oFile->isFile() || $oFile->getExtension() !== 'php') {
                continue;
            }

            $sSource = file_get_contents($oFile->getPathname());
            if (is_string($sSource) && preg_match(self::ERROR_SUPPRESSION_PATTERN, $sSource) === 1) {
                $aSuppressionFiles[] = $oFile->getPathname();
            }
        }

        $this->assertSame([], $aSuppressionFiles, 'Application files must not disable request-wide error reporting.');
    }

    private function readProjectFile(string $sPath): string
    {
        $sContents = file_get_contents($this->projectRoot() . '/' . $sPath);
        $this->assertIsString($sContents);

        return $sContents;
    }

    private function projectRoot(): string
    {
        return dirname(__DIR__, 3);
    }
}
