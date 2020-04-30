<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class GameModel extends GameCoreModel
{
    /**
     * @param int|null $iCategoryId
     * @param int $iOffset
     * @param int $iLimit
     * @param bool $bCount
     *
     * @return array|\stdClass|bool
     */
    public function getCategory($iCategoryId = null, $iOffset, $iLimit, $bCount = false)
    {
        $this->cache->start(static::CACHE_GROUP, 'category' . $iCategoryId . $iOffset . $iLimit . $bCount, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $bIsCategoryId = $iCategoryId !== null;

            if ($bCount) {
                $sSql = 'SELECT c.*, COUNT(g.gameId) AS totalCatGames FROM' . Db::prefix(DbTableName::GAME_CATEGORY) .
                    'AS c INNER JOIN' . Db::prefix(DbTableName::GAME) . 'AS g
                    ON c.categoryId = g.categoryId GROUP BY c.name ASC LIMIT :offset, :limit';
            } else {
                $sSqlCategoryId = $bIsCategoryId ? ' WHERE categoryId = :categoryId ' : ' ';
                $sSql = 'SELECT * FROM' . Db::prefix(DbTableName::GAME_CATEGORY) . $sSqlCategoryId . 'ORDER BY name ASC LIMIT :offset, :limit';
            }

            $rStmt = Db::getInstance()->prepare($sSql);

            if ($bIsCategoryId) {
                $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
            }
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $mData = $bIsCategoryId ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);

            $this->cache->put($mData);
        }

        return $mData;
    }

    /**
     * @param string $sCategoryName
     * @param bool $bCount
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array
     */
    public function category($sCategoryName, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $sCategoryName = trim($sCategoryName);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'n');
        $sSqlSelect = !$bCount ? 'g.*, c.*' : 'COUNT(g.gameId)';
        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';

        $sSql = 'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::GAME) . 'AS g LEFT JOIN ' .
            Db::prefix(DbTableName::GAME_CATEGORY) .
            'AS c ON g.categoryId = c.categoryId WHERE c.name LIKE :name' . $sSqlOrder . $sSqlLimit;
        $rStmt = Db::getInstance()->prepare($sSql);

        $rStmt->bindValue(':name', '%' . $sCategoryName . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }

        Db::free($rStmt);

        return $mData;
    }

    /**
     * @param int $iGameId
     *
     * @return string
     */
    public function getFile($iGameId)
    {
        $this->cache->start(static::CACHE_GROUP, 'file' . $iGameId, static::CACHE_TIME);

        if (!$sFile = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT file FROM' . Db::prefix(DbTableName::GAME) . 'WHERE gameId = :gameId LIMIT 1');
            $rStmt->bindValue(':gameId', $iGameId, \PDO::PARAM_INT);
            $rStmt->execute();
            $sFile = $rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($sFile);
        }

        return $sFile;
    }

    /**
     * Search a Game.
     *
     * @param int|string $mLooking (integer for game ID or string for a keyword)
     * @param bool $bCount Put 'true' for count the games or 'false' for the result of games.
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array Returns int for the number games returned or DB object containing the games list.
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);
        $sSqlSelect = !$bCount ? '*' : 'COUNT(gameId)';

        $sSqlWhere = ' WHERE title LIKE :looking OR name LIKE :looking OR description LIKE :looking OR keywords LIKE :looking';
        if ($bDigitSearch) {
            $sSqlWhere = ' WHERE gameId = :looking';
        }

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::GAME) . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        if ($bDigitSearch) {
            $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }

        return $mData;
    }

    /**
     * @return int
     */
    public function totalGames()
    {
        $this->cache->start(static::CACHE_GROUP, 'totalGames', static::CACHE_TIME);

        if (!$iTotalGames = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(gameId) FROM' . Db::prefix(DbTableName::GAME));
            $rStmt->execute();
            $iTotalGames = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalGames);
        }

        return $iTotalGames;
    }

    /**
     * Set Number Downloads Statistics.
     *
     * @param int $iId
     *
     * @return void
     */
    public function setDownloadStat($iId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::GAME) . 'SET downloads = downloads+1 WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * This method was created to avoid retrieving the column "download" with the GameModel::get() method
     * since it uses the cache and therefore can not retrieve the number of real-time the number of download.
     *
     * @param int $iId
     *
     * @return int The number of downloads
     */
    public function getDownloadStat($iId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT downloads FROM' . Db::prefix(DbTableName::GAME) . 'WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return (int)$oRow->downloads;
    }

    public function add(array $aData)
    {
        $sSql = 'INSERT INTO' . Db::prefix(DbTableName::GAME) .
            '(categoryId, name, title, description, keywords, thumb, file) VALUES(:categoryId, :name, :title, :description, :keywords, :thumb, :file)';
        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindValue(':categoryId', $aData['category_id'], \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $aData['name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':title', $aData['title'], \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $aData['description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':keywords', $aData['keywords'], \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $aData['thumb'], \PDO::PARAM_STR);
        $rStmt->bindValue(':file', $aData['file'], \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    public function update(array $aData)
    {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::GAME) .
            'SET categoryId = :categoryId, name = :name, title = :title, description = :description, keywords = :keywords WHERE gameId = :id LIMIT 1'
        );
        $rStmt->bindValue(':id', $aData['id'], \PDO::PARAM_INT);
        $rStmt->bindValue(':categoryId', $aData['category_id'], \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $aData['name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':title', $aData['title'], \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $aData['description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':keywords', $aData['keywords'], \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    public function delete($iId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::GAME) . 'WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
