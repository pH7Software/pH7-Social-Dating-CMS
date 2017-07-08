<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class BlogCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/blog';
    const CACHE_TIME = 999990;

    /**
     * Gets all blog posts.
     *
     * @param integer $iOffset
     * @param integer $iLimit
     * @param string $sOrder A constant: SearchCoreModel::CREATED (default value) or SearchCoreModel::UPDATED
     *
     * @return string
     */
    public function getPosts($iOffset, $iLimit, $sOrder = SearchCoreModel::CREATED)
    {
        // We do not have a long duration of the cache for the changes of positions to be easily updated on the list of Blogs of the home page.
        $this->cache->start(self::CACHE_GROUP, 'posts' . $iOffset . $iLimit . $sOrder, 3600);

        if (!$oData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Blogs') . $sOrderBy . 'LIMIT :offset, :limit');
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Gets the total posts.
     *
     * @param integer $iDay
     *
     * @return integer
     */
    public function totalPosts($iDay = 0)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPosts', static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $iDay = (int)$iDay;
            $sSqlDay = ($iDay > 0) ? ' WHERE (createdDate + INTERVAL ' . $iDay . ' DAY) > NOW()' : '';

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(postId) AS totalPosts FROM' . Db::prefix('Blogs') . $sSqlDay);
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
