<?php
/**
 * @title          Comment Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.0
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class CommentCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const
    CACHE_GROUP = 'db/sys/mod/comment',
    CACHE_TIME = 345600,
    CREATED = 'createdDate',
    UPDATED = 'updatedDate';

    public function gets($sTable, $iApproved = 1, $sOrder = self::UPDATED, $iOffset = 0, $iLimit = 500)
    {
        $sTable = CommentCore::checkTable($sTable);
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Comments' . $sTable) . ' AS c LEFT JOIN' . Db::prefix('Members') . 'AS m ON c.sender = m.profileId WHERE c.approved = :approved ORDER BY ' . $sOrder . ' DESC LIMIT :offset, :limit');

        $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oData;
    }

    public function read($iRecipientId, $iApproved, $iOffset, $iLimit, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlRecipientId = (!empty($iRecipientId)) ? 'c.recipient =:recipient AND' : '';

        $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Comments' . $sTable) . ' AS c LEFT JOIN' . Db::prefix('Members') . 'AS m ON c.sender = m.profileId WHERE ' . $sSqlRecipientId . ' c.approved =:approved ORDER BY c.createdDate DESC LIMIT :offset, :limit');

        if (!empty($iRecipientId)) $rStmt->bindParam(':recipient', $iRecipientId, \PDO::PARAM_INT);
        $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oData;
    }

    public function total($iRecipientId, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'total' . $iRecipientId . $sTable, static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $sTable = CommentCore::checkTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(commentId) AS totalComments FROM' . Db::prefix('Comments' . $sTable) . ' WHERE recipient = :recipient');
            $rStmt->bindParam(':recipient', $iRecipientId);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) $oRow->totalComments;
            unset($oRow);
            $this->cache->put($iData);
        }
        return $iData;
    }

    /**
     * Delete a comment.
     *
     * @param integer $iRecipientId The Comment Recipient ID.
     * @param string $sTable The Comment Table.
     * @return boolean Returns TRUE on success, FALSE on failure.
     */
    public static function deleteRecipient($iRecipientId, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $iRecipientId = (int) $iRecipientId;
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Comments' . $sTable) . 'WHERE recipient = :recipient');
        $rStmt->bindValue(':recipient', $iRecipientId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

}
