<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class FriendCoreModel extends Framework\Mvc\Model\Engine\Model
{

    /**
     * Get Pending Friend.
     *
     * @param integer $iFriendId
     * @return integer
     */
    public static function getPending($iFriendId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(pending) AS pendingFds FROM' . Db::prefix('MembersFriends') . 'WHERE friendId = :friendId AND pending = \'1\'');
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return (int) $oRow->pendingFds;
    }

    /**
     * Count total friends.
     *
     * @param integer $iProfileId
     * @return integer
     */
    public static function total($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(friendId) AS totalFds FROM' . Db::prefix('MembersFriends') . 'WHERE (profileId = :profileId OR friendId= :profileId)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return (int) $oRow->totalFds;
    }

}
