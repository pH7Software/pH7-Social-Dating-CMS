<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Cli\Command;

use PH7\Cli\Health\HealthReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class HealthReportCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('health:report')
            ->setDescription('Inspect this installation without changing it')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Return machine-readable JSON');
    }

    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        $aChecks = (new HealthReport(PH7_ROOT_PROJECT))->collect();
        if ((bool)$oInput->getOption('json')) {
            $oOutput->writeln(json_encode($aChecks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        } else {
            $oIo = new SymfonyStyle($oInput, $oOutput);
            $oIo->title('pH7Builder operator health report');
            $oIo->table(
                ['Status', 'Check', 'Detail'],
                array_map(
                    static fn (array $aCheck): array => [
                        strtoupper($aCheck['status']),
                        $aCheck['name'],
                        $aCheck['detail']
                    ],
                    $aChecks
                )
            );
            $oIo->note('This read-only report cannot prove external cron execution, email delivery, backups, or live providers.');
        }

        return HealthReport::hasFailures($aChecks) ? Command::FAILURE : Command::SUCCESS;
    }
}
