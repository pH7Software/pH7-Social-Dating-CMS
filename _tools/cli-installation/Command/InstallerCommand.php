<?php

declare(strict_types=1);

namespace PH7\Cli\Installer\Command;

use PDO;
use PDOException;
use PH7\Cli\Installer\Exception\FileNotWritableException;
use PH7\Cli\Installer\Exception\InvalidEmailException;
use PH7\Cli\Installer\Exception\InvalidLicenseAgreementException;
use PH7\Cli\Installer\Exception\Validation\InvalidPathException;
use PH7\Cli\Installer\Misc\DbDefaultConfig;
use PH7\Cli\Installer\Misc\Helper;
use PH7\Cli\Installer\Misc\MySQL;
use PH7\Cli\Installer\Misc\SqlQuery;
use PH7\Cli\Installer\Misc\Validation;
use PH7\DbTableName;
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
        $io = new SymfonyStyle($input, $output);

        try {
            $this->license($io);
        } catch (InvalidLicenseAgreementException $except) {
            $io->error($except->getMessage());
        }

        try {
            $this->configProtectedPath($io);
            try {
                $dbDetails = $this->getDatabaseSetup($io);
                $db = new MySQL([
                    'db_type' => MySQL::DSN_MYSQL_PREFIX,
                    'db_hostname' => $dbDetails['db_host'],
                    'db_name' => $dbDetails['db_name'],
                    'db_username' => $dbDetails['db_user'],
                    'db_password' => $dbDetails['db_password'],
                    'db_charset' => DbDefaultConfig::CHARSET
                ]);
            } catch (PDOException $except) {
                $io->error(
                    sprintf('Database error: %s', $except->getMessage())
                );

                return Command::FAILURE;
            }

            try {
                $appSettings = $this->getAppSettings($io);
            } catch (InvalidEmailException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }


            $this->buildAppConfigFile(
                array_merge($appSettings, $dbDetails)
            );

            $this->configureSite($io, $db);
        } catch (FileNotWritableException $except) {
            $io->error($except->getMessage());
        }

        $output->writeln(
            $io->success('The installation is now completed! 🤗')
        );

        return Command::SUCCESS;
    }

    private function license(SymfonyStyle $io): void
    {
        $io->section('License Agreement');

        $message = 'Do you agree to use the software at my own risk and that the author of this software cannot in any case be held liable for direct or indirect damage, nor for any other damage of any kind whatsoever, resulting from the use of this software or the impossibility to use it for any reason whatsoever? [y/n]';
        $answer = $io->choice($message, ['y' => 'yes', 'n' => 'no'], 'y');

        if ($answer === Answer::NO) {
            $message = "Before installing the software, you will have to agree with it.\n
            Come back later if you changed your mind.";

            throw new InvalidLicenseAgreementException($message);
        }
    }

    private function configProtectedPath(SymfonyStyle $io): void
    {
        $io->section('Protected Path');

        $protectedPath = $io->ask('Full path to the "protected" folder');

        if (is_file($protectedPath)) {
            if (is_readable($protectedPath)) {
                $constantContent = file_get_contents(self::ROOT_INSTALL . 'data/configs/constants.php');
            }
        } else {
            throw new InvalidPathException();
        }

        if (!@file_put_contents(self::ROOT_PROJECT . '_constants.php', $constantContent)) {
            throw new FileNotWritableException('Please change the permissions of the root public directory to write mode (CHMOD 777)');
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

    private function buildAppConfigFile(array $aData): void
    {
        @require_once self::ROOT_PROJECT . '_constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';

        // Config File
        @chmod(PH7_PATH_APP_CONFIG, 0777);
        $configContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/config.ini');

        $configContent = str_replace('%bug_report_email%', $aData['bug_report_email'], $configContent);
        $configContent = str_replace('%ffmpeg_path%', Helper::cleanString($aData['ffmpeg_path']), $configContent);

        $configContent = str_replace('%db_type_name%', MySQL::DBMS_MYSQL_NAME, $configContent);
        $configContent = str_replace('%db_type%', MySQL::DSN_MYSQL_PREFIX, $configContent);
        $configContent = str_replace('%db_hostname%', $aData['db_host'], $configContent);
        $configContent = str_replace('%db_username%', Helper::cleanString($aData['db_user']), $configContent);
        $configContent = str_replace('%db_password%', Helper::cleanString($aData['db_password']), $configContent);
        $configContent = str_replace('%db_name%', Helper::cleanString($aData['db_name']), $configContent);
        $configContent = str_replace('%db_prefix%', DbDefaultConfig::PREFIX, $configContent);
        $configContent = str_replace('%db_charset%', DbDefaultConfig::CHARSET, $configContent);
        $configContent = str_replace('%db_port%', DbDefaultConfig::PORT, $configContent);

        $configContent = str_replace('%private_key%', Helper::generateHash(40), $configContent);
        $configContent = str_replace('%rand_id%', Helper::generateHash(5), $configContent);

        if (@file_put_contents(PH7_PATH_APP_CONFIG . 'config.ini', $configContent)) {
            throw new FileNotWritableException('Please change the permissions for "protected/app/configs" directory to write mode (CHMOD 777)');
        }
    }

    private function configureSite(SymfonyStyle $io, PDO $db): void
    {
        $io->section('Admin Dashboard Configuration');

        $siteName = $io->ask('Site Name');

        $adminUsername = $io->ask('Admin Username');
        $adminPassword = $io->ask('Admin Password');
        $adminLoginEmail = $io->ask('Admin Login Email (to login to dashboard)');
        $adminEmail = $io->ask('Admin Email');
        $adminFirstName = $io->ask('Admin First Name');
        $adminLastName = $io->ask('Admin First Name');
        $adminFeedbackEmail = $io->ask('Contact Email');
        $noReplyEmail = $io->ask('No-reply Email');

        $rStmt = $db->prepare(
            sprintf(SqlQuery::ADD_ADMIN, DbDefaultConfig::PREFIX . DbTableName::ADMIN)
        );

        $sCurrentDate = date('Y-m-d H:i:s');
        $rStmt->execute([
            'username' => $adminUsername,
            'password' => Framework\Security\Security::hashPwd($adminPassword),
            'email' => $adminLoginEmail,
            'firstName' => $adminFirstName,
            'lastName' => $adminLastName,
            'joinDate' => $sCurrentDate,
            'lastActivity' => $sCurrentDate,
        ]);

        $rStmt = $db->prepare(
            sprintf(SqlQuery::UPDATE_SITE_NAME, DbDefaultConfig::PREFIX . DbTableName::SETTING)
        );
        $rStmt->execute(['siteName' => $siteName]);

        $rStmt = $db->prepare(
            sprintf(SqlQuery::UPDATE_ADMIN_EMAIL, DbDefaultConfig::PREFIX . DbTableName::SETTING)
        );
        $rStmt->execute(['adminEmail' => $adminEmail]);

        $rStmt = $db->prepare(
            sprintf(SqlQuery::UPDATE_FEEDBACK_EMAIL, DbDefaultConfig::PREFIX . DbTableName::SETTING)
        );
        $rStmt->execute(['feedbackEmail' => $adminFeedbackEmail]);

        $rStmt = $db->prepare(
            sprintf(SqlQuery::UPDATE_RETURN_EMAIL, DbDefaultConfig::PREFIX . DbTableName::SETTING)
        );
        $rStmt->execute(['returnEmail' => $noReplyEmail]);
    }
}
