<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class FriendCoreModel extends Framework\Mvc\Model\Engine\Model
{
    /**
     * "Get" and "Find" "Friends" or "Mutual Friends"
     *
     * @param integer $iIdProfileId User ID
     * @param integer $iFriendId Enter a user Friend ID to find a mutual friend in the friends list or null = the whole list. Default is NULL
     * @param integet|string $mLooking Integer for profile ID or string for a keyword
     * @param boolean $bCount Put 'true' for count friends or 'false' for the result of friends
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @param integer|string $mPending 'all' = approved and pending, 1 = approved or 0 = pending friend requests. Default value is 'all'
     *
     * @return integer|\stdClass Integer for the number friends returned or string for the friends list returned)
     */
    public function get($iIdProfileId, $iFriendId = null, $mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit, $mPending = 'all')
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        if (!$bCount) {
            $sSqlSelect = '(f.profileId + f.friendId - :profileId) AS fdId, f.*, m.username, m.firstName, m.sex';
        } else {
            $sSqlSelect = 'COUNT(f.friendId) AS totalFriends';
        }

        if (!empty($iFriendId)) {
            $sSqlWhere = 'f.profileId IN
           (SELECT * FROM (SELECT (m.profileId)
           FROM ' . Db::prefix('MembersFriends') . ' AS m
           WHERE (m.friendId IN(:profileId, :friendId))
           UNION ALL
               SELECT (f.friendId) FROM ' . Db::prefix('MembersFriends') . ' AS f
               WHERE (f.profileId IN(:profileId, :friendId))) AS fd
               GROUP BY fd.profileId HAVING COUNT(fd.profileId) > 1)';
        } else {
            $sSqlWhere = '(f.profileId = :profileId OR f.friendId = :profileId)';
        }

        if (ctype_digit($mLooking)) {
            $sSqlSearchWhere = '(m.profileId = :profileId AND f.friendId= :profileId) OR (m.profileId = :friendId OR f.friendId= :friendId)';
        } else {
            $sSqlSearchWhere = '(m.username LIKE :looking OR m.firstName LIKE :looking OR m.lastName LIKE :looking OR m.email LIKE :looking)';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('MembersFriends') . 'AS f INNER JOIN' . Db::prefix('Members') .
            'AS m ON m.profileId = (f.profileId + f.friendId - :profileId) WHERE ' . $sSqlWhere . ' AND ' . $sSqlSearchWhere .
            $sSqlOrder . $sSqlLimit
        );

        $rStmt->bindValue(':profileId', $iIdProfileId, \PDO::PARAM_INT);
        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!empty($iFriendId)) {
            $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        }

        if ($mPending !== 'all') {
            $rStmt->bindValue(':pending', $mPending, \PDO::PARAM_INT);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int) $oRow->totalFriends;
            unset($oRow);
        }

        return $mData;
    }

    /**
     * Get Pending Friend.
     *
     * @param integer $iFriendId
     *
     * @return integer
     */
    public static function getPending($iFriendId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(pending) AS pendingFds FROM' .
            Db::prefix('MembersFriends') . 'WHERE friendId = :friendId AND pending = \'1\'');

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
     *
     * @return integer
     */
    public static function total($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(friendId) AS totalFds FROM' . Db::prefix('MembersFriends') .
            'WHERE (profileId = :profileId OR friendId= :profileId)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return (int) $oRow->totalFds;
    }
}
