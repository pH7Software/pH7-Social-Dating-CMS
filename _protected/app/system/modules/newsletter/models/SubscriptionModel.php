<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class SubscriptionModel extends UserCoreModel
{
    /**
     * Get all Active Subscribers (it is required by the law to send emails only to the confirmed opt-in subscribers).
     *
     * @return array
     */
    public function getSubscribers()
    {
        $rStmt = Db::getInstance()->prepare('SELECT email, name AS firstName FROM' . Db::prefix(DbTableName::SUBSCRIBER) . 'WHERE active = 1');
        $rStmt->execute();
        $aRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aRow;
    }

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

        $rStmt->bindValue(':name', $aData['name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':joinDate', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $aData['hash_validation'], \PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);
        $rStmt->bindValue(':active', $aData['active'], \PDO::PARAM_INT);
        $rStmt->bindValue(':affiliatedId', $aData['affiliated_id'], \PDO::PARAM_INT);
        $rStmt->execute();

        return (int)Db::getInstance()->lastInsertId();
    }

    /**
     * Delete a Subscriber.
     *
     * @param int $sEmail
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function unsubscribe($sEmail)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::SUBSCRIBER) . 'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Browse Subscribers.
     *
     * @param int|string $mLooking Integer for profile ID or string for a keyword
     * @param bool $bCount Put 'true' for count the subscribers or 'false' for the result of subscribers.
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|\stdClass Integer for the number subscribers returned or string for the subscribers list returned
     */
    public function browse($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlLimit = !$bCount ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(profileId)';
        $sSqlWhere = ctype_digit($mLooking) ? ' WHERE profileId = :looking' : ' WHERE name LIKE :looking OR email LIKE :looking OR ip LIKE :looking';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::SUBSCRIBER) . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        if (ctype_digit($mLooking)) {
            $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }
        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }
        Db::free($rStmt);

        return $mData;
    }
}
