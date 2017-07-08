<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class NoteCoreModel extends Framework\Mvc\Model\Engine\Model
{
    const CACHE_GROUP = 'db/sys/mod/note';
    const CACHE_TIME = 999990;

    /**
     * Gets all note posts.
     *
     * @param integer $iOffset
     * @param integer $iLimit
     * @param string $sOrder A constant: SearchCoreModel::CREATED (default value) or SearchCoreModel::UPDATED
     * @param integer $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     *
     * @return string
     */
    public function getPosts($iOffset, $iLimit, $sOrder = SearchCoreModel::CREATED, $iApproved = 1)
    {
        $this->cache->enabled(false); // Disabled the cache (if you have a few notes, you can enable it to improve performance).

        // We do not have a long duration of the cache for the changes of positions to be easily updated on the list of Notes of the home page.
        $this->cache->start(self::CACHE_GROUP, 'posts' . $iOffset . $iLimit . $sOrder . $iApproved, 3600);

        if (!$oData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sSqlApproved = (isset($iApproved)) ? ' WHERE approved = :approved' : '';
            $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);
            $rStmt = Db::getInstance()->prepare('SELECT n.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Notes') . ' AS n INNER JOIN ' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId' . $sSqlApproved . $sOrderBy . 'LIMIT :offset, :limit');
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            if (isset($iApproved)) $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Gets total note posts.
     *
     * @param integer $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     * @param integer $iDay Default 0
     *
     * @return integer
     */
    public function totalPosts($iApproved = 1, $iDay = 0)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPosts', static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $iDay = (int)$iDay;
            $sSqlWhere = (isset($iApproved)) ? 'WHERE' : '';
            $sSqlAnd = (isset($iApproved) && $iDay > 0 ? ' AND' : ($iDay > 0 ? 'WHERE' : ''));
            $sSqlApproved = (isset($iApproved)) ? ' approved = :approved' : '';
            $sSqlDay = ($iDay > 0) ? ' (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(postId) AS totalPosts FROM' . Db::prefix('Notes') . $sSqlWhere . $sSqlApproved . $sSqlAnd . $sSqlDay);
            if (isset($iApproved)) $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
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
