<?php
/**
 * @title            Database Cron Class
 * @desc             Database Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 * @version          1.1
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Model\Engine\Util\Backup;

class DatabaseCoreCron extends Cron
{

    public function __construct()
    {
        parent::__construct();

        // Available options
        if ($this->httpRequest->getExists('option'))
        {
            switch ($this->httpRequest->get('option'))
            {
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
                    Framework\Http\Http::setHeadersByCode(400);
                    exit('Bad Request Error!');
            }
        }

        // Clean data
        $this->cleanData();

        // Remove temporary messages
        $this->removeTmpMsg();

        // Delete temporary Data
        $this->removeTmpData();

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
        (new Backup(PH7_PATH_BACKUP_SQL . 'Periodic-database-update.' . (new Framework\Date\CDateTime)->get()->date() . '.sql.gz'))->back()->saveArchive();

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

        echo t('Repair of the Database... Ok!') . '<br />';
    }

    protected function removeTmpData()
    {
        if (Db::getInstance()->exec('TRUNCATE TABLE ' . Db::prefix('Messenger')))
            echo t('Deleting Temporary Data... OK!') . '<br />';
    }

    protected function removeTmpMsg()
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

    protected function cleanData()
    {
        $iCleanMsg = (int) DbConfig::getSetting('cleanMsg');
        $iCleanComment = (int) DbConfig::getSetting('cleanComment');

        // If the option is enabled
        if ($iCleanMsg > 0)
        {
            $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Messages') . 'WHERE (sendDate < NOW() - INTERVAL :cleanMsg DAY)');
            $rStmt->bindValue(':cleanMsg', $iCleanMsg, \PDO::PARAM_INT);

            if ($rStmt->execute())
                echo nt('Deleted %n% message... OK!', 'Deleted %n% messages... OK!', $rStmt->rowCount()) . '<br />';
        }

        // If the option is enabled
        if ($iCleanComment > 0)
        {
            $aCommentMod = ['Blog', 'Note', 'Picture', 'Video', 'Game', 'Profile'];
            foreach ($aCommentMod as $sSuffixTable)
            {
                $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Comments' . $sSuffixTable) . 'WHERE (updatedDate < NOW() - INTERVAL :cleanComment DAY)');
                $rStmt->bindValue(':cleanComment', $iCleanComment, \PDO::PARAM_INT);
                if ($rStmt->execute())
                    echo t('Deleted %0% %1% comment(s) ... OK!', $rStmt->rowCount(), $sSuffixTable) . '<br />';
            }
        }
    }

}

// Go!
new DatabaseCoreCron;
