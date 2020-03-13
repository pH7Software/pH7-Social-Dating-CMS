<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;

class SubscriberCoreModel extends UserCoreModel
{
    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;

    /**
     * Adding a Subscriber.
     *
     * @param array $aData
     *
     * @return int The ID of the Subscriber.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::SUBSCRIBER) . '(name, email, joinDate, ip, hashValidation, active, affiliatedId)
            VALUES (:name, :email, :joinDate, :ip, :hashValidation, :active, :affiliatedId)');

        $rStmt->bindValue(':name', $aData['name'], PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], PDO::PARAM_STR);
        $rStmt->bindValue(':joinDate', $aData['current_date'], PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $aData['ip'], PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $aData['hash_validation'], PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);
        $rStmt->bindValue(':active', $aData['active'], PDO::PARAM_INT);
        $rStmt->bindValue(':affiliatedId', $aData['affiliated_id'], PDO::PARAM_INT);
        $rStmt->execute();

        return (int)Db::getInstance()->lastInsertId();
    }
}
