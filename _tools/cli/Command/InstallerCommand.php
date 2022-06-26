<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace PH7\Cli\Command;

use Exception;
use PDO;
use PDOException;
use PH7\Cli\Exception\FileNotWritableException;
use PH7\Cli\Exception\InvalidEmailException;
use PH7\Cli\Exception\InvalidLicenseAgreementException;
use PH7\Cli\Exception\Validation\InvalidPathException;
use PH7\Cli\Misc\Database\DbDefaultConfig;
use PH7\Cli\Misc\Database\MySQL;
use PH7\Cli\Misc\Database\SqlQuery;
use PH7\Cli\Misc\Helper;
use PH7\Cli\Misc\Validation;
use PH7\DbTableName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallerCommand extends Command
{
    protected const SOFTWARE_NAME = 'pH7CMS';

    private const ROOT_PROJECT = PH7_ROOT_PROJECT;
    private const INSTALL_DIR_NAME = '_install/';

    private const SUCCESS_MESSAGE = 'The installation is now completed! 🤗';

    protected function configure(): void
    {
        $this->setName('setup:install')
            ->setDescription(sprintf('Installing %s, as simple as possible!', self::SOFTWARE_NAME));

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            try {
                $this->license($io);
            } catch (InvalidLicenseAgreementException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }

            try {
                $this->configProtectedPath();
            } catch (InvalidPathException | FileNotWritableException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }

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
                    sprintf('DB error: %s', $except->getMessage())
                );

                return Command::FAILURE;
            }

            try {
                $appSettings = $this->getAppSettings($io);
            } catch (InvalidEmailException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }

            try {
                $this->buildAppConfigFile(
                    array_merge($appSettings, $dbDetails)
                );
            } catch (FileNotWritableException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }

            try {
                $this->configureSite($io, $db);
            } catch (PDOException $except) {
                $io->error(
                    sprintf('MySQL error: %s', $except->getMessage())
                );

                return Command::FAILURE;
            } catch (InvalidEmailException $except) {
                $io->error($except->getMessage());

                return Command::FAILURE;
            }

            $output->writeln(
                $io->success(self::SUCCESS_MESSAGE)
            );

            return Command::SUCCESS;
        } catch (Exception $except) {
            $io->error($except->getMessage());
            $io->writeln('Please try again 😊');
            $io->writeln('Or report any bugs/issues at:');
            $io->writeln('https://github.com/pH7Software/pH7-Social-Dating-CMS/issues');

            return Command::FAILURE;
        }
    }

    private function license(SymfonyStyle $io): void
    {
        $io->section('License Agreement');

        $message = 'Do you agree to use the software at my own risk and that the author of this software cannot in any case be held liable for direct or indirect damage, nor for any other damage of any kind whatsoever, resulting from the use of this software or the impossibility to use it for any reason whatsoever? [y/n]';
        $answer = $io->choice($message, ['y' => 'yes', 'n' => 'no'], 'y');

        if ($answer === Answer::NO) {
            $message = "Before installing the software, you will have to agree with it.\n
            Come back later if you change your mind.";

            throw new InvalidLicenseAgreementException($message);
        }
    }

    private function configProtectedPath(): void
    {
        $protectedPath = self::ROOT_PROJECT . PH7_PROTECTED_DIR_NAME;
        if (is_file($protectedPath)) {
            if (is_readable($protectedPath)) {
                $constantContent = file_get_contents(self::ROOT_PROJECT . self::INSTALL_DIR_NAME . 'data/configs/constants.php');
                $constantContent = str_replace('%path_protected%', addslashes($protectedPath), $constantContent);

                if (!@file_put_contents(self::ROOT_PROJECT . '_constants.php', $constantContent)) {
                    throw new FileNotWritableException('Please change the permissions of the public root directory to write mode (CHMOD 777)');
                }
            }

            throw new InvalidPathException('The protected directory wasn\'t found or doesn\'t have the right (CHMOD 777) writing permission');
        }
    }

    private function getDatabaseSetup(SymfonyStyle $io): array
    {
        $io->section('Database Configuration');

        $dbHostName = $io->ask('Database Host Name (e.g. localhost)');
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

        $currentFfmpegPath = Helper::getFfmpegPath();
        $ffmpegPath = $io->ask(sprintf('Optional. The path to the FFmpeg executable [%s]', $currentFfmpegPath), $currentFfmpegPath);
        $bugReportEmail = $io->ask('Bug reports email');

        $this->checkEmailAddress($bugReportEmail);

        return [
            'bug_report_email' => $bugReportEmail,
            'ffmpeg_path' => $ffmpegPath
        ];
    }

    private function checkEmailAddress(?string $email): void
    {
        $validation = new Validation($email);
        if (!$validation->isValidEmail()) {
            throw new InvalidEmailException(
                sprintf('%s is not a valid email. Please retry with a valid email.', $email)
            );
        }
    }

    private function buildAppConfigFile(array $data): void
    {
        @require_once self::ROOT_PROJECT . '_constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';

        // Config File
        @chmod(PH7_PATH_APP_CONFIG, 0777);
        $configContent = file_get_contents(self::ROOT_PROJECT . self::INSTALL_DIR_NAME . 'data/configs/config.ini');

        $configContent = str_replace('%bug_report_email%', $data['bug_report_email'], $configContent);
        $configContent = str_replace('%ffmpeg_path%', Helper::cleanString($data['ffmpeg_path']), $configContent);

        $configContent = str_replace('%db_type_name%', MySQL::DBMS_MYSQL_NAME, $configContent);
        $configContent = str_replace('%db_type%', MySQL::DSN_MYSQL_PREFIX, $configContent);
        $configContent = str_replace('%db_hostname%', $data['db_host'], $configContent);
        $configContent = str_replace('%db_username%', Helper::cleanString($data['db_user']), $configContent);
        $configContent = str_replace('%db_password%', Helper::cleanString($data['db_password']), $configContent);
        $configContent = str_replace('%db_name%', Helper::cleanString($data['db_name']), $configContent);
        $configContent = str_replace('%db_prefix%', DbDefaultConfig::PREFIX, $configContent);
        $configContent = str_replace('%db_charset%', DbDefaultConfig::CHARSET, $configContent);
        $configContent = str_replace('%db_port%', DbDefaultConfig::PORT, $configContent);

        $configContent = str_replace('%private_key%', Helper::generateHash(40), $configContent);
        $configContent = str_replace('%rand_id%', Helper::generateHash(5), $configContent);

        if (@file_put_contents(PH7_PATH_APP_CONFIG . 'config.ini', $configContent)) {
            throw new FileNotWritableException('Please change the permissions for "protected/app/configs" to write mode (CHMOD 777)');
        }
    }

    private function configureSite(SymfonyStyle $io, PDO $db): void
    {
        $io->section('Admin Dashboard Configuration');

        $siteName = (string)$io->ask('Site Name (optional)');

        $adminUsername = $io->ask('Admin Username');
        $adminPassword = $io->ask('Admin Password');
        $adminLoginEmail = $io->ask('Admin Login Email (to login to dashboard)');
        $adminEmail = $io->ask('Admin Email');
        $adminFirstName = $io->ask('Admin First Name');
        $adminLastName = $io->ask('Admin Last Name');
        $adminFeedbackEmail = $io->ask('Contact Email');
        $noReplyEmail = $io->ask('No-reply Email');

        // Validate the fields
        foreach ([$adminLoginEmail, $adminEmail] as $email) {
            $this->checkEmailAddress($email);
        }

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

        // Only update the default site name if it was mentioned
        if ($this->isSiteNameFieldNotEmpty($siteName)) {
            $rStmt = $db->prepare(
                sprintf(SqlQuery::UPDATE_SITE_NAME, DbDefaultConfig::PREFIX . DbTableName::SETTING)
            );
            $rStmt->execute(['siteName' => $siteName]);
        }

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

    private function isSiteNameFieldNotEmpty(?string $siteName): bool
    {
        return empty($siteName) || strlen($siteName) <= 1;
    }
}
