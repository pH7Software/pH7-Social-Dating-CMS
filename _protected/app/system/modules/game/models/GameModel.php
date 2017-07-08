<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class GameModel extends GameCoreModel
{
    public function getCategory($iCategoryId = null, $iOffset, $iLimit, $bCount = false)
    {
        $this->cache->start(static::CACHE_GROUP, 'category' . $iCategoryId . $iOffset . $iLimit . $bCount, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            if ($bCount) {
                $sSql = 'SELECT c.*, COUNT(g.gameId) AS totalCatGames FROM' . Db::prefix('GamesCategories') . 'AS c INNER JOIN' . Db::prefix('Games') . 'AS g
                ON c.categoryId = g.categoryId GROUP BY c.name ASC LIMIT :offset, :limit';
            } else {
                $sSqlCategoryId = (!empty($iCategoryId)) ? ' WHERE categoryId = :categoryId ' : ' ';
                $sSql = 'SELECT * FROM' . Db::prefix('GamesCategories') . $sSqlCategoryId . 'ORDER BY name ASC LIMIT :offset, :limit';
            }

            $rStmt = Db::getInstance()->prepare($sSql);

            if (!empty($iCategoryId)) $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = (!empty($iCategoryId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * @param string $sCategoryName
     * @param boolean $bCount
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @return integer|object
     */
    public function category($sCategoryName, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $sCategoryName = trim($sCategoryName);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'n');
        $sSqlSelect = (!$bCount) ? 'g.*, c.*' : 'COUNT(g.gameId) AS totalGames';
        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Games') . 'AS g LEFT JOIN ' . Db::prefix('GamesCategories') . 'AS c ON g.categoryId = c.categoryId
        WHERE c.name LIKE :name' . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':name', '%' . $sCategoryName . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int)$oRow->totalGames;
            unset($oRow);
        }

        return $mData;
    }

    public function getFile($iGameId)
    {
        $this->cache->start(static::CACHE_GROUP, 'file' . $iGameId, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT file FROM' . Db::prefix('Games') . 'WHERE gameId = :gameId LIMIT 1');
            $rStmt->bindValue(':gameId', $iGameId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = $oRow->file;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Search a Game.
     *
     * @param integer|string $mLooking (integer for game ID or string for a keyword)
     * @param boolean $bCount Put 'true' for count the games or 'false' for the result of games.
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return integer|object Returns integer for the number games returned or DB object containing the games list.
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(gameId) AS totalGames';

        if (ctype_digit($mLooking)) {
            $sSqlWhere = ' WHERE gameId = :looking';
        } else {
            $sSqlWhere = ' WHERE title LIKE :looking OR name LIKE :looking OR description LIKE :looking OR keywords LIKE :looking';
        }

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Games') . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int)$oRow->totalGames;
            unset($oRow);
        }

        return $mData;
    }

    public function totalGames()
    {
        $this->cache->start(static::CACHE_GROUP, 'totalGames', static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(gameId) AS totalGames FROM' . Db::prefix('Games'));
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = (int)$oRow->totalGames;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Set Number Downloads Statistics.
     *
     * @param integer $iId
     * @return void
     */
    public function setDownloadStat($iId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Games') . 'SET downloads = downloads+1 WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * This method was created to avoid retrieving the column "download" with the GameModel::get() method
     * since it uses the cache and therefore can not retrieve the number of real-time the number of download.
     *
     * @param integer $iId
     * @return integer The number of downloads
     */
    public function getDownloadStat($iId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT downloads FROM' . Db::prefix('Games') . 'WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->downloads;
    }

    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Games') . '(categoryId, name, title, description, keywords, thumb, file) VALUES(:categoryId, :name, :title, :description, :keywords, :thumb, :file)');
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
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Games') . 'SET categoryId = :categoryId, name = :name, title = :title, description = :description, keywords = :keywords WHERE gameId = :id LIMIT 1');
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
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Games') . 'WHERE gameId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }
}
