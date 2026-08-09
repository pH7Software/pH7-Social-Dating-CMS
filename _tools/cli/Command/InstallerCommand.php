<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Cli / Command
 */

declare(strict_types=1);

namespace PH7\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class InstallerCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('setup:install')
            ->setDescription('Show the supported pH7Builder installation path');
    }

    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        $oIo = new SymfonyStyle($oInput, $oOutput);
        $oIo->warning(
            'The legacy CLI installer is disabled because it could leave a partial installation.'
        );
        $oIo->writeln(
            'Deploy the official release, run <info>composer install --no-dev --optimize-autoloader</info>, ' .
            'then open <info>https://your-domain.example/_install/</info>.'
        );
        $oIo->writeln('The browser installer validates requirements, creates the schema and admin, then removes itself.');

        return Command::FAILURE;
    }
}
