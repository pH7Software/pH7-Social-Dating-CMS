<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class NoteCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/note';
    const CACHE_LIFETIME = 999990;
    const CACHE_SHORT_LIFETIME = 3600;

    // Disabled cache (if you have a few notes, you can enable it to improve performance)
    const POSTS_CACHE_ENABLED = false;

    /**
     * Gets all note posts.
     *
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sOrder A constant: SearchCoreModel::CREATED (default value) or SearchCoreModel::UPDATED
     * @param int|null $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved)
     *
     * @return array
     */
    public function getPosts($iOffset, $iLimit, $sOrder = SearchCoreModel::CREATED, $iApproved = 1)
    {
        $this->cache->enabled(self::POSTS_CACHE_ENABLED);

        $this->cache->start(
            self::CACHE_GROUP,
            'posts' . $iOffset . $iLimit . $sOrder . $iApproved,
            self::CACHE_SHORT_LIFETIME
        );

        if (!$aData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;
            $bIsApproved = isset($iApproved);

            $sSqlApproved = $bIsApproved ? ' WHERE approved = :approved' : '';
            $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);
            $sSqlQuery = 'SELECT n.*, m.username, m.firstName, m.sex FROM' . Db::prefix(DbTableName::NOTE) . ' AS n INNER JOIN ' .
                Db::prefix(DbTableName::MEMBER) . 'AS m ON n.profileId = m.profileId' . $sSqlApproved . $sOrderBy . 'LIMIT :offset, :limit';

            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
            if ($bIsApproved) {
                $rStmt->bindParam(':approved', $iApproved, PDO::PARAM_INT);
            }
            $rStmt->execute();
            $aData = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aData);
        }

        return $aData;
    }

    /**
     * Gets total note posts.
     *
     * @param int|null $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     * @param int $iDay Default 0
     *
     * @return int
     */
    public function totalPosts($iApproved = 1, $iDay = 0)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPosts', static::CACHE_LIFETIME);

        if (!$iTotalPosts = $this->cache->get()) {
            $iDay = (int)$iDay;
            $bIsApproved = isset($iApproved);

            $sSqlWhere = $bIsApproved ? 'WHERE' : '';
            $sSqlAnd = ($bIsApproved && $iDay > 0 ? ' AND' : ($iDay > 0 ? 'WHERE' : ''));
            $sSqlApproved = $bIsApproved ? ' approved = :approved' : '';
            $sSqlDay = ($iDay > 0) ? ' (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';
            $sSqlQuery = 'SELECT COUNT(postId) FROM' . Db::prefix(DbTableName::NOTE) . $sSqlWhere . $sSqlApproved . $sSqlAnd . $sSqlDay;
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            if ($bIsApproved) {
                $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_INT);
            }
            $rStmt->execute();

            $iTotalPosts = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalPosts);
        }

        return $iTotalPosts;
    }
}
