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
use stdClass;

class GameCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/game';
    const CACHE_TIME = 93312000;

    /**
     * @param string|null $sTitle
     * @param int|null $iGameId
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sOrder
     *
     * @return array|stdClass
     */
    public function get($sTitle = null, $iGameId = null, $iOffset, $iLimit, $sOrder = SearchCoreModel::NAME)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $bIsTitle = $sTitle !== null;
        $bIsGameId = $iGameId !== null;

        $sOrderBy = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);

        $sSqlGameId = ($bIsTitle && $bIsGameId) ? ' WHERE title LIKE :title AND gameId =:gameId ' : '';
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::GAME) . $sSqlGameId . $sOrderBy . 'LIMIT :offset, :limit');
        if ($bIsTitle) {
            $rStmt->bindValue(':title', $sTitle . '%', PDO::PARAM_STR);
        }
        if ($bIsGameId) {
            $rStmt->bindValue(':gameId', $iGameId, PDO::PARAM_INT);
        }
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();
        $mData = $bIsGameId ? $rStmt->fetch(PDO::FETCH_OBJ) : $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $mData;
    }
}
