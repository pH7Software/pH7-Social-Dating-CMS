<?php
/**
 * @title            Database Cron Class
 * @desc             Database Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 * @version          1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Backup;
use PH7\Framework\Mvc\Model\Engine\Util\Various as DbVarious;

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
                    Http::setHeadersByCode(400);
                    exit('Bad Request Error!');
            }
        }

        // Clean data
        $this->cleanData();

        // Remove deleted messages
        $this->removeDeletedMsg();

        // Optimization tables
        $this->optimize();

        echo '<br />' . t('Done!') . '<br />';
        echo t('The Jobs Cron is working to complete successfully!');
    }

    protected function stat()
    {
        Db::getInstance()->exec('UPDATE' . Db::prefix('Members') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Members') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Members') . 'SET score=0');


        Db::getInstance()->exec('UPDATE' . Db::prefix('Games') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Games') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Games') . 'SET score=0');
        //Db::getInstance()->exec('UPDATE' . Db::prefix('Games') . 'SET downloads=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('Pictures') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Pictures') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Pictures') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsPictures') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsPictures') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsPictures') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('Videos') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Videos') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Videos') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsVideos') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsVideos') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('AlbumsVideos') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('Blogs') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Blogs') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Blogs') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('Notes') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Notes') . 'SET votes=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Notes') . 'SET score=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('ForumsTopics') . 'SET views=0');

        Db::getInstance()->exec('UPDATE' . Db::prefix('Ads') . 'SET views=0');
        Db::getInstance()->exec('UPDATE' . Db::prefix('Ads') . 'SET clicks=0');

        echo t('Restart Statistics... OK!') . '<br />';
    }

    protected function backup()
    {
        (new Backup(PH7_PATH_BACKUP_SQL . 'Periodic-database-update.' . (new CDateTime)->get()->date() . '.sql.gz'))->back()->saveArchive();

        echo t('Backup of the Database... Ok!') . '<br />';
    }

    protected function optimize()
    {
        Db::optimize();

        echo t('Optimizing tables... OK!') . '<br />';
    }

    protected function repair()
    {
        Db::repair();

        echo t('Repair Database... Ok!') . '<br />';
    }

    protected function removeDeletedMsg()
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Messages') . 'WHERE FIND_IN_SET(\'sender\', toDelete) AND FIND_IN_SET(\'recipient\', toDelete)');

        if ($rStmt->execute())
            echo nt('Deleted %n% temporary message... OK!', 'Deleted %n% temporary messages... OK!', $rStmt->rowCount()) . '<br />';
    }

    protected function removeLog()
    {
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AdminsAttemptsLogin'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('MembersAttemptsLogin'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AffiliatesAttemptsLogin'));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AdminsLogLogin'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('MembersLogLogin'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AffiliatesLogLogin'));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AdminsLogSess'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('MembersLogSess'));
        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('AffiliatesLogSess'));

        Db::getInstance()->exec('TRUNCATE TABLE' . Db::prefix('LogError'));

        echo t('Deleting Log... OK!') . '<br />';
    }

    /**
     * Pruning the old data (messages, comments and instant messenger contents).
     *
     * @return void
     */
    protected function cleanData()
    {
        $iCleanComment = (int)DbConfig::getSetting('cleanComment');
        $iCleanMsg = (int)DbConfig::getSetting('cleanMsg');
        $iCleanMessenger = (int)DbConfig::getSetting('cleanMessenger');

        // If the option is enabled for Comments
        if ($iCleanComment > 0) {
            $aCommentMod = ['Blog', 'Note', 'Picture', 'Video', 'Game', 'Profile'];
            foreach ($aCommentMod as $sSuffixTable) {
                if ($iRow = $this->pruningDb($iCleanComment, 'Comments' . $sSuffixTable, 'updatedDate') > 0) {
                    echo t('Deleted %0% %1% comment(s) ... OK!', $iRow, $sSuffixTable) . '<br />';
                }
            }
        }

        // If the option is enabled for Messages
        if ($iCleanMsg > 0) {
            if ($iRow = $this->pruningDb($iCleanMsg, 'Messages', 'sendDate') > 0) {
                echo nt('Deleted %n% message... OK!', 'Deleted %n% messages... OK!', $iRow) . '<br />';
            }
        }

        // If the option is enabled for Messenger
        if ($iCleanMessenger > 0) {
            if ($iRow = $this->pruningDb($iCleanMessenger, 'Messenger', 'sent') > 0) {
                echo nt('Deleted %n% IM message... OK!', 'Deleted %n% IM messages... OK!', $iRow) . '<br />';
            }
        }
    }

    /**
     * @param integer $iOlderThanXDay Delete data older than X days (e.g., 365 for data older than 1 year).
     * @param string $sTable Table name. Choose between 'Comments<TYPE>', 'Messages' and 'Messenger'
     * @param string $sDateColumn The DB column that indicates when the data has been created/updated (e.g., sendDate, updatedDate).
     *
     * @return integer Returns the number of rows.
     */
    protected function pruningDb($iOlderThanXDay, $sTable, $sDateColumn)
    {
        if (strstr($sTable, 'Comments') === false && $sTable !== 'Messages' && $sTable !== 'Messenger') {
           DbVarious::launchErr($sTable);
        }

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix($sTable) . 'WHERE (' . $sDateColumn . ' < NOW() - INTERVAL :dayNumber DAY)');
        $rStmt->bindValue(':dayNumber', $iOlderThanXDay, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->rowCount();
    }
}

// Go!
new DatabaseCoreCron;
