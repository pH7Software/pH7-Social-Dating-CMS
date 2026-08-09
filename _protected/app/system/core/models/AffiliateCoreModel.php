<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Security\Security;
use PH7\Framework\Translate\Lang;

// Abstract Class
class AffiliateCoreModel extends AdminCoreModel
{
    /**
     * Update Affiliate Commission.
     *
     * @param int       $iProfileId affiliate ID
     * @param float|int $mAffCom    amount
     *
     * @return bool returns TRUE on success or FALSE on failure
     */
    public function updateUserJoinCom($iProfileId, $mAffCom)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::AFFILIATE) . 'SET amount = amount + :amount WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':amount', number_format((float)$mAffCom, 2, '.', ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $bUpdated = $rStmt->execute();
        Db::free($rStmt);

        return $bUpdated;
    }

    /**
     * Get the Affiliated Id of a User.
     *
     * @param int    $iProfileId
     * @param string $sTable     DbTableName::MEMBER, DbTableName::AFFILIATE or DbTableName::SUBSCRIBER. Default DbTableName::MEMBER
     *
     * @return int The Affiliated ID
     */
    public function getAffiliatedId($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(static::CACHE_GROUP, 'affiliatedId' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$iAffiliatedId = $this->cache->get()) {
            Various::checkModelTable($sTable);
            $iProfileId = (int)$iProfileId;

            $rStmt = Db::getInstance()->prepare('SELECT affiliatedId FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $iAffiliatedId = (int)$rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($iAffiliatedId);
        }

        return $iAffiliatedId;
    }

    /**
     * Adding an Affiliate.
     *
     * @return int the ID of the Affiliate
     */
    public function add(array $aData)
    {
        $sCurrentDate = (new CDateTime())->get()->dateTime(static::DATETIME_FORMAT);

        return $this->runRegistrationTransaction(
            function (Db $oDb) use ($aData, $sCurrentDate): int {
                $rStmt = $oDb->prepare('INSERT INTO' . Db::prefix(DbTableName::AFFILIATE) . '(email, username, password, firstName, lastName, sex, birthDate, bankAccount, lang, ip, joinDate, lastActivity)
                VALUES (:email, :username, :password, :firstName, :lastName, :sex, :birthDate, :bankAccount, :lang, :ip, :joinDate, :lastActivity)');

                $rStmt->bindValue(':email', trim($aData['email']), \PDO::PARAM_STR);
                $rStmt->bindValue(':username', trim($aData['username']), \PDO::PARAM_STR);
                $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
                $rStmt->bindValue(':firstName', $aData['first_name'], \PDO::PARAM_STR);
                $rStmt->bindValue(':lastName', $aData['last_name'], \PDO::PARAM_STR);
                $rStmt->bindValue(':sex', $aData['sex'], \PDO::PARAM_STR);
                $rStmt->bindValue(':birthDate', $aData['birth_date'], \PDO::PARAM_STR);
                $rStmt->bindValue(':bankAccount', $aData['bank_account'], \PDO::PARAM_STR);
                $rStmt->bindValue(':lang', !empty($aData['lang']) ? substr($aData['lang'], 0, 5) : Lang::DEFAULT_LOCALE, \PDO::PARAM_STR);
                $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
                $rStmt->bindValue(':joinDate', $sCurrentDate, \PDO::PARAM_STR);
                $rStmt->bindValue(':lastActivity', $sCurrentDate, \PDO::PARAM_STR);
                if (!$rStmt->execute()) {
                    throw new \RuntimeException('The affiliate account could not be created.');
                }
                $this->setKeyId($oDb->lastInsertId()); // Set the affiliate's ID
                Db::free($rStmt);

                if (!$this->setInfoFields($aData)) {
                    throw new \RuntimeException('The complete affiliate profile could not be created.');
                }

                return $this->getKeyId();
            }
        );
    }

    public function setInfoFields(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::AFFILIATE_INFO) . '(profileId, middleName, country, city, state, zipCode, phone, description, website)
            VALUES (:profileId, :middleName, :country, :city, :state, :zipCode, :phone, :description, :website)');

        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        $rStmt->bindValue(':middleName', !empty($aData['middle_name']) ? $aData['middle_name'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':country', !empty($aData['country']) ? $aData['country'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':city', !empty($aData['city']) ? $aData['city'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':state', !empty($aData['state']) ? $aData['state'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', !empty($aData['zip_code']) ? $aData['zip_code'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $aData['description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':phone', !empty($aData['phone']) ? $aData['phone'] : '', \PDO::PARAM_STR);
        $rStmt->bindValue(':website', trim($aData['website']), \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Delete Affiliate.
     *
     * @param int    $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        $oDb = Db::getInstance();
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::AFFILIATE_LOG_SESS) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::AFFILIATE_INFO) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::AFFILIATE) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        unset($oDb);
    }
}
