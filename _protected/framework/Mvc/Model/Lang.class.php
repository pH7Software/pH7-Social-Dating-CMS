<?php
/**
 * @title            Lang Model Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          0.8
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PH7\DbTableName;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Engine\Db;

class Lang
{
    const CACHE_GROUP = 'db/lang';

    /**
     * Get information about the language(s).
     *
     * @param bool $bOnlyActive Only active lang
     *
     * @return array Get the info of the available languages.
     */
    public function getInfos($bOnlyActive = true)
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'list' . $bOnlyActive, 172800);

        if (!$aData = $oCache->get()) {
            $sSqlWhere = $bOnlyActive ? 'WHERE active = \'1\'' : '';
            $sSqlQuery = 'SELECT * FROM ' . DB::prefix(DbTableName::LANGUAGE_INFO) . $sSqlWhere . ' ORDER BY name ASC';
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->execute();
            $aData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $oCache->put($aData);
        }
        unset($oCache);

        return $aData;
    }
}
