<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc / Model / Engine
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine;

use FilesystemIterator;
use PH7\Framework\Mvc\Model\Engine\Db;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class DbTest extends TestCase
{
    private const SQL_SOURCE_DIRECTORIES = [
        '_protected/app',
        '_protected/framework',
        '_install/data/sql'
    ];

    public function testSqlCompatibilityRequirements(): void
    {
        $this->assertSame('8.0.0', Db::REQUIRED_SQL_VERSION);

        $sProjectRoot = dirname(__DIR__, 6);
        $aViolations = [];

        foreach (self::SQL_SOURCE_DIRECTORIES as $sRelativeDirectory) {
            foreach ($this->findSqlSourceFiles($sProjectRoot . '/' . $sRelativeDirectory) as $sSourceFile) {
                $sSource = file_get_contents($sSourceFile);

                if (is_string($sSource) && preg_match(
                    '/\bGROUP\s+BY\b(?:(?!\bORDER\s+BY\b|\bLIMIT\b|;).)*\b(?:ASC|DESC)\b/is',
                    $sSource
                )) {
                    $aViolations[] = $sSourceFile;
                }
            }
        }

        $this->assertSame(
            [],
            $aViolations,
            "These files use GROUP BY sort directions removed in MySQL 8.0.13:\n" . implode("\n", $aViolations)
        );
    }

    private function findSqlSourceFiles(string $sDirectory): array
    {
        $aFiles = [];
        $oIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sDirectory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($oIterator as $oFile) {
            if (!$oFile->isFile()) {
                continue;
            }

            $sExtension = strtolower($oFile->getExtension());
            if ($sExtension === 'php' || $sExtension === 'sql') {
                $aFiles[] = $oFile->getPathname();
            }
        }

        sort($aFiles);

        return $aFiles;
    }
}
