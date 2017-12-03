<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class NoteCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/note';
    const CACHE_TIME = 999990;

    /**
     * Gets all note posts.
     *
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sOrder A constant: SearchCoreModel::CREATED (default value) or SearchCoreModel::UPDATED
     * @param int $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     *
     * @return array
     */
    public function getPosts($iOffset, $iLimit, $sOrder = SearchCoreModel::CREATED, $iApproved = 1)
    {
        $this->cache->enabled(false); // Disabled the cache (if you have a few notes, you can enable it to improve performance).

        // We do not have a long duration of the cache for the changes of positions to be easily updated on the list of Notes of the home page.
        $this->cache->start(self::CACHE_GROUP, 'posts' . $iOffset . $iLimit . $sOrder . $iApproved, 3600);

        if (!$aData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;
            $bIsApprived = isset($iApproved);

            $sSqlApproved = $bIsApprived ? ' WHERE approved = :approved' : '';
            $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);
            $rStmt = Db::getInstance()->prepare('SELECT n.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Notes') . ' AS n INNER JOIN ' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId' . $sSqlApproved . $sOrderBy . 'LIMIT :offset, :limit');
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            if ($bIsApprived) {
                $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
            }
            $rStmt->execute();
            $aData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aData);
        }

        return $aData;
    }

    /**
     * Gets total note posts.
     *
     * @param int $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     * @param int $iDay Default 0
     *
     * @return int
     */
    public function totalPosts($iApproved = 1, $iDay = 0)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPosts', static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $iDay = (int)$iDay;
            $bIsApprived = isset($iApproved);

            $sSqlWhere = $bIsApprived ? 'WHERE' : '';
            $sSqlAnd = ($bIsApprived && $iDay > 0 ? ' AND' : ($iDay > 0 ? 'WHERE' : ''));
            $sSqlApproved = $bIsApprived ? ' approved = :approved' : '';
            $sSqlDay = ($iDay > 0) ? ' (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(postId) AS totalPosts FROM' . Db::prefix('Notes') . $sSqlWhere . $sSqlApproved . $sSqlAnd . $sSqlDay);
            if ($bIsApprived) {
                $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            }

            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int)$oRow->totalPosts;
            unset($oRow);
            $this->cache->put($iData);
        }

        return $iData;
    }
}
