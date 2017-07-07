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
class RatingCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const CACHE_GROUP = 'db/sys/core/rating', CACHE_TIME = 604800;

    public function getVote($iId, $sTable)
    {
        $this->cache->start(self::CACHE_GROUP, 'getVote' . $iId . $sTable, static::
            CACHE_TIME);

        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        if (!$iData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT votes FROM' . Db::prefix($sTable) .
                'WHERE ' . $sWhere . ' = :id LIMIT 1');
            $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) @$oRow->votes;
            unset($oRow);
            $this->cache->put($iData);
        }
        return $iData;
    }

    public function getScore($iId, $sTable)
    {
        $this->cache->start(self::CACHE_GROUP, 'getScore' . $iId . $sTable, static::
            CACHE_TIME);

        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        if (!$fData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT score FROM' . Db::prefix($sTable) .
                'WHERE ' . $sWhere . ' = :id LIMIT 1');
            $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $fData = (float) @$oRow->score;
            unset($oRow);
            $this->cache->put($fData);
        }
        return $fData;
    }

    public function updateVotes($iId, $sTable)
    {

        $sTable = Various::checkTable($sTable);
        $sWhere = Various::convertTableToId($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) .
            'SET votes = votes + 1 WHERE ' . $sWhere . ' = :id');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

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
