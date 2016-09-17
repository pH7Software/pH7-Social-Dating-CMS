<?php
/**
 * @title          Friend Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / User / Model
 * @version        1.0
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class FriendModel extends FriendCoreModel
{

    private $_sStatus;

    /**
     * Check exists in the friends list
     *
     * @param integer $iProfileId = user Id
     * @param integer $iFriendId friend id
     * @param mixed (integer or string) $mPending 'all' = select the friends that are approved and pending, 1 = approved or 0 = pending friend requests. Default value is 'all'
     * @param boolean
     */
    public function inList($iProfileId, $iFriendId, $mPending = 'all')
    {
        $iProfileId = (int) $iProfileId;
        $iFriendId = (int) $iFriendId;

        $sSqlPending = ($mPending !== 'all') ? 'AND pending = :pending' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('MembersFriends') .
          'WHERE profileId = :profileId AND friendId = :friendId ' . $sSqlPending . ' LIMIT 1');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        if ($mPending !== 'all') $rStmt->bindValue(':pending', $mPending, \PDO::PARAM_INT);
        $rStmt->execute();
        return ($rStmt->fetchColumn() > 0) ? true : false;
    }

    /**
     * Add a friend
     *
     * @param integer $iProfileId = user Id
     * @param integer $iFriendId friend id
     * @param string $sRequestDate Date of the Request Friend.
     * @param integer $iPending  1 = approved or 0 = pending friend requests. Default value is 1
     * @return string Status in word: 'error', 'id_does_not_exist', 'friend_exists' or 'success'
     */
    public function add($iProfileId, $iFriendId, $sRequestDate, $iPending = 1)
    {
        $iProfileId = (int) $iProfileId;
        $iFriendId = (int) $iFriendId;

        // Check if the two existing ID
        $oExistsModel = new ExistsCoreModel;

        if ($oExistsModel->id($iProfileId, 'Members') && $oExistsModel->id($iFriendId, 'Members'))
        {
            if (!$this->inList($iProfileId, $iFriendId))
            {
                $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersFriends') .
                  '(profileId, friendId, pending, requestDate) VALUES (:profileId, :friendId, :pending, :requestDate)');

                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
                $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
                $rStmt->bindValue(':pending', $iPending, \PDO::PARAM_INT);
                $rStmt->bindValue(':requestDate', $sRequestDate, \PDO::PARAM_STR);
                $oRow = $rStmt->execute();
                Db::free($rStmt);
                if (!$oRow)
                    $this->_sStatus = 'error';
                else
                    $this->_sStatus = 'success';

            }
            else
            {
                $this->_sStatus = 'friend_exists';
            }
        }
        else
        {
            $this->_sStatus = 'id_does_not_exist';
        }

        unset($oExistsModel); // Destruction of the object

        return $this->_sStatus;
    }

    /**
     * Approve friends
     *
     * @param integer $iProfileId
     * @param integer $iFriendId
     * @return boolean
     */
    public function approval($iProfileId, $iFriendId)
    {
        $iProfileId = (int) $iProfileId;
        $iFriendId = (int) $iFriendId;

        $rStmt = Db::getInstance()->prepare('UPDATE'.Db::prefix('MembersFriends') .
            'SET pending = 0 WHERE profileId = :friendId AND friendId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Delete a friend
     *
     * @param integer $iProfileId
     * @param integer $iFriendId
     * @return boolean
     */
    public function delete($iProfileId, $iFriendId)
    {
        $iProfileId = (int) $iProfileId;
        $iFriendId = (int) $iFriendId;

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('MembersFriends') .
            'WHERE (profileId = :profileId AND friendId = :friendId) OR (friendId = :profileId AND profileId = :friendId)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * "Get" and "Find" "Friends" or "Mutual Friends"
     *
     * @param integer $iIdProfileId User ID
     * @param integer $iFriendId Enter a user Friend ID to find a mutual friend in the friends list or null = the whole list. Default is NULL
     * @param mixed (integer for profile ID or string for a keyword) $mLooking
     * @param boolean $bCount Put 'true' for count friends or 'false' for the result of friends
     * @param string $sOrderBy
     * @param string $sSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @param mixed (integer or string) $mPending 'all' = approved and pending, 1 = approved or 0 = pending friend requests. Default value is 'all'
     * @return mixed (integer for the number friends returned or string for the friends list returned)
     */
    public function get($iIdProfileId, $iFriendId = null, $mLooking, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit, $mPending = 'all')
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlLimit = (!$bCount) ?  'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ?  '(f.profileId + f.friendId - :profileId) AS fdId, f.*, m.username, m.firstName, m.sex' : 'COUNT(f.friendId) AS totalFriends';

        $sSqlWhere = (!empty($iFriendId))
        ? 'f.profileId IN
           (SELECT * FROM (SELECT (m.profileId)
           FROM ' . Db::prefix('MembersFriends') . ' AS m
           WHERE (m.friendId IN(:profileId, :friendId))
           UNION ALL
               SELECT (f.friendId) FROM ' . Db::prefix('MembersFriends') . ' AS f
               WHERE (f.profileId IN(:profileId, :friendId))) AS fd
               GROUP BY fd.profileId HAVING COUNT(fd.profileId) > 1)'
        : '(f.profileId = :profileId OR f.friendId = :profileId)';

        $sSqlSearchWhere = (ctype_digit($mLooking)) ? '(m.profileId = :profileId AND f.friendId= :profileId) OR (m.profileId = :friendId OR f.friendId= :friendId)' : '(m.username LIKE :looking OR m.firstName LIKE :looking OR m.lastName LIKE :looking OR m.email LIKE :looking)';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('MembersFriends') . 'AS f INNER JOIN' . Db::prefix('Members') .
            'AS m ON m.profileId = (f.profileId + f.friendId - :profileId) WHERE ' . $sSqlWhere . ' AND ' . $sSqlSearchWhere .
            $sSqlOrder . $sSqlLimit
        );

        $rStmt->bindValue(':profileId', $iIdProfileId, \PDO::PARAM_INT);
        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!empty($iFriendId)) $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        if ($mPending !== 'all') $rStmt->bindValue(':pending', $mPending, \PDO::PARAM_INT);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int) $oRow->totalFriends;
            unset($oRow);
        }

        return $mData;
    }

}
