<?php
/**
 * @title            Lang Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          0.8
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db, PH7\Framework\Cache\Cache;

class Lang
{

    const CACHE_GROUP = 'db/lang';

    /**
     * Get information about the language.
     *
     * @param boolean $bOnlyActive Only active lang. Default: TRUE
     * @return object Language data.
     */
    public function getInfos($bOnlyActive = true)
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'list' . $bOnlyActive, 172800);

        if (!$oData = $oCache->get())
        {
            $sSqlWhere = ($bOnlyActive) ? 'WHERE active=\'1\'' : '';
            $rStmt = Db::getInstance()->prepare('SELECT * FROM ' . DB::prefix('Language') . $sSqlWhere . ' ORDER BY name ASC');
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $oCache->put($oData);
        }
        unset($oCache);

        return $oData;
    }

}
