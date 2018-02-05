<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / Friend / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class FriendModel extends FriendCoreModel
{
    /** @var string */
    private $sStatus;

    /**
     * Check exists in the friends list
     *
     * @param int $iProfileId
     * @param int $iFriendId
     * @param int|string $mPending 'all' = select the friends that are approved and pending, 1 = approved or 0 = pending friend requests.
     *
     * @return bool
     */
    public function inList($iProfileId, $iFriendId, $mPending = 'all')
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        $sSqlPending = ($mPending !== 'all') ? 'AND pending = :pending' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) .
            'WHERE profileId = :profileId AND friendId = :friendId ' . $sSqlPending . ' LIMIT 1');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
        if ($mPending !== 'all') {
            $rStmt->bindValue(':pending', $mPending, \PDO::PARAM_INT);
        }
        $rStmt->execute();

        return $rStmt->fetchColumn() > 0;
    }

    /**
     * Add a friend
     *
     * @param int $iProfileId = user Id
     * @param int $iFriendId friend id
     * @param string $sRequestDate Date of the Request Friend.
     * @param int $iPending 1 = approved or 0 = pending friend requests.
     *
     * @return string Status in word: 'error', 'id_does_not_exist', 'friend_exists' or 'success'
     */
    public function add($iProfileId, $iFriendId, $sRequestDate, $iPending = 1)
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        // Check if the two existing ID
        $oExistsModel = new ExistsCoreModel;

        if ($oExistsModel->id($iProfileId, DbTableName::MEMBER) && $oExistsModel->id($iFriendId, DbTableName::MEMBER)) {
            if (!$this->inList($iProfileId, $iFriendId)) {
                $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_FRIEND) .
                    '(profileId, friendId, pending, requestDate) VALUES (:profileId, :friendId, :pending, :requestDate)');

                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
                $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);
                $rStmt->bindValue(':pending', $iPending, \PDO::PARAM_INT);
                $rStmt->bindValue(':requestDate', $sRequestDate, \PDO::PARAM_STR);
                $oRow = $rStmt->execute();
                Db::free($rStmt);
                if (!$oRow) {
                    $this->sStatus = 'error';
                } else {
                    $this->sStatus = 'success';
                }
            } else {
                $this->sStatus = 'friend_exists';
            }
        } else {
            $this->sStatus = 'id_does_not_exist';
        }

        unset($oExistsModel); // Destruction of the object

        return $this->sStatus;
    }

    /**
     * Approve friends
     *
     * @param int $iProfileId
     * @param int $iFriendId
     *
     * @return bool
     */
    public function approval($iProfileId, $iFriendId)
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::MEMBER_FRIEND) .
            'SET pending = 0 WHERE profileId = :friendId AND friendId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Delete a friend
     *
     * @param int $iProfileId
     * @param int $iFriendId
     *
     * @return bool
     */
    public function delete($iProfileId, $iFriendId)
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) .
            'WHERE (profileId = :profileId AND friendId = :friendId) OR (friendId = :profileId AND profileId = :friendId)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
