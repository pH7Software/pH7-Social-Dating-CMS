<?php
/**
 * @title          Statistic Core Model Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.1
 */

namespace PH7;

use PDO;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Record;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Model\Statistic as StatisticModel;

class StatisticCoreModel extends StatisticModel
{
    const CACHE_GROUP = 'db/sys/core/statistic';
    const CACHE_LIFETIME = 10368000;

    /**
     * Get the date since the website has been created.
     *
     * @return string The creation date.
     */
    public static function getDateOfCreation()
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'dateofcreation', self::CACHE_LIFETIME);

        if (!$sSinceDate = $oCache->get()) {
            $sSinceDate = Record::getInstance()->getOne(DbTableName::ADMIN, 'profileId', AdminCore::ROOT_PROFILE_ID, 'joinDate')->joinDate;
            $oCache->put($sSinceDate);
        }
        unset($oCache);

        return $sSinceDate;
    }

    /**
     * Get the total number of members.
     *
     * @param int $iDay Default '0'
     * @param string $sGender Values ​​available 'all', 'male', 'female', 'couple'. Default 'all'
     *
     * @return int Total Users
     */
    public function totalMembers($iDay = 0, $sGender = 'all')
    {
        return (new UserCoreModel)->total(DbTableName::MEMBER, $iDay, $sGender);
    }

    /**
     * Get the total number of affiliates.
     *
     * @param int $iDay Default '0'
     * @param string $sGender Values ​​available 'all', 'male', 'female'. Default 'all'
     *
     * @return int Total Users
     */
    public function totalAffiliates($iDay = 0, $sGender = 'all')
    {
        return (new UserCoreModel)->total(DbTableName::AFFILIATE, $iDay, $sGender);
    }

    /**
     * Total Logins.
     *
     * @param string $sTable Default DbTableName::MEMBER
     * @param int $iDay Default '0'
     * @param string $sGender Values ​​available 'all', 'male', 'female'. 'couple' is only available to Members. Default 'all'
     *
     * @return int
     */
    public function totalLogins($sTable = DbTableName::MEMBER, $iDay = 0, $sGender = 'all')
    {
        Various::checkModelTable($sTable);

        $iDay = (int)$iDay;

        $bIsDay = ($iDay > 0);
        $bIsGender = $sTable === DbTableName::MEMBER ? GenderTypeUserCore::isGenderValid($sGender) : GenderTypeUserCore::isGenderValid($sGender, GenderTypeUserCore::IGNORE_COUPLE_GENDER);

        $sSqlDay = $bIsDay ? ' AND (lastActivity + INTERVAL :day DAY) > NOW()' : '';
        $sSqlGender = $bIsGender ? ' AND sex = :gender' : '';

        $sSqlQuery = 'SELECT COUNT(profileId) AS totalLogins FROM' . Db::prefix($sTable) . 'WHERE username <> \'' . PH7_GHOST_USERNAME . '\'' . $sSqlDay . $sSqlGender;
        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        if ($bIsDay) {
            $rStmt->bindValue(':day', $iDay, PDO::PARAM_INT);
        }
        if ($bIsGender) {
            $rStmt->bindValue(':gender', $sGender, PDO::PARAM_STR);
        }
        $rStmt->execute();
        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);

        return (int)$oRow->totalLogins;
    }

    /**
     * Get the total number of admins.
     *
     * @param int $iDay Default '0'
     * @param string $sGender Values ​​available 'all', 'male', 'female'. Default 'all'
     *
     * @return int Total Users
     */
    public function totalAdmins($iDay = 0, $sGender = 'all')
    {
        return (new UserCoreModel)->total(DbTableName::ADMIN, $iDay, $sGender);
    }

    public function totalBlogs($iDay = 0)
    {
        return (new BlogCoreModel)->totalPosts($iDay);
    }

    public function totalNotes($iDay = 0)
    {
        return (new NoteCoreModel)->totalPosts(1, $iDay);
    }

    public function totalMails($iDay = 0)
    {
        $iDay = (int)$iDay;
        $sSqlDay = ($iDay > 0) ? ' WHERE (sendDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(messageId) AS totalMails FROM' . Db::prefix(DbTableName::MESSAGE) . $sSqlDay);
        $rStmt->execute();
        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->totalMails;
    }

    public function totalProfileComments($iDay = 0)
    {
        return $this->totalComments('profile', $iDay);
    }

    public function totalPictureComments($iDay = 0)
    {
        return $this->totalComments('picture', $iDay);
    }

    public function totalVideoComments($iDay = 0)
    {
        return $this->totalComments('video', $iDay);
    }

    public function totalBlogComments($iDay = 0)
    {
        return $this->totalComments('blog', $iDay);
    }

    public function totalNoteComments($iDay = 0)
    {
        return $this->totalComments('note', $iDay);
    }

    public function totalGameComments($iDay = 0)
    {
        return $this->totalComments('game', $iDay);
    }

    /**
     * @param string $sTable
     * @param int $iDay
     *
     * @return int
     */
    protected function totalComments($sTable, $iDay = 0)
    {
        CommentCore::checkTable($sTable);
        $iDay = (int)$iDay;

        $sSqlDay = ($iDay > 0) ? ' WHERE (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(commentId) AS totalComments FROM' . Db::prefix(CommentCoreModel::TABLE_PREFIX_NAME . $sTable) . $sSqlDay);
        $rStmt->execute();
        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);

        return (int)$oRow->totalComments;
    }
}
