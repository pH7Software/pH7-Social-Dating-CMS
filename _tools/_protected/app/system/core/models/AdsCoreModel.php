<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Ads as AdsModel;
use PH7\Framework\Mvc\Model\Engine\Db;

class AdsCoreModel extends AdsModel
{
    const ACTIVE = '1';
    const DEACTIVATE = '0';

    const CACHE_GROUP = 'db/sys/core/ads';
    const CACHE_TIME = 604800;

    /**
     * Get Advertisements in the database.
     *
     * @param string|null $mActive 1 = active otherwise null. Default value is '1'
     * @param string $sTable The table.
     *
     * @return array The advertisements data.
     */
    public function get($mActive = self::ACTIVE, $iOffset, $iLimit, $sTable = AdsCore::AD_TABLE_NAME)
    {
        AdsCore::checkTable($sTable);
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlActive = !empty($mActive) ? 'WHERE active= :active' : '';
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . $sSqlActive . ' ORDER BY active ASC LIMIT :offset, :limit');
        if (!empty($mActive)) {
            $rStmt->bindValue(':active', $mActive, PDO::PARAM_STR);
        }
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();
        $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aRow;
    }

    /**
     * @param string $sName
     * @param string $sCode
     * @param int $iWidth
     * @param int $iHeight
     * @param string $sTable
     *
     * @return bool
     */
    public function add($sName, $sCode, $iWidth, $iHeight, $sTable = AdsCore::AD_TABLE_NAME)
    {
        AdsCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable) . '(name, code, width, height) VALUES(:name, :code, :width, :height)');
        $rStmt->bindValue(':name', $sName, PDO::PARAM_STR);
        $rStmt->bindValue(':code', $sCode, PDO::PARAM_STR);
        $rStmt->bindValue(':width', $iWidth, PDO::PARAM_INT);
        $rStmt->bindValue(':height', $iHeight, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iId
     * @param string $sStatus
     * @param string $sTable
     *
     * @return bool
     */
    public function setStatus($iId, $sStatus, $sTable = AdsCore::AD_TABLE_NAME)
    {
        AdsCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET active = :status WHERE adsId = :adsId');
        $rStmt->bindValue(':adsId', $iId, PDO::PARAM_INT);
        $rStmt->bindValue(':status', $sStatus, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iId
     * @param string $sTable
     *
     * @return bool
     */
    public function delete($iId, $sTable = AdsCore::AD_TABLE_NAME)
    {
        AdsCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix($sTable) . 'WHERE adsId = :adsId');
        $rStmt->bindValue(':adsId', $iId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iId
     * @param string $sName
     * @param string $sCode
     * @param string $sTable
     *
     * @return bool
     */
    public function update($iId, $sName, $sCode, $sTable = AdsCore::AD_TABLE_NAME)
    {
        AdsCore::checkTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET name = :name, code = :code WHERE adsId = :adsId');
        $rStmt->bindValue(':adsId', $iId, PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sName, PDO::PARAM_STR);
        $rStmt->bindValue(':code', $sCode, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Get Total Advertisements.
     *
     * @param string $sTable
     *
     * @return int
     */
    public function total($sTable = AdsCore::AD_TABLE_NAME)
    {
        $this->cache->start(self::CACHE_GROUP, 'total' . $sTable, static::CACHE_TIME);

        if (!$iTotalAds = $this->cache->get()) {
            AdsCore::checkTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(adsId) FROM' . Db::prefix($sTable));
            $rStmt->execute();
            $iTotalAds = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalAds);
        }

        return $iTotalAds;
    }
}
