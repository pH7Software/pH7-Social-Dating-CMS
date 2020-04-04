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

class BlogCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/blog';
    const CACHE_LIFETIME = 999990;
    const CACHE_SHORT_LIFETIME = 3600;

    /**
     * Gets all blog posts.
     *
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sOrder A constant: SearchCoreModel::CREATED (default value) or SearchCoreModel::UPDATED
     *
     * @return array
     */
    public function getPosts($iOffset, $iLimit, $sOrder = SearchCoreModel::CREATED)
    {
        $this->cache->start(
            self::CACHE_GROUP,
            'posts' . $iOffset . $iLimit . $sOrder,
            self::CACHE_SHORT_LIFETIME
        );

        if (!$aData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);

            $rStmt = Db::getInstance()->prepare(
                'SELECT * FROM' . Db::prefix(DbTableName::BLOG) . $sOrderBy . 'LIMIT :offset, :limit'
            );
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
            $rStmt->execute();
            $aData = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aData);
        }

        return $aData;
    }

    /**
     * Gets the total posts.
     *
     * @param int $iDay
     *
     * @return int
     */
    public function totalPosts($iDay = 0)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPosts', static::CACHE_LIFETIME);

        if (!$iTotalPosts = $this->cache->get()) {
            $iDay = (int)$iDay;
            $sSqlDay = ($iDay > 0) ? ' WHERE (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

            $rStmt = Db::getInstance()->prepare(
                'SELECT COUNT(postId) FROM' . Db::prefix(DbTableName::BLOG) . $sSqlDay
            );
            $rStmt->execute();

            $iTotalPosts = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalPosts);
        }

        return $iTotalPosts;
    }
}
