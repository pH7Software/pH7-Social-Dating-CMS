<?php
/**
 * @title          Statistic Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.1
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class StatisticCoreModel extends Framework\Mvc\Model\Statistic
{

    /**
     * Get the since date of the website.
     *
     * @return string The date.
     */
    public static function getSiteSinceDate()
    {
        $rStmt = Db::getInstance()->prepare('SELECT joinDate AS sinceDate FROM' . Db::prefix('Admins') . 'WHERE profileId = 1');
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        return $oRow->sinceDate;
    }

    /**
     * Get the total number of members.
     *
     * @param integer $iDay Default '0'
     * @param string $sGenger Values ​​available 'all', 'male', 'female', 'couple'. Default 'all'
     * @return integer Total Users
     */
    public function totalMembers($iDay = 0, $sGenger = 'all')
    {
        return  (new UserCoreModel)->total('Members', $iDay, $sGenger);
    }

    /**
     * Get the total number of affiliates.
     *
     * @param integer $iDay Default '0'
     * @param string $sGenger Values ​​available 'all', 'male', 'female'. Default 'all'
     * @return integer Total Users
     */
    public function totalAffiliates($iDay = 0, $sGenger = 'all')
    {
        return  (new UserCoreModel)->total('Affiliates', $iDay, $sGenger);
    }

    /**
     * Total Logins.
     *
     * @param string $sTable Default 'Members'
     * @param integer $iDay Default '0'
     * @param string $sGenger Values ​​available 'all', 'male', 'female'. 'couple' is only available to Members. Default 'all'
     */
    public function totalLogins($sTable = 'Members', $iDay = 0, $sGenger = 'all')
    {
        Framework\Mvc\Model\Engine\Util\Various::checkModelTable($sTable);
        $iDay = (int) $iDay;

        $bIsDay = ($iDay > 0);
        $bIsGenger = ($sTable === 'Members' ? ($sGenger === 'male' || $sGenger === 'female' || $sGenger === 'couple') : ($sGenger === 'male' || $sGenger === 'female'));

        $sSqlDay = $bIsDay ? ' AND (lastActivity + INTERVAL :day DAY) > NOW()' : '';
        $sSqlGender = $bIsGenger ? ' AND sex = :gender' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) AS totalLogins FROM' . Db::prefix($sTable) . 'WHERE username <> \''.PH7_GHOST_USERNAME.'\'' . $sSqlDay . $sSqlGender);
        if($bIsDay) $rStmt->bindValue(':day', $iDay, \PDO::PARAM_INT);
        if($bIsGenger) $rStmt->bindValue(':gender', $sGenger, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        return (int) $oRow->totalLogins;
    }

    /**
     * Get the total number of admins.
     *
     * @param integer $iDay Default '0'
     * @param string $sGenger Values ​​available 'all', 'male', 'female'. Default 'all'
     * @return integer Total Users
     */
    public function totalAdmins($iDay = 0, $sGenger = 'all')
    {
        return  (new UserCoreModel)->total('Admins', $iDay, $sGenger);
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
        $iDay = (int) $iDay;
        $sSqlDay = ($iDay > 0) ? ' WHERE (sendDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(messageId) AS totalMails FROM' . Db::prefix('Messages') . $sSqlDay);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int) $oRow->totalMails;
    }

    public function totalProfileComments($iDay = 0)
    {
        return $this->totalComments('Profile', $iDay);
    }

    public function totalPictureComments($iDay = 0)
    {
        return $this->totalComments('Picture', $iDay);
    }

    public function totalVideoComments($iDay = 0)
    {
        return $this->totalComments('Video', $iDay);
    }

    public function totalBlogComments($iDay = 0)
    {
        return $this->totalComments('Blog', $iDay);
    }

    public function totalNoteComments($iDay = 0)
    {
        return $this->totalComments('Note', $iDay);
    }

    public function totalGameComments($iDay = 0)
    {
        return $this->totalComments('Game', $iDay);
    }

    protected function totalComments($sTable, $iDay = 0)
    {
        CommentCore::checkTable($sTable);
        $iDay = (int) $iDay;

        $sSqlDay = ($iDay > 0) ? ' WHERE (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(commentId) AS totalComments FROM' . Db::prefix('Comments' . $sTable) . $sSqlDay);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        return (int) $oRow->totalComments;
    }

}
