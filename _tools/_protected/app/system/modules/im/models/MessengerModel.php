<?php
/**
 * @title          Messenger Model
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
     * @return array SQL content
     */
    public function select($sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix(DbTableName::MESSENGER) .
            'WHERE (toUser = :to AND recd = 0) ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
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
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function update($sFrom, $sTo)
    {
        $sSqlQuery = 'UPDATE' . Db::prefix(DbTableName::MESSENGER) .
            'SET recd = 1 WHERE (fromUser = :from OR toUser = :to) AND recd = 0';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
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
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function insert($sFrom, $sTo, $sMessage, $sDate)
    {
        $sSqlQuery = 'INSERT INTO' . Db::prefix(DbTableName::MESSENGER) .
            '(fromUser, toUser, message, sent) VALUES (:from, :to, :message, :date)';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':date', $sDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }
}
