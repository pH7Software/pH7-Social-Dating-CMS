<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / Friend / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;

class FriendModel extends FriendCoreModel
{
    const SUCCESS_STATUS = 0;
    const EXISTS_STATUS = 1;
    const UNEXISTENT_ID_STATUS = 2;
    const ERROR_STATUS = -1;

    /** @var string */
    private $sStatus;

    /**
     * Add a friend.
     *
     * @param int $iProfileId = user Id
     * @param int $iFriendId friend id
     * @param string $sRequestDate Date of the Request Friend.
     * @param int $iPending 0 = approved or 1 = pending friend requests.
     *
     * @return int
     */
    public function add($iProfileId, $iFriendId, $sRequestDate, $iPending = self::PENDING_REQUEST)
    {
        $iProfileId = (int)$iProfileId;
        $iFriendId = (int)$iFriendId;

        // Check if the two existing ID
        $oExistsModel = new ExistsCoreModel;

        if ($oExistsModel->id($iProfileId, DbTableName::MEMBER) && $oExistsModel->id($iFriendId, DbTableName::MEMBER)) {
            if ($this->inList($iProfileId, $iFriendId) === false) {
                $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_FRIEND) .
                    '(profileId, friendId, pending, requestDate) VALUES (:profileId, :friendId, :pending, :requestDate)');

                $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
                $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);
                $rStmt->bindValue(':pending', $iPending, PDO::PARAM_INT);
                $rStmt->bindValue(':requestDate', $sRequestDate, PDO::PARAM_STR);
                $oRow = $rStmt->execute();
                Db::free($rStmt);
                if (!$oRow) {
                    $this->sStatus = self::ERROR_STATUS;
                } else {
                    $this->sStatus = self::SUCCESS_STATUS;
                }
            } else {
                $this->sStatus = self::EXISTS_STATUS;
            }
        } else {
            $this->sStatus = self::UNEXISTENT_ID_STATUS;
        }

        unset($oExistsModel); // Destruction of the object

        return $this->sStatus;
    }

    /**
     * Approve friends.
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
            'SET pending = :approved WHERE profileId = :friendId AND friendId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);
        $rStmt->bindValue(':approved', self::APPROVED_REQUEST, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Delete a friend :-(
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
            'WHERE (profileId = :profileId AND friendId = :friendId) OR (profileId = :friendId AND friendId = :profileId)');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':friendId', $iFriendId, PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
