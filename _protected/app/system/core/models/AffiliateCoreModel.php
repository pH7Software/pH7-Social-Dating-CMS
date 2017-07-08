<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

// Abstract Class
class AffiliateCoreModel extends AdminCoreModel
{

    /**
     * Update Affiliate Commission.
     *
     * @param integer $iProfileId Affiliate ID.
     * @param integer $iAffCom Amount.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function updateUserJoinCom($iProfileId, $iAffCom)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Affiliates') . 'SET amount = amount + :amount WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':amount', $iAffCom, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        Db::free($rStmt);
        return $rStmt->execute();
    }

    /**
     * Get the Affiliated Id of a User.
     *
     * @param integer $iProfileId
     * @param string $sTable 'Members', 'Affiliates' or 'Subscribers'. Default 'Members'
     * @return integer The Affiliated ID
     */
    public function getAffiliatedId($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(static::CACHE_GROUP, 'affiliatedId' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            Various::checkModelTable($sTable);
            $iProfileId = (int)$iProfileId;

            $rStmt = Db::getInstance()->prepare('SELECT affiliatedId FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int)@$oRow->affiliatedId;
            unset($oRow);
            $this->cache->put($iData);
        }

        return $iData;
    }

    /**
     * Delete Affiliate.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        $oDb = Db::getInstance();
        $oDb->exec('DELETE FROM' . Db::prefix('AffiliatesInfo') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        $oDb->exec('DELETE FROM' . Db::prefix('Affiliates') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        unset($oDb);
    }

}
