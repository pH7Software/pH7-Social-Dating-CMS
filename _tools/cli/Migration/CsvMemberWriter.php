<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Cli / Migration
 */

declare(strict_types=1);

namespace PH7\Cli\Migration;

final class CsvMemberWriter
{
    /** @var resource|false */
    private $rFile;
    private string $sOutputPath;

    public function __construct(string $sOutputPath)
    {
        $this->sOutputPath = $sOutputPath;
        $this->rFile = @fopen($sOutputPath, 'xb');
        if ($this->rFile === false) {
            throw new \RuntimeException('The migration export path already exists or cannot be created.');
        }

        try {
            @chmod($sOutputPath, 0600);
            $this->writeRow(UnaMemberMapper::CSV_HEADERS);
        } catch (\RuntimeException $oException) {
            $this->close();
            @unlink($this->sOutputPath);

            throw $oException;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param array<string, string> $aMember
     */
    public function append(array $aMember): void
    {
        $aRow = [];
        foreach (UnaMemberMapper::CSV_HEADERS as $sHeader) {
            $aRow[] = $aMember[$sHeader] ?? '';
        }

        $this->writeRow($aRow);
    }

    public function close(): void
    {
        if (is_resource($this->rFile)) {
            fflush($this->rFile);
            fclose($this->rFile);
        }

        $this->rFile = false;
    }

    private function writeRow(array $aRow): void
    {
        if (!is_resource($this->rFile) || fputcsv($this->rFile, $aRow, ',', '"', '\\') === false) {
            throw new \RuntimeException('The migration CSV could not be written completely.');
        }
    }
}
