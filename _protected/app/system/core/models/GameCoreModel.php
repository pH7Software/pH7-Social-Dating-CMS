<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class GameCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const CACHE_GROUP = 'db/sys/mod/game', CACHE_TIME = 93312000;

    public function get($sTitle = null, $iGameId = null, $iOffset, $iLimit, $sOrder = SearchCoreModel::NAME)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);

        $sSqlGameId = (!empty($iGameId)) ? ' WHERE title LIKE :title AND gameId =:gameId ' : '';
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Games') . $sSqlGameId . $sOrderBy . 'LIMIT :offset, :limit');
        (isset($sTitle, $iGameId)) ? $rStmt->bindValue(':title', $sTitle . '%', \PDO::PARAM_STR) : '';
        (isset($sTitle, $iGameId)) ? $rStmt->bindValue(':gameId', $iGameId, \PDO::PARAM_INT) : '';
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        $oData = (!empty($iGameId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oData;
    }

}
