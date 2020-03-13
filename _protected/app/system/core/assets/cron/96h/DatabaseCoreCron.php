<?php
/**
 * @title            Database Cron Class
 * @desc             Database Periodic Cron.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
use Teapot\StatusCode;

class DatabaseCoreCron extends Cron
{
    public function __construct()
    {
        parent::__construct();

        // Available options
        if ($this->httpRequest->getExists('option')) {
            switch ($this->httpRequest->get('option')) {
                // Backup
                case 'backup':
                    $this->backup();
                    break;

                // Restart Stat
                case 'stat':
                    $this->stat();
                    break;

                // Repair Tables
                case 'repair':
                    $this->repair();
                    break;

                // Delete Log
                case 'remove_log':
                    $this->removeLog();
                    break;

                default:
                    Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                    exit('Bad Request Error!');
            }
        }

        // Clean data
        $this->cleanData();

        // Remove deleted messages
        $this->removeDeletedMsg();

        // Optimization tables
        $this->optimize();

        echo '<br />' . t('Cron job finished!');
    }

    private function stat()
    {
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::MEMBER) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::MEMBER) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::MEMBER) . 'SET score=0');


        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::GAME) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::GAME) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::GAME) . 'SET score=0');
        //Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::GAME) . 'SET downloads=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::PICTURE) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::PICTURE) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::PICTURE) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_PICTURE) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_PICTURE) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_PICTURE) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::VIDEO) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::VIDEO) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::VIDEO) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::BLOG) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::BLOG) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::BLOG) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::NOTE) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::NOTE) . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::NOTE) . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::FORUM_TOPIC) . 'SET views=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::AD) . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix(DbTableName::AD) . 'SET clicks=0');

        echo t('Restart Statistics... OK!') . '<br />';
    }

    private function backup()
    {
        (new Backup(PH7_PATH_BACKUP_SQL . 'Periodic-database-update.' . (new CDateTime)->get()->date() . '.sql.gz'))->back()->saveArchive();

        echo t('Backup of the Database... Ok!') . '<br />';
    }

    private function optimize()
    {
        Db::optimize();

        echo t('Optimizing tables... OK!') . '<br />';
    }

    private function repair()
    {
        Db::repair();

        echo t('Repair Database... Ok!') . '<br />';
    }

    private function removeDeletedMsg()
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::MESSAGE) . 'WHERE FIND_IN_SET(\'sender\', toDelete) AND FIND_IN_SET(\'recipient\', toDelete)');

        if ($rStmt->execute()) {
            echo nt('Deleted %n% temporary message... OK!', 'Deleted %n% temporary messages... OK!', $rStmt->rowCount()) . '<br />';
        }
    }

    private function removeLog()
    {
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_ATTEMPT_LOGIN));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_ATTEMPT_LOGIN));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_ATTEMPT_LOGIN));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_LOG_LOGIN));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_LOG_LOGIN));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_LOG_LOGIN));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::ADMIN_LOG_SESS));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::MEMBER_LOG_SESS));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::AFFILIATE_LOG_SESS));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix(DbTableName::LOG_ERROR));

        echo t('Deleting Log... OK!') . '<br />';
    }

    /**
     * Pruning the old data (messages, comments and instant messenger contents).
     *
     * @return void
     */
    private function cleanData()
    {
        $iCleanComment = (int)DbConfig::getSetting('cleanComment');
        $iCleanMsg = (int)DbConfig::getSetting('cleanMsg');
        $iCleanMessenger = (int)DbConfig::getSetting('cleanMessenger');

        // If the option is enabled for Comments
        if ($iCleanComment > 0) {
            $aCommentMods = ['blog', 'note', 'picture', 'video', 'game', 'profile'];
            foreach ($aCommentMods as $sSuffixTable) {
                if ($iRow = ($this->pruningDb($iCleanComment, CommentCoreModel::TABLE_PREFIX_NAME . $sSuffixTable, 'updatedDate') > 0)) {
                    echo t('Deleted %0% %1% comment(s) ... OK!', $iRow, $sSuffixTable) . '<br />';
                }
            }
        }

        // If the option is enabled for Messages
        if ($iCleanMsg > 0) {
            if ($iRow = ($this->pruningDb($iCleanMsg, DbTableName::MESSAGE, 'sendDate') > 0)) {
                echo nt('Deleted %n% message... OK!', 'Deleted %n% messages... OK!', $iRow) . '<br />';
            }
        }

        // If the option is enabled for Messenger
        if ($iCleanMessenger > 0) {
            if ($iRow = ($this->pruningDb($iCleanMessenger, DbTableName::MESSENGER, 'sent') > 0)) {
                echo nt('Deleted %n% IM message... OK!', 'Deleted %n% IM messages... OK!', $iRow) . '<br />';
            }
        }
    }

    /**
     * @param int $iOlderThanXDay Delete data older than X days (e.g., 365 for data older than 1 year).
     * @param string $sTable Table name. Choose between 'Comments<TYPE>', 'Messages' and 'Messenger'
     * @param string $sDateColumn The DB column that indicates when the data has been created/updated (e.g., sendDate, updatedDate).
     *
     * @return int Returns the number of rows.
     */
    private function pruningDb($iOlderThanXDay, $sTable, $sDateColumn)
    {
        if ($this->isTableInvalid($sTable)) {
            DbVarious::launchErr($sTable);
        }

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix($sTable) . 'WHERE (' . $sDateColumn . ' < NOW() - INTERVAL :dayNumber DAY)');
        $rStmt->bindValue(':dayNumber', $iOlderThanXDay, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->rowCount();
    }

    /**
     * @param string $sTable
     *
     * @return bool
     */
    private function isTableInvalid($sTable)
    {
        return strstr($sTable, CommentCoreModel::TABLE_PREFIX_NAME) === false &&
            $sTable !== DbTableName::MESSAGE && $sTable !== DbTableName::MESSENGER;
    }
}

// Go!
new DatabaseCoreCron;
