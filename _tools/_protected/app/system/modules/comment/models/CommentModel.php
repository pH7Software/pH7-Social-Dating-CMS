<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Spam;

class CommentModel extends CommentCoreModel
{
    /**
     * @param int $iCommentId
     * @param string $sApproved
     * @param string $sTable
     *
     * @return array
     */
    public function get($iCommentId, $sApproved, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'get' . $iCommentId . $sApproved . $sTable, static::CACHE_TIME);

        if (!$oComment = $this->cache->get()) {
            $sTable = CommentCore::checkTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT c.*, m.username, m.firstName, m.sex FROM' .
                Db::prefix(self::TABLE_PREFIX_NAME . $sTable) . ' AS c LEFT JOIN' . Db::prefix(DbTableName::MEMBER) .
                'AS m ON c.sender = m.profileId WHERE commentId = :commentId AND c.approved = :approved LIMIT 1');
            $rStmt->bindParam(':commentId', $iCommentId, PDO::PARAM_INT);
            $rStmt->bindParam(':approved', $sApproved, PDO::PARAM_STR);
            $rStmt->execute();
            $oComment = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oComment);
        }

        return $oComment;
    }

    /**
     * @param int $iCommentId
     * @param int $iRecipientId
     * @param int $iSenderId
     * @param string $sApproved
     * @param string $sCreatedDate
     * @param string $sTable
     *
     * @return bool
     */
    public function add($iCommentId, $iRecipientId, $iSenderId, $sApproved, $sCreatedDate, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(self::TABLE_PREFIX_NAME . $sTable) .
            '(comment, recipient, sender, approved, createdDate) VALUES(:comment, :recipient, :sender, :approved, :createdDate)');

        $rStmt->bindValue(':comment', $iCommentId, PDO::PARAM_STR);
        $rStmt->bindValue('recipient', $iRecipientId, PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iCommentId
     * @param int $iRecipientId
     * @param int $iSenderId
     * @param string $sComment
     * @param string $sApproved
     * @param string $sUpdatedDate
     * @param string $sTable
     *
     * @return bool
     */
    public function update($iCommentId, $iRecipientId, $iSenderId, $sComment, $sApproved, $sUpdatedDate, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(self::TABLE_PREFIX_NAME . $sTable) .
            'SET comment = :comment, approved = :approved, updatedDate = :updatedDate WHERE commentId = :commentId AND recipient = :recipient AND sender = :sender LIMIT 1');

        $rStmt->bindValue('commentId', $iCommentId, PDO::PARAM_INT);
        $rStmt->bindValue('recipient', $iRecipientId, PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, PDO::PARAM_INT);
        $rStmt->bindValue(':comment', $sComment, PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iCommentId
     * @param int $iRecipientId
     * @param int $iSenderId
     * @param string $sTable
     *
     * @return bool
     */
    public function delete($iCommentId, $iRecipientId, $iSenderId, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(self::TABLE_PREFIX_NAME . $sTable) .
            'WHERE commentId = :commentId AND recipient = :recipient AND sender = :sender LIMIT 1');

        $rStmt->bindValue(':commentId', $iCommentId, PDO::PARAM_INT);
        $rStmt->bindValue('recipient', $iRecipientId, PDO::PARAM_INT);
        $rStmt->bindValue(':sender', $iSenderId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Check if the recipient ID exists in the table.
     *
     * @param int $iId
     * @param string $sTable
     *
     * @return bool
     */
    public function idExists($iId, $sTable)
    {
        $this->cache->start(static::CACHE_GROUP, 'idExists' . $iId . $sTable, static::CACHE_TIME);

        if (!$bExists = $this->cache->get()) {
            $iId = (int)$iId;
            $sTable = CommentCore::checkTable($sTable);
            $sRealTable = Comment::getTable($sTable);
            $sProfileIdColumn = lcfirst($sTable) . 'Id';

            $rStmt = Db::getInstance()->prepare(
                'SELECT COUNT(' . $sProfileIdColumn . ') FROM' . Db::prefix($sRealTable) . 'WHERE ' . $sProfileIdColumn . ' = :id LIMIT 1'
            );
            $rStmt->bindValue(':id', $iId, PDO::PARAM_INT);
            $rStmt->execute();
            $bExists = $rStmt->fetchColumn() == 1;
            Db::free($rStmt);
            $this->cache->put($bExists);
        }

        return $bExists;
    }

    /**
     * Check Duplicate Contents.
     *
     * @param int $iSenderId
     * @param string $sCheckMsg
     * @param string $sTable
     *
     * @return bool Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateContent($iSenderId, $sCheckMsg, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        return Spam::detectDuplicate(
            $sCheckMsg,
            'comment',
            'sender',
            $iSenderId,
            self::TABLE_PREFIX_NAME . $sTable
        );
    }

    /**
     * To prevent spam!
     *
     * @param int $iSenderId
     * @param int $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @param string $sTable
     *
     * @return bool Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitSend($iSenderId, $iWaitTime, $sCurrentTime, $sTable)
    {
        $sTable = CommentCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT commentId FROM' . Db::prefix(self::TABLE_PREFIX_NAME . $sTable) .
            'WHERE sender = :sender AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');

        $rStmt->bindValue(':sender', $iSenderId, PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 0;
    }
}
