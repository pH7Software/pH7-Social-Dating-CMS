<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Security\Security;

class AffiliateModel extends AffiliateCoreModel
{
    /**
     * Add a new affiliate.
     *
     * @param array $aData
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function join(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Affiliates') .
            '(email, username, password, firstName, lastName, sex, birthDate, active, ip, hashValidation, joinDate, lastActivity, affiliatedId)
            VALUES (:email, :username, :password, :firstName, :lastName, :sex, :birthDate, :active, :ip, :hashValidation, :joinDate, :lastActivity, :affiliatedId)');

        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $aData['username'], \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':lastName', $aData['last_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':sex', $aData['sex'], \PDO::PARAM_STR);
        $rStmt->bindValue(':birthDate', $aData['birth_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':active', $aData['is_active'], \PDO::PARAM_INT);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $aData['hash_validation'], \PDO::PARAM_STR, 40);
        $rStmt->bindValue(':joinDate', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $aData['current_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':affiliatedId', $aData['affiliated_id'], \PDO::PARAM_INT);
        $rStmt->execute();
        $this->setKeyId(Db::getInstance()->lastInsertId()); // Set the affiliate's ID
        Db::free($rStmt);

        return $this->join2($aData);
    }

    /**
     * Join part 2.
     *
     * @param array $aData
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function join2(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('AffiliatesInfo') .
            '(profileId, country, city, state, zipCode) VALUES (:profileId, :country, :city, :state, :zipCode)');

        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        $rStmt->bindParam(':country', $aData['country'], \PDO::PARAM_STR, 2);
        $rStmt->bindValue(':city', $aData['city'], \PDO::PARAM_STR);
        $rStmt->bindValue(':state', $aData['state'], \PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', $aData['zip_code'], \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Add a reference affiliate.
     *
     * @param integer $iProfileId
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function addRefer($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Affiliates') . 'SET refer = refer+1 WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        Db::free($rStmt);

        return $rStmt->execute();
    }

    /**
     * Search an affiliate.
     *
     * @param integer|string $mLooking (integer for profile ID or string for a keyword)
     * @param boolean $bCount Put 'true' for count the affiliates or 'false' for the result of affiliates.
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return object|integer Object for the affiliate list or Integer for the total number users returned.
     */
    public function searchAff($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlLimit = (!$bCount) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(a.profileId) AS totalUsers';

        if (ctype_digit($mLooking)) {
            $sSqlWhere = ' WHERE a.profileId = :looking';
        } else {
            $sSqlWhere = ' WHERE username LIKE :looking OR firstName LIKE :looking OR lastName LIKE :looking OR email LIKE :looking OR bankAccount LIKE :looking OR sex LIKE :looking OR ip LIKE :looking';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Affiliates') . 'AS a LEFT JOIN' . Db::prefix('AffiliatesInfo') . 'AS i ON a.profileId = i.profileId' . $sSqlWhere . $sSqlOrder . $sSqlLimit);

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

    /**
     * Adding an Affiliate.
     *
     * @param array $aData
     *
     * @return integer The ID of the Affiliate.
     */
    public function add(array $aData)
    {
        $sCurrentDate = (new Framework\Date\CDateTime)->get()->dateTime('Y-m-d H:i:s');

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Affiliates') . '(email, username, password, firstName, lastName, sex, birthDate, bankAccount, ip, joinDate, lastActivity)
        VALUES (:email, :username, :password, :firstName, :lastName, :sex, :birthDate, :bankAccount, :ip, :joinDate, :lastActivity)');

        $rStmt->bindValue(':email', trim($aData['email']), \PDO::PARAM_STR);
        $rStmt->bindValue(':username', trim($aData['username']), \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':lastName', $aData['last_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':sex', $aData['sex'], \PDO::PARAM_STR);
        $rStmt->bindValue(':birthDate', $aData['birth_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':bankAccount', $aData['bank_account'], \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindValue(':joinDate', $sCurrentDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $sCurrentDate, \PDO::PARAM_STR);
        $rStmt->execute();
        $this->setKeyId(Db::getInstance()->lastInsertId()); // Set the affiliate's ID
        Db::free($rStmt);
        $this->setInfoFields($aData);

        return $this->getKeyId();
    }

    public function setInfoFields(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('AffiliatesInfo') . '(profileId, middleName, country, city, state, zipCode, phone, description, website)
            VALUES (:profileId, :middleName, :country, :city, :state, :zipCode, :phone, :description, :website)');

        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        $rStmt->bindValue(':middleName', $aData['middle_name'], \PDO::PARAM_STR);
        $rStmt->bindParam(':country', $aData['country'], \PDO::PARAM_STR, 2);
        $rStmt->bindValue(':city', $aData['city'], \PDO::PARAM_STR);
        $rStmt->bindValue(':state', $aData['state'], \PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', $aData['zip_code'], \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $aData['description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':phone', $aData['phone'], \PDO::PARAM_STR);
        $rStmt->bindValue(':website', trim($aData['website']), \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Get the Affiliate's Amount.
     *
     * @param integer $iProfileId
     *
     * @return integer|float The amount
     */
    public function getAmount($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT amount FROM' . Db::prefix('Affiliates') . ' WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow->amount;
    }
}
