<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Model
 */
namespace PH7;
use PH7\Framework\Mvc\Model\Engine\Db;

class SubscriptionModel extends UserCoreModel
{

    public function getSubscribers()
    {
        $rStmt = Db::getInstance()->prepare('SELECT s.*, m.email, m.firstName AS name, n.enableNewsletters
            FROM' . Db::prefix('Subscribers') . 'AS s INNER JOIN' . Db::prefix('Members') . 'AS m INNER JOIN' . Db::prefix('MembersNotifications') . 'as n ON m.profileId = n.profileId
            WHERE (s.active = 1) AND (m.username <> \'' . PH7_GHOST_USERNAME . '\') AND (m.username IS NOT NULL) AND (m.firstName IS NOT NULL) AND (m.sex IS NOT NULL) AND (m.matchSex IS NOT NULL) AND (m.country IS NOT NULL) AND (m.city IS NOT NULL) AND (m.groupId = 2) AND (n.enableNewsletters = 1)');
        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    /**
     * Adding a Subscriber.
     *
     * @param array $aData
     * @return integer The ID of the Subscriber.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Subscribers') . '(name, email, hashValidation, active) VALUES (:name, :email, :hashValidation, :active)');
        $rStmt->bindValue(':name', $aData['name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':hashValidation', $aData['hash_validation'], \PDO::PARAM_STR);
        $rStmt->bindValue(':active', $aData['active'], \PDO::PARAM_INT);
        $rStmt->execute();
        return (int) Db::getInstance()->lastInsertId();
    }

    /**
     * Delete a Subscriber.
     *
     * @param integer $sEmail
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function unsubscribe($sEmail)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Subscribers') . 'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

}
