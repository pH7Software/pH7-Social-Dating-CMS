<?php

declare(strict_types=1);

namespace PH7\Cli\Installer\Command;

use PDOException;
use PH7\Cli\Installer\Exception\InvalidEmailException;
use PH7\Cli\Installer\Exception\Validation\InvalidPathException;
use PH7\Cli\Installer\Misc\Database;
use PH7\Cli\Installer\Misc\Helper;
use PH7\Cli\Installer\Misc\Validation;
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
        $this->configProtectedPath();
        try {
            $dbDetails = $this->getDatabaseSetup();
            $db = new Database([
                'db_type' => Database::DSN_MYSQL_PREFIX,
                'db_hostname' => $dbDetails['db_host'],
                'db_name' => $dbDetails['db_name'],
                'db_username' => $dbDetails['db_user'],
                'db_password' => $dbDetails['db_password'],
            ]);
        } catch (PDOException $except) {
            $io->error(
                sprintf('Database error: %s', $except->getMessage())
            );

            return Command::FAILURE;
        }

        try {
            $appSettings = $this->getAppSettings();
        } catch (InvalidEmailException $except) {
            $io->error($except->getMessage());

            return Command::FAILURE;
        }


        $this->buildAppConfigFile(
            array_merge($appSettings, $dbDetails)
        );


        $output->writeln(
            $io->success('The installation is now completed')
        );

        return Command::SUCCESS;
    }

    private function license(SymfonyStyle $io): int
    {
        $io->section('License Agreement');

        $message = 'Do you agree to use the software at my own risk and that the author of this software cannot in any case be held liable for direct or indirect damage, nor for any other damage of any kind whatsoever, resulting from the use of this software or the impossibility to use it for any reason whatsoever? [y/n]';
        $answer = $io->choice($message, ['y' => 'yes', 'n' => 'no'], 'y');

        if ($answer === Answer::NO) {
            $io->error('Before installing the software, you will have to agree with it.');
            $io->error('Come back later if you changed your mind.');

            return Command::INVALID;
        }

        return Command::SUCCESS;
    }

    private function configProtectedPath(SymfonyStyle $io): int
    {
        $io->section('Protected Path');

        $sProtectedPath = $io->ask('Full path to the "protected" folder');

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
            'db_host' => $dbHostName,
            'db_user' => $dbUser,
            'db_password' => $dbPassword,
            'db_name' => $dbName
        ];
    }

    private function getAppSettings(SymfonyStyle $io): array
    {
        $io->section('Application Settings');

        $fFmpeg = $io->ask('Optional. The path to the FFmpeg executable', Helper::getFfmpegPath());
        $bugReportEmail = $io->ask('Bug reports email');

        $validation = new Validation($bugReportEmail);
        if (!$validation->isValidEmail()) {
            throw new InvalidEmailException('Email not valid. Please enter a valid email.');
        }

        return [
            $fFmpeg,
            $bugReportEmail
        ];
    }

    private function buildAppConfigFile(array $aData)
    {
        @require_once self::ROOT_PROJECT . '_constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';

        // Config File
        @chmod(PH7_PATH_APP_CONFIG, 0777);
        $sConfigContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/config.ini');

        $sConfigContent = str_replace('%bug_report_email%', $aData['bug_report_email'], $sConfigContent);
        $sConfigContent = str_replace('%ffmpeg_path%', Helper::cleanString($aData['ffmpeg_path']), $sConfigContent);

        $sConfigContent = str_replace('%db_type_name%', Database::DBMS_MYSQL_NAME, $sConfigContent);
        $sConfigContent = str_replace('%db_type%', Database::DSN_MYSQL_PREFIX, $sConfigContent);
        $sConfigContent = str_replace('%db_hostname%', $aData['db_name'], $sConfigContent);
        $sConfigContent = str_replace('%db_username%', Helper::cleanString($aData['db_user']), $sConfigContent);
        $sConfigContent = str_replace('%db_password%', Helper::cleanString($aData['db_password']), $sConfigContent);
        $sConfigContent = str_replace('%db_name%', Helper::cleanString($aData['db_name']), $sConfigContent);
        $sConfigContent = str_replace('%db_prefix%', 'ph7_', $sConfigContent);
        $sConfigContent = str_replace('%db_charset%', Database::CHARSET, $sConfigContent);
        $sConfigContent = str_replace('%db_port%', Database::PORT, $sConfigContent);

        $sConfigContent = str_replace('%private_key%', Helper::generateHash(40), $sConfigContent);
        $sConfigContent = str_replace('%rand_id%', Helper::generateHash(5), $sConfigContent);

        return @file_put_contents(PH7_PATH_APP_CONFIG . 'config.ini', $sConfigContent);
    }
}
