<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class MailModel extends MailCoreModel
{

    public function readMessages($iRecipient, $iOffset, $iLimit)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT msg.*, m.profileId,m.username, m.firstName FROM' . Db::prefix('Messages') . 'AS msg LEFT JOIN ' . Db::prefix('Members') . 'AS m ON msg.sender = m.profileId WHERE msg.recipient = :recipient ORDER BY msg.sendDate DESC LIMIT :offset, :limit');
        $rStmt->bindValue(':recipient', $iRecipient, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function readMessage($iRecipient, $iMessageId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT msg.*, m.profileId, m.username, m.firstName FROM' . Db::prefix('Messages') . 'AS msg LEFT JOIN ' . Db::prefix('Members') . 'AS m ON msg.sender = m.profileId WHERE msg.recipient = :recipient AND msg.messageId = :messageId');
        $rStmt->bindValue(':recipient', $iRecipient, \PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Send a message.
     *
     * @param integer $iSender
     * @param integer $iRecipient
     * @param string $sTitle
     * @param string $sMessage
     * @param string $sCreateDate
     * @return mixed (boolean | integer) Returns the ID of the message on success or FALSE on failure.
     */
    public function sendMessage($iSender, $iRecipient, $sTitle, $sMessage, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Messages') . '(sender, recipient, title, message, sendDate, status)
                VALUES (:sender, :recipient, :title, :message, :sendDate, \'1\')');
        $rStmt->bindValue(':sender', $iSender, \PDO::PARAM_INT);
        $rStmt->bindValue(':recipient', $iRecipient, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, \PDO::PARAM_STR);
        $rStmt->bindValue(':sendDate', $sCreatedDate, \PDO::PARAM_STR);
        return (!$rStmt->execute()) ? false : Db::getInstance()->lastInsertId();
    }

    public function deleteMessage($iRecipient, $iMessageId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Messages') . 'WHERE (recipient = :recipient) AND (messageId = :messageId)');
        $rStmt->bindValue(':recipient', $iRecipient, \PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function adminDeleteMessage($iMessageId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Messages') . 'WHERE messageId = :messageId');
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function totalMessages($iRecipient)
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(messageId) AS totalMsgs FROM' . Db::prefix('Messages') . 'WHERE recipient = :recipient');
        $rStmt->bindValue(':recipient', $iRecipient, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow->totalMsgs;
    }

    public function setReadMsg($iMessageId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Messages') . 'SET status=0 WHERE messageId = :messageId');
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    public function search($iRecipient, $mLooking, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlLimit = ($bCount === false) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = ($bCount === false) ? '*' : 'COUNT(messageId) AS totalMails';
        $sSqlWhere = (ctype_digit($mLooking)) ? '(messageId = :looking)' : '(title LIKE :looking OR message LIKE :looking OR username LIKE :looking OR firstName LIKE :looking OR lastName LIKE :looking)';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Messages') . 'AS msg LEFT JOIN ' . Db::prefix('Members') . 'AS m ON msg.sender = m.profileId
                            WHERE (msg.recipient = :recipient) AND ' . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        $rStmt->bindParam(':recipient', $iRecipient, \PDO::PARAM_INT);

        if ($bCount === false) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if ($bCount === false) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            $mData = (int) $oRow->totalMails;
            unset($oRow);
        }

        Db::free($rStmt);
        return $mData;
    }

    public function allMessage($mLooking, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlLimit = ($bCount === false) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = ($bCount === false) ? '*' : 'COUNT(messageId) AS totalMails';
        $sSqlWhere = (ctype_digit($mLooking)) ? '(messageId = :looking)' : '(title LIKE :looking OR message LIKE :looking OR username LIKE :looking OR firstName LIKE :looking OR lastName LIKE :looking)';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Messages') . 'AS msg LEFT JOIN ' . Db::prefix('Members') . 'AS m ON msg.sender = m.profileId WHERE ' . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if ($bCount === false)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if ($bCount === false)
        {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            $mData = (int) $oRow->totalMails;
            unset($oRow);
        }

        Db::free($rStmt);
        return $mData;
    }

    /**
     * Check Duplicate Contents.
     *
     * @param integer $iSenderId
     * @param string $sCheckMsg
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateContent($iSenderId, $sCheckMsg)
    {
        return Framework\Mvc\Model\SpamModel::detectDuplicate($sCheckMsg, 'message', 'sender', $iSenderId, 'Messages');
    }

    /**
     * To prevent spam!
     *
     * @param integer $iSenderId
     * @param integer $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitSend($iSenderId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT messageId FROM' . Db::prefix('Messages') . 'WHERE sender = :sender AND DATE_ADD(sendDate, INTERVAL :waitTime MINUTE) > :currentTime');
        $rStmt->bindValue(':sender', $iSenderId, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 0) ? true : false;
    }

}
