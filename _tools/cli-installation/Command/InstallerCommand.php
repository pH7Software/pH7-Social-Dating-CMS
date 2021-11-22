<?php

declare(strict_types=1);

namespace PH7\Cli\Installer\Command;

use PH7\Cli\Installer\Cli\Helper;
use PH7\Cli\Installer\Exception\Validation\InvalidPathException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallerCommand extends Command
{
    protected const SOFTWARE_NAME = 'pH7CMS';
    private const ROOT_INSTALL = __DIR__ . DIRECTORY_SEPARATOR;
    private const ROOT_PROJECT = __DIR__ . '../../../';

    protected function configure(): void
    {
        $this->setName('install:run')
            ->setDescription(sprintf('Installing %s, as simple as possible!', self::SOFTWARE_NAME));

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $this->license($io);

        return Command::FAILURE;

    }

    private function license(SymfonyStyle $io): int
    {
        $io->section('License Agreement');

        $message = 'Do you agree to use the software at my own risk and that the author of this software cannot in any case be held liable for direct or indirect damage, nor for any other damage of any kind whatsoever, resulting from the use of this software or the impossibility to use it for any reason whatsoever? [y/n]';
        $response = $io->choice($message, ['y' => 'yes', 'n' => 'no'], 'y');

        if ($response === 'n') {
            $io->error('Before installing the software, you will have to agree with it.');
            $io->error('Come back later if you changed your mind.');

            return Command::INVALID;
        }

        return Command::SUCCESS;
    }

    private function configPath(SymfonyStyle $io): int
    {
        $io->section('Protected Path');

        $sProtectedPath = $io->ask('Full path of the "protected" directory');

        if (is_file($sProtectedPath)) {
            if (is_readable($sProtectedPath)) {
                $sConstantContent = file_get_contents(self::ROOT_INSTALL . 'data/configs/constants.php');
            }
        } else {
            throw new InvalidPathException();
        }

        if (!@file_put_contents(self::ROOT_PROJECT . '_constants.php', $sConstantContent)) {
            $io->error('Please change the permissions of the root public directory to write mode (CHMOD 777)');

            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }
    }

    private function getDatabaseSetup(SymfonyStyle $io): array
    {
        $io->section('Database Configuration');

        $dbHostName = $io->ask('Database Host Name');
        $dbUser = $io->ask('Database User');
        $dbPassword = $io->ask('Database Password');
        $dbName = $io->ask('Database Name');

        return [
            $dbHostName,
            $dbUser,
            $dbPassword,
            $dbName
        ];
    }

    private function getApplicationSettings(SymfonyStyle $io): array
    {
        $io->section('Application Settings');

        $fFmpeg = $io->ask('Optional. The path to the FFmpeg executable', Helper::getFfmpegPath());
        $bugReportEmail = $io->ask('Bug reports email');

        return [
            $fFmpeg,
            $bugReportEmail
        ];
    }
}
