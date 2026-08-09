<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Security\Security;

class AffiliateModel extends AffiliateCoreModel
{
    /**
     * Add a new affiliate.
     *
     * @return bool returns TRUE on success or FALSE on failure
     */
    public function join(array $aData)
    {
        return $this->runRegistrationTransaction(
            function (Db $oDb) use ($aData): bool {
                $rStmt = $oDb->prepare('INSERT INTO' . Db::prefix(DbTableName::AFFILIATE) .
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
                $rStmt->bindParam(':hashValidation', $aData['hash_validation'], \PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);
                $rStmt->bindValue(':joinDate', $aData['current_date'], \PDO::PARAM_STR);
                $rStmt->bindValue(':lastActivity', $aData['current_date'], \PDO::PARAM_STR);
                $rStmt->bindValue(':affiliatedId', $aData['affiliated_id'], \PDO::PARAM_INT);
                if (!$rStmt->execute()) {
                    throw new \RuntimeException('The affiliate account could not be created.');
                }
                $this->setKeyId($oDb->lastInsertId()); // Set the affiliate's ID
                Db::free($rStmt);

                if (!$this->join2($aData)) {
                    throw new \RuntimeException('The complete affiliate profile could not be created.');
                }

                return true;
            }
        );
    }

    /**
     * Join part 2.
     *
     * @return bool returns TRUE on success or FALSE on failure
     */
    public function join2(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::AFFILIATE_INFO) .
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
     * @param int $iProfileId
     *
     * @return bool returns TRUE on success or FALSE on failure
     */
    public function addRefer($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::AFFILIATE) . 'SET refer = refer+1 WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $bUpdated = $rStmt->execute();
        Db::free($rStmt);

        return $bUpdated;
    }

    /**
     * Search an affiliate.
     *
     * @param int|string $mLooking (integer for profile ID or string for a keyword)
     * @param bool       $bCount   put 'true' for count the affiliates or 'false' for the result of affiliates
     * @param string     $sOrderBy
     * @param int        $iSort
     * @param int        $iOffset
     * @param int        $iLimit
     *
     * @return array|int an array containing stdClass object with the affiliates or an integer for the total number of users returned
     */
    public function searchAff($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlLimit = !$bCount ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(a.profileId)';

        $sSqlWhere = ' WHERE username LIKE :looking OR firstName LIKE :looking OR lastName LIKE :looking OR email LIKE :looking OR bankAccount LIKE :looking OR sex LIKE :looking OR ip LIKE :looking';
        if ($bDigitSearch) {
            $sSqlWhere = ' WHERE a.profileId = :looking';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::AFFILIATE) . 'AS a LEFT JOIN' . Db::prefix(DbTableName::AFFILIATE_INFO) . 'AS i ON a.profileId = i.profileId' . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        if ($bDigitSearch) {
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

    /**
     * Get the Affiliate's Amount.
     *
     * @param int $iProfileId
     *
     * @return int|float The amount
     */
    public function getAmount($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT amount FROM' . Db::prefix(DbTableName::AFFILIATE) . ' WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow->amount;
    }
}
