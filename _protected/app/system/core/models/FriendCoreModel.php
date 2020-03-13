<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class FriendCoreModel extends Model
{
    const ALL_REQUEST = 2;
    const APPROVED_REQUEST = 0;
    const PENDING_REQUEST = 1;

    /**
     * "Get" and "Find" "Friends" or "Mutual Friends"
     *
     * @param int $iIdProfileId User ID
     * @param int $iFriendId Enter a user Friend ID to find a mutual friend in the friends list or null = the whole list. Default is NULL
     * @param int|string $mLooking Integer for profile ID or string for a keyword
     * @param bool $bCount Put 'true' for count friends or 'false' for the result of friends
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array Integer for the number friends returned or an array containing a stdClass object with the friends list)
     */
    public function get($iIdProfileId, $iFriendId = null, $mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlSelect = !$bCount ? '(f.profileId + f.friendId - :profileId) AS fdId, f.*, m.username, m.firstName, m.sex' : 'COUNT(f.friendId)';
        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';

        $sSqlWhere = '(f.profileId = :profileId OR f.friendId = :profileId)';
        if (!empty($iFriendId)) {
            $sSqlWhere = 'f.profileId IN
           (SELECT * FROM (SELECT (m.profileId)
           FROM ' . Db::prefix(DbTableName::MEMBER_FRIEND) . ' AS m
           WHERE (m.friendId IN (:profileId, :friendId))
           UNION ALL
               SELECT (f.friendId) FROM ' . Db::prefix(DbTableName::MEMBER_FRIEND) . ' AS f
               WHERE (f.profileId IN (:profileId, :friendId))) AS fd
               GROUP BY fd.profileId HAVING COUNT(fd.profileId) > 1)';
        }

        $sSqlSearchWhere = '(m.username LIKE :looking OR m.firstName LIKE :looking OR m.lastName LIKE :looking OR m.email LIKE :looking)';
        if ($bDigitSearch) {
            $sSqlSearchWhere = '(m.profileId = :profileId AND f.friendId= :profileId) OR (m.profileId = :friendId OR f.friendId= :friendId)';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) . 'AS f INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON m.profileId = (f.profileId + f.friendId - :profileId) WHERE m.ban = 0 AND ' . $sSqlWhere . ' AND ' . $sSqlSearchWhere .
            $sSqlOrder . $sSqlLimit
        );

        $rStmt->bindValue(':profileId', $iIdProfileId, PDO::PARAM_INT);

        if ($bDigitSearch) {
            $rStmt->bindValue(':looking', $mLooking, PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', PDO::PARAM_STR);
        }

        if (!empty($iFriendId)) {
            $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }
        Db::free($rStmt);

        return $mData;
    }

    /**
     * Check if a user exists in another user's friends list.
     *
     * @param int $iProfileId
     * @param int $iFriendId
     * @param int $iPending 2 = select the friends that are approved and pending, 0 = approved or 1 = pending friend requests.
     *
     * @return bool
     */
    public function inList($iProfileId, $iFriendId, $iPending = self::ALL_REQUEST)
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        $sSqlPending = ($iPending !== self::ALL_REQUEST) ? 'AND pending = :pending' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) .
            'WHERE (profileId = :profileId AND friendId = :friendId) OR (profileId = :friendId AND friendId = :profileId) ' . $sSqlPending . ' LIMIT 1');

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);
        if ($iPending !== self::ALL_REQUEST) {
            $rStmt->bindValue(':pending', $iPending, PDO::PARAM_INT);
        }
        $rStmt->execute();

        return $rStmt->fetchColumn() > 0;
    }

    /**
     * Get Pending Friend.
     *
     * @param int $iFriendId
     *
     * @return int
     */
    public static function getPending($iFriendId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(pending) FROM' .
            Db::prefix(DbTableName::MEMBER_FRIEND) . 'WHERE friendId = :friendId AND pending = :pending');

        $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);
        $rStmt->bindValue(':pending', self::PENDING_REQUEST, PDO::PARAM_INT);
        $rStmt->execute();
        $iPendingFriends = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iPendingFriends;
    }

    /**
     * Count total friends.
     *
     * @param int $iProfileId
     *
     * @return int
     */
    public static function total($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(friendId) FROM' .
            Db::prefix(DbTableName::MEMBER_FRIEND) .
            'WHERE (profileId = :profileId OR friendId = :profileId)');

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->execute();
        $iTotalFriends = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalFriends;
    }
}
