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

// Abstract Class
class LikeCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/core/like';

    /**
     * @param string $sKey
     *
     * @return array
     */
    public function select($sKey)
    {
        $this->cache->start(self::CACHE_GROUP, 'select' . $sKey, 3600 * 168);

        if (!$aData = $this->cache->get()) {
            $sSqlQuery = 'SELECT * FROM' . Db::prefix(DbTableName::LIKE) . 'WHERE keyId =:key LIMIT 1';
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->bindValue(':key', $sKey, PDO::PARAM_STR);
            $rStmt->execute();
            $aData = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aData);
        }

        return $aData;
    }

    /**
     * @param string $sKey
     * @param float $fLastIp
     *
     * @return bool
     */
    public function update($sKey, $fLastIp)
    {
        $sSqlQuery = 'UPDATE' . Db::prefix(DbTableName::LIKE) .
            'SET votes = votes + 1 , lastVote = NOW() , lastIp =:lastIp WHERE keyId =:key';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':key', $sKey, PDO::PARAM_STR);
        $rStmt->bindValue(':lastIp', $fLastIp, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param string $sKey
     * @param float $fLastIp
     *
     * @return bool
     */
    public function insert($sKey, $fLastIp)
    {
        $sSqlQuery = 'INSERT INTO' . Db::prefix(DbTableName::LIKE) .
            'SET keyId =:key ,votes=1 , lastVote = NOW(), lastIp =:lastIp';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':key', $sKey, PDO::PARAM_STR);
        $rStmt->bindValue(':lastIp', $fLastIp, PDO::PARAM_STR);

        return $rStmt->execute();
    }
}
