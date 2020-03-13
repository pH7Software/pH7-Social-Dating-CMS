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
use PH7\Framework\Mvc\Model\Engine\Util\Various;

// Abstract Class
class RatingCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/core/rating';
    const CACHE_TIME = 604800;

    /**
     * @param int $iId
     * @param string $sTable
     *
     * @return int
     */
    public function getVote($iId, $sTable)
    {
        $this->cache->start(
            self::CACHE_GROUP,
            'getVote' . $iId . $sTable,
            static::CACHE_TIME
        );

        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        if (!$iVote = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT votes FROM' . Db::prefix($sTable) .
                'WHERE ' . $sWhere . ' = :id LIMIT 1');
            $rStmt->bindValue(':id', $iId, PDO::PARAM_INT);
            $rStmt->execute();
            $iVote = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iVote);
        }

        return $iVote;
    }

    /**
     * @param int $iId
     * @param string $sTable
     *
     * @return float
     */
    public function getScore($iId, $sTable)
    {
        $this->cache->start(
            self::CACHE_GROUP,
            'getScore' . $iId . $sTable,
            static::CACHE_TIME
        );

        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        if (!$fScore = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT score FROM' . Db::prefix($sTable) .
                'WHERE ' . $sWhere . ' = :id LIMIT 1');
            $rStmt->bindValue(':id', $iId, PDO::PARAM_INT);
            $rStmt->execute();
            $fScore = (float)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($fScore);
        }

        return $fScore;
    }

    /**
     * @param int $iId
     * @param string $sTable
     *
     * @return bool
     */
    public function updateVotes($iId, $sTable)
    {
        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) .
            'SET votes = votes + 1 WHERE ' . $sWhere . ' = :id');
        $rStmt->bindValue(':id', $iId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param float $fScore
     * @param int $iId
     * @param string $sTable
     *
     * @return bool
     */
    public function updateScore($fScore, $iId, $sTable)
    {
        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) .
            'SET score = :score WHERE ' . $sWhere . ' = :id');
        $rStmt->bindValue(':score', $fScore);
        $rStmt->bindValue(':id', $iId);

        return $rStmt->execute();
    }
}
