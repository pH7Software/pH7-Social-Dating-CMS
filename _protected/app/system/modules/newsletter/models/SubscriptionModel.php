<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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
     * @return object
     */
    public function getSubscribers()
    {
        $rStmt = Db::getInstance()->prepare('SELECT email, name AS firstName FROM' . Db::prefix('Subscribers') . 'WHERE active = 1');
        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    /**
     * Adding a Subscriber.
     *
     * @param array $aData
     *
     * @return integer The ID of the Subscriber.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Subscribers') . '(name, email, joinDate, ip, hashValidation, active, affiliatedId)
            VALUES (:name, :email, :joinDate, :ip, :hashValidation, :active, :affiliatedId)');

        $rStmt->bindValue(':name', $aData['name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':joinDate', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $aData['hash_validation'], \PDO::PARAM_STR, 40);
        $rStmt->bindValue(':active', $aData['active'], \PDO::PARAM_INT);
        $rStmt->bindValue(':affiliatedId', $aData['affiliated_id'], \PDO::PARAM_INT);
        $rStmt->execute();

        return (int)Db::getInstance()->lastInsertId();
    }

    /**
     * Delete a Subscriber.
     *
     * @param integer $sEmail
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function unsubscribe($sEmail)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Subscribers') . 'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Browse Subscribers.
     *
     * @param integer|string $mLooking Integer for profile ID or string for a keyword
     * @param boolean $bCount Put 'true' for count the subscribers or 'false' for the result of subscribers.
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return integer|object Integer for the number subscribers returned or string for the subscribers list returned
     */
    public function browse($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlLimit = (!$bCount) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(profileId) AS totalUsers';
        $sSqlWhere = (ctype_digit($mLooking)) ? ' WHERE profileId = :looking' : ' WHERE name LIKE :looking OR email LIKE :looking OR ip LIKE :looking';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Subscribers') . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            $mData = (int)$oRow->totalUsers;
            unset($oRow);
        }
        Db::free($rStmt);

        return $mData;
    }
}
