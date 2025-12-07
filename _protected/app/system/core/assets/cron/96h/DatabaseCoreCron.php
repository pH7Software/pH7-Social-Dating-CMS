<?php
/**
 * @title            Database Cron Class
 * @desc             Database Periodic Cron.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 * @version          1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PDO;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Backup;
use PH7\Framework\Mvc\Model\Engine\Util\Various as DbVarious;
use PH7\JustHttp\StatusCode;

class DatabaseCoreCron extends Cron
{
    public function __construct()
    {
        parent::__construct();

        if ($this->httpRequest->getExists('option')) {
            $this->executeRequestedOption();
        }

        $this->cleanOldData();
        $this->removeMessagesDeletedByBothParties();
        $this->optimizeDatabaseTables();

        echo '<br />' . t('Cron job finished!');
    }

    private function executeRequestedOption(): void
    {
        $sOption = $this->httpRequest->get('option');

        switch ($sOption) {
            case 'backup':
                $this->createDatabaseBackup();
                break;

            case 'stat':
                $this->resetAllStatisticsToZero();
                break;

            case 'repair':
                $this->repairDatabaseTables();
                break;

            case 'remove_log':
                $this->deleteAllLogTables();
                break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
    }

    private function resetAllStatisticsToZero(): void
    {
        $this->resetStatisticsForTable(DbTableName::MEMBER);
        $this->resetStatisticsForTable(DbTableName::PICTURE);
        $this->resetStatisticsForTable(DbTableName::ALBUM_PICTURE);
        $this->resetStatisticsForTable(DbTableName::VIDEO);
        $this->resetStatisticsForTable(DbTableName::ALBUM_VIDEO);
        $this->resetStatisticsForTable(DbTableName::BLOG);
        $this->resetStatisticsForTable(DbTableName::NOTE);
        $this->resetForumTopicViews();
        $this->resetAdvertisementStatistics();

        echo t('Restart Statistics... OK!') . '<br />';
    }

    private function resetStatisticsForTable(string $sTableName): void
    {
        $oDatabase = Db::getInstance();
        $sTablePrefix = Db::prefix($sTableName);

        $oDatabase->exec("UPDATE {$sTablePrefix} SET views=0");
        $oDatabase->exec("UPDATE {$sTablePrefix} SET votes=0");
        $oDatabase->exec("UPDATE {$sTablePrefix} SET score=0");
    }

    private function resetForumTopicViews(): void
    {
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::FORUM_TOPIC) . 'SET views=0');
    }

    private function resetAdvertisementStatistics(): void
    {
        $oDatabase = Db::getInstance();
        $sAdTablePrefix = Db::prefix(DbTableName::AD);

        $oDatabase->exec("UPDATE {$sAdTablePrefix} SET views=0");
        $oDatabase->exec("UPDATE {$sAdTablePrefix} SET clicks=0");
    }

    private function createDatabaseBackup(): void
    {
        $sBackupFilename = $this->generateBackupFilename();
        $sBackupPath = PH7_PATH_BACKUP_SQL . $sBackupFilename;

        (new Backup($sBackupPath))->back()->saveArchive();

        echo t('Backup of the Database... Ok!') . '<br />';
    }

    private function generateBackupFilename(): string
    {
        $sCurrentDate = (new CDateTime)->get()->date();
        return "Periodic-database-update.{$sCurrentDate}.sql.gz";
    }

    private function optimizeDatabaseTables(): void
    {
        Db::optimize();
        echo t('Optimizing tables... OK!') . '<br />';
    }

    private function repairDatabaseTables(): void
    {
        Db::repair();
        echo t('Repair Database... Ok!') . '<br />';
    }

    private function removeMessagesDeletedByBothParties(): void
    {
        $sSqlQuery = 'DELETE FROM' . Db::prefix(DbTableName::MESSAGE) .
                     'WHERE FIND_IN_SET(\'sender\', toDelete) AND FIND_IN_SET(\'recipient\', toDelete)';

        $oStatement = Db::getInstance()->prepare($sSqlQuery);

        if ($oStatement->execute()) {
            $iDeletedCount = $oStatement->rowCount();
            echo nt('Deleted %n% temporary message... OK!', 'Deleted %n% temporary messages... OK!', $iDeletedCount) . '<br />';
        }
    }

    private function deleteAllLogTables(): void
    {
        $oDatabase = Db::getInstance();

        $oDatabase->exec('START TRANSACTION');

        try {
            $this->truncateLoginAttemptTables($oDatabase);
            $this->truncateLoginLogTables($oDatabase);
            $this->truncateSessionLogTables($oDatabase);
            $this->truncateErrorLogTable($oDatabase);

            $oDatabase->exec('COMMIT');
        } catch (\Exception $oException) {
            $oDatabase->exec('ROLLBACK');
            throw $oException;
        }

        echo t('Deleting Log... OK!') . '<br />';
    }

    private function truncateLoginAttemptTables(Db $oDatabase): void
    {
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_ATTEMPT_LOGIN));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_ATTEMPT_LOGIN));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_ATTEMPT_LOGIN));
    }

    private function truncateLoginLogTables(Db $oDatabase): void
    {
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_LOG_LOGIN));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_LOG_LOGIN));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_LOG_LOGIN));
    }

    private function truncateSessionLogTables(Db $oDatabase): void
    {
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_LOG_SESS));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_LOG_SESS));
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_LOG_SESS));
    }

    private function truncateErrorLogTable(Db $oDatabase): void
    {
        $oDatabase->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::LOG_ERROR));
    }

    private function cleanOldData(): void
    {
        $iDaysToKeepComments = (int)DbConfig::getSetting('cleanComment');
        $iDaysToKeepMessages = (int)DbConfig::getSetting('cleanMsg');
        $iDaysToKeepMessenger = (int)DbConfig::getSetting('cleanMessenger');

        if ($iDaysToKeepComments > 0) {
            $this->deleteOldComments($iDaysToKeepComments);
        }

        if ($iDaysToKeepMessages > 0) {
            $this->deleteOldMessages($iDaysToKeepMessages);
        }

        if ($iDaysToKeepMessenger > 0) {
            $this->deleteOldInstantMessages($iDaysToKeepMessenger);
        }
    }

    private function deleteOldComments(int $iDaysToKeep): void
    {
        $aCommentTypes = ['blog', 'note', 'picture', 'video', 'profile'];

        foreach ($aCommentTypes as $sCommentType) {
            $sTableName = CommentCoreModel::TABLE_PREFIX_NAME . $sCommentType;
            $iDeletedRows = $this->deleteOldRecordsFromTable($iDaysToKeep, $sTableName, 'updatedDate');

            if ($iDeletedRows > 0) {
                echo t('Deleted %0% %1% comment(s) ... OK!', $iDeletedRows, $sCommentType) . '<br />';
            }
        }
    }

    private function deleteOldMessages(int $iDaysToKeep): void
    {
        $iDeletedRows = $this->deleteOldRecordsFromTable($iDaysToKeep, DbTableName::MESSAGE, 'sendDate');

        if ($iDeletedRows > 0) {
            echo nt('Deleted %n% message... OK!', 'Deleted %n% messages... OK!', $iDeletedRows) . '<br />';
        }
    }

    private function deleteOldInstantMessages(int $iDaysToKeep): void
    {
        $iDeletedRows = $this->deleteOldRecordsFromTable($iDaysToKeep, DbTableName::MESSENGER, 'sent');

        if ($iDeletedRows > 0) {
            echo nt('Deleted %n% IM message... OK!', 'Deleted %n% IM messages... OK!', $iDeletedRows) . '<br />';
        }
    }

    private function deleteOldRecordsFromTable(int $iDaysOld, string $sTableName, string $sDateColumnName): int
    {
        if ($this->isInvalidTableForCleaning($sTableName)) {
            DbVarious::launchErr($sTableName);
        }

        $sSqlQuery = 'DELETE FROM' . Db::prefix($sTableName) .
                     "WHERE ({$sDateColumnName} < NOW() - INTERVAL :dayNumber DAY)";

        $oStatement = Db::getInstance()->prepare($sSqlQuery);
        $oStatement->bindValue(':dayNumber', $iDaysOld, PDO::PARAM_INT);
        $oStatement->execute();

        return $oStatement->rowCount();
    }

    private function isInvalidTableForCleaning(string $sTableName): bool
    {
        $bIsCommentTable = strstr($sTableName, CommentCoreModel::TABLE_PREFIX_NAME) !== false;
        $bIsMessageTable = $sTableName === DbTableName::MESSAGE;
        $bIsMessengerTable = $sTableName === DbTableName::MESSENGER;

        return !$bIsCommentTable && !$bIsMessageTable && !$bIsMessengerTable;
    }
}

// Go!
new DatabaseCoreCron;
