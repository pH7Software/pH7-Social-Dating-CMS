<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

// Abstract Class
class LikeCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const CACHE_GROUP = 'db/sys/core/like';

    public function select($sKey)
    {
        $this->cache->start(self::CACHE_GROUP, 'select' . $sKey, 3600 * 168);

        if (!$oData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Likes') . 'WHERE keyId =:key LIMIT 1');
            $rStmt->bindValue(':key', $sKey, \PDO::PARAM_STR);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    public function update($sKey, $fLastIp)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Likes') . 'SET votes = votes + 1 , lastVote = NOW() , lastIp =:lastIp WHERE keyId =:key');
        $rStmt->bindValue(':key', $sKey, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastIp', $fLastIp, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function insert($sKey, $fLastIp)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Likes') . 'SET keyId =:key ,votes=1 , lastVote = NOW(), lastIp =:lastIp');
        $rStmt->bindValue(':key', $sKey, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastIp', $fLastIp, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

}
