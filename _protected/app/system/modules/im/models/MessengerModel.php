<?php
/**
 * @title          Messenger Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / IM / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class MessengerModel extends Model
{
    /**
     * Select Data of content messenger.
     *
     * @param string $sTo Username
     *
     * @return \stdClass SQL content
     */
    public function select($sTo)
    {
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE (toUser = :to AND recd = 0) ORDER BY messengerId ASC');
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();
        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update Message.
     *
     * @param string $sFrom The 'from' username
     * @param string $sTo The 'to' username
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function update($sFrom, $sTo)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Messenger') .
            'SET recd = 1 WHERE (fromUser = :from OR toUser = :to) AND recd = 0');
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * Add a new message.
     *
     * @param string $sFrom Username
     * @param string $sTo Username 2
     * @param string $sMessage Message content
     * @param string $sDate In date format: 0000-00-00 00:00:00
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function insert($sFrom, $sTo, $sMessage, $sDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Messenger') .
            '(fromUser, toUser, message, sent) VALUES (:from, :to, :message, :date)');
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':date', $sDate, PDO::PARAM_STR);
        return $rStmt->execute();
    }
}
