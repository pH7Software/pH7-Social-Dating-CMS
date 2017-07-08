<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class CommentModel extends CommentCoreModel
{
    public function get($iCommentId, $iApproved, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'get' . $iCommentId . $iApproved . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $sTable = CommentCore::checkTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Comments' . $sTable) . ' AS c LEFT JOIN' . Db::prefix('Members') . 'AS m ON c.sender = m.profileId WHERE commentId = :commentId AND c.approved =:approved LIMIT 1');
            $rStmt->bindParam(':commentId', $iCommentId, \PDO::PARAM_INT);
            $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    public function add($iCommentId, $iRecipientId, $iSenderId, $iApproved, $sCreatedDate, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Comments' . $sTable) . '(comment, recipient, sender, approved, createdDate) VALUES(:comment, :recipient, :sender, :approved, :createdDate)');
        $rStmt->bindValue(':comment', $iCommentId, \PDO::PARAM_STR);
        $rStmt->bindValue('recipient', $iRecipientId, \PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, \PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function update($iCommentId, $iRecipientId, $iSenderId, $sComment, $iApproved, $sUpdatedDate, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Comments' . $sTable) . 'SET comment = :comment, approved = :approved, updatedDate = :updatedDate WHERE commentId = :commentId AND recipient = :recipient AND sender = :sender LIMIT 1');
        $rStmt->bindValue('commentId', $iCommentId, \PDO::PARAM_INT);
        $rStmt->bindValue('recipient', $iRecipientId, \PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, \PDO::PARAM_INT);
        $rStmt->bindValue(':comment', $sComment, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function delete($iCommentId, $iRecipientId, $iSenderId, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Comments' . $sTable) . 'WHERE commentId = :commentId AND recipient = :recipient AND sender = :sender LIMIT 1');
        $rStmt->bindValue(':commentId', $iCommentId, \PDO::PARAM_INT);
        $rStmt->bindValue('recipient', $iRecipientId, \PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Check if the recipient ID exists in the table.
     *
     * @param  integer $iId
     * @param  string $sTable
     * @return boolean
     */
    public function idExists($iId, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'idExists' . $iId . $sTable, static::CACHE_TIME);

        if (!$bData = $this->cache->get()) {
            $iId = (int)$iId;
            $sTable = CommentCore::checkTable($sTable);
            $sRealTable = Comment::getTable($sTable);
            $sProfileIdColumn = lcfirst($sTable) . 'Id';

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(' . $sProfileIdColumn . ') FROM' . Db::prefix($sRealTable) . 'WHERE ' . $sProfileIdColumn . ' = :id LIMIT 1');
            $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
            $rStmt->execute();
            $bData = ($rStmt->fetchColumn() == 1);
            Db::free($rStmt);
            $this->cache->put($bData);
        }
        return $bData;
    }

    /**
     * Check Duplicate Contents.
     *
     * @param integer $iSenderId
     * @param string $sCheckMsg
     * @param string $sTable
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateContent($iSenderId, $sCheckMsg, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        return Framework\Mvc\Model\Spam::detectDuplicate($sCheckMsg, 'comment', 'sender', $iSenderId, 'Comments' . $sTable);
    }

    /**
     * To prevent spam!
     *
     * @param integer $iSenderId
     * @param integer $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @param string $sTable
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitSend($iSenderId, $iWaitTime, $sCurrentTime, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT commentId FROM' . Db::prefix('Comments' . $sTable) . 'WHERE sender = :sender AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');
        $rStmt->bindValue(':sender', $iSenderId, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return $rStmt->rowCount() === 0;
    }
}
