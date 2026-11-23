<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Cli / Command
 */

declare(strict_types=1);

namespace PH7\Cli\Command;

use PH7\Cli\Migration\CsvMemberWriter;
use PH7\Cli\Migration\UnaMemberMapper;
use PH7\Cli\Migration\UnaMemberSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UnaMigrationExportCommand extends Command
{
    private const DEFAULT_BATCH_SIZE = 500;
    private const SOURCE_DSN_ENV = 'PH7_MIGRATION_SOURCE_DSN';
    private const SOURCE_USER_ENV = 'PH7_MIGRATION_SOURCE_USER';
    private const SOURCE_PASSWORD_ENV = 'PH7_MIGRATION_SOURCE_PASSWORD';

    protected function configure(): void
    {
        $this
            ->setName('migration:una:export')
            ->setDescription('Preview or export UNA 14.0.0 members to the pH7Builder migration CSV format.')
            ->addOption(
                'dsn',
                null,
                InputOption::VALUE_REQUIRED,
                'Read-only UNA MySQL PDO DSN. Defaults to ' . self::SOURCE_DSN_ENV . '.'
            )
            ->addOption(
                'user',
                null,
                InputOption::VALUE_REQUIRED,
                'Read-only UNA database username. Defaults to ' . self::SOURCE_USER_ENV . '.'
            )
            ->addOption(
                'table-prefix',
                null,
                InputOption::VALUE_REQUIRED,
                'Optional UNA table prefix.',
                ''
            )
            ->addOption(
                'output',
                null,
                InputOption::VALUE_REQUIRED,
                'New CSV path. Omit it to run a read-only preview.'
            )
            ->addOption(
                'after-id',
                null,
                InputOption::VALUE_REQUIRED,
                'Resume after this UNA account ID.',
                '0'
            )
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_REQUIRED,
                'Source rows per batch (1-5000).',
                (string)self::DEFAULT_BATCH_SIZE
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Maximum exported rows; 0 means all compatible members.',
                '0'
            );
    }

    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        $oIo = new SymfonyStyle($oInput, $oOutput);
        $sOutputPath = $this->normalizeOptionalString($oInput->getOption('output'));
        $oWriter = null;
        $oDb = null;
        $bOutputCreated = false;

        try {
            $sDsn = $this->resolveRequiredSetting(
                $this->normalizeOptionalString($oInput->getOption('dsn')),
                self::SOURCE_DSN_ENV,
                'dsn'
            );
            if (!str_starts_with($sDsn, 'mysql:')) {
                throw new \RuntimeException('The UNA source DSN must use the mysql PDO driver.');
            }

            $sUser = $this->resolveRequiredSetting(
                $this->normalizeOptionalString($oInput->getOption('user')),
                self::SOURCE_USER_ENV,
                'user'
            );
            $sPassword = getenv(self::SOURCE_PASSWORD_ENV);
            if (!is_string($sPassword)) {
                throw new \RuntimeException('Set ' . self::SOURCE_PASSWORD_ENV . ' instead of placing a database password in shell history.');
            }

            $iAfterId = $this->normalizeNonNegativeInteger($oInput->getOption('after-id'), 'after-id');
            $iBatchSize = $this->normalizeBatchSize($oInput->getOption('batch-size'));
            $iLimit = $this->normalizeNonNegativeInteger($oInput->getOption('limit'), 'limit');
            $sTablePrefix = trim((string)$oInput->getOption('table-prefix'));

            $aPdoOptions = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ];
            if (class_exists(\Pdo\Mysql::class)) {
                $aPdoOptions[\Pdo\Mysql::ATTR_USE_BUFFERED_QUERY] = false;
            } else {
                $aPdoOptions[\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = false;
            }

            $oDb = new \PDO(
                $sDsn,
                $sUser,
                $sPassword,
                $aPdoOptions
            );
            $oDb->exec('SET SESSION TRANSACTION READ ONLY');
            $oDb->beginTransaction();

            $oSource = new UnaMemberSource($oDb, $sTablePrefix);
            $oSource->assertCompatible();
            $iDetected = $oSource->countMembers($iAfterId);
            $oMapper = new UnaMemberMapper();

            if ($sOutputPath !== null) {
                $oWriter = new CsvMemberWriter($sOutputPath);
                $bOutputCreated = true;
            }

            [$iExportable, $iSkipped, $iLastSourceId] = $this->processMembers(
                $oSource,
                $oMapper,
                $oWriter,
                $iAfterId,
                $iBatchSize,
                $iLimit
            );

            $oWriter?->close();
            $oDb->commit();

            $oIo->table(
                ['Check', 'Result'],
                [
                    ['Source contract', 'UNA 14.0.0 account and Persons schema'],
                    ['Detected after checkpoint', (string)$iDetected],
                    [$sOutputPath === null ? 'Importable preview' : 'Exported', (string)$iExportable],
                    ['Skipped invalid emails', (string)$iSkipped],
                    ['Last source account ID', (string)$iLastSourceId]
                ]
            );

            if ($sOutputPath === null) {
                $oIo->success('Preview completed without modifying the UNA or pH7Builder databases.');
                $oIo->note('Pass --output=/secure/path/members.csv to create the canonical migration file.');
            } else {
                $oIo->success('The migration CSV was created with owner-only file permissions.');
                $oIo->warning(
                    'UNA password hashes were not copied. Imported members must use pH7Builder password recovery.'
                );
            }

            return Command::SUCCESS;
        } catch (\Throwable $oException) {
            $oWriter?->close();
            if ($oDb instanceof \PDO && $oDb->inTransaction()) {
                $oDb->rollBack();
            }
            if ($bOutputCreated && $sOutputPath !== null && is_file($sOutputPath)) {
                @unlink($sOutputPath);
            }

            $oIo->error($oException->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * @return array{int, int, int}
     */
    private function processMembers(
        UnaMemberSource $oSource,
        UnaMemberMapper $oMapper,
        ?CsvMemberWriter $oWriter,
        int $iAfterId,
        int $iBatchSize,
        int $iLimit
    ): array {
        $iExportable = 0;
        $iSkipped = 0;
        $iLastSourceId = $iAfterId;

        while ($iLimit === 0 || $iExportable < $iLimit) {
            $iRemaining = $iLimit > 0 ? $iLimit - $iExportable : $iBatchSize;
            $iCurrentBatchSize = min($iBatchSize, max(1, $iRemaining));
            $aBatch = $oSource->fetchBatch($iLastSourceId, $iCurrentBatchSize);
            if ($aBatch === []) {
                break;
            }

            foreach ($aBatch as $aSourceMember) {
                $iLastSourceId = (int)$aSourceMember['source_id'];
                $aMember = $oMapper->map($aSourceMember);
                if (filter_var($aMember['email'], FILTER_VALIDATE_EMAIL) === false) {
                    ++$iSkipped;
                    continue;
                }

                $oWriter?->append($aMember);
                ++$iExportable;
                if ($iLimit > 0 && $iExportable >= $iLimit) {
                    break 2;
                }
            }
        }

        return [$iExportable, $iSkipped, $iLastSourceId];
    }

    private function resolveRequiredSetting(
        ?string $sOption,
        string $sEnvironmentName,
        string $sOptionName
    ): string {
        if ($sOption !== null) {
            return $sOption;
        }

        $sValue = getenv($sEnvironmentName);
        if (!is_string($sValue) || trim($sValue) === '') {
            throw new \RuntimeException('Provide --' . $sOptionName . ' or set ' . $sEnvironmentName . '.');
        }

        return trim($sValue);
    }

    private function normalizeOptionalString(mixed $mValue): ?string
    {
        if (!is_string($mValue) || trim($mValue) === '') {
            return null;
        }

        return trim($mValue);
    }

    private function normalizeNonNegativeInteger(mixed $mValue, string $sName): int
    {
        if (filter_var($mValue, FILTER_VALIDATE_INT) === false || (int)$mValue < 0) {
            throw new \RuntimeException('--' . $sName . ' must be a non-negative integer.');
        }

        return (int)$mValue;
    }

    private function normalizeBatchSize(mixed $mValue): int
    {
        $iBatchSize = $this->normalizeNonNegativeInteger($mValue, 'batch-size');
        if ($iBatchSize < 1 || $iBatchSize > 5000) {
            throw new \RuntimeException('--batch-size must be between 1 and 5000.');
        }

        return $iBatchSize;
    }
}
