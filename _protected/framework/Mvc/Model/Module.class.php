<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db;

class Module extends Engine\Model
{

    const CACHE_GROUP = 'db/sys/core/enabled_modules', CACHE_TIME = 172800;

    /**
     * Get all modules status (enabled & disabled).
     *
     * @param string $sFolderName  Name of the module folder. Default: NULL
     * @return object
     */
    public function get($sFolderName = null)
    {
        $this->cache->start(static::CACHE_GROUP, 'list' . $sFolderName, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $bIsFolderName = !empty($sFolderName);
            $sSelect = ($bIsFolderName) ? 'enabled' : '*';
            $sSqlWhere = ($bIsFolderName) ? 'WHERE folderName = :modName LIMIT 1' : '';

            $rStmt = Db::getInstance()->prepare('SELECT ' . $sSelect . ' FROM ' . DB::prefix('SysModsEnabled') . $sSqlWhere);
            if ($bIsFolderName) $rStmt->bindValue(':modName', $sFolderName, \PDO::PARAM_STR);
            $rStmt->execute();
            $sFetchMethod = ($bIsFolderName) ? 'fetch' : 'fetchAll';
            $oData = $rStmt->$sFetchMethod(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    /**
     * Update the module status (enabled/disabled).
     *
     * @param string $iId Module ID
     * @param string $sIsEnabled '1' = Enabled | '0' = Disabled. Need to be string because in DB it is an "enum". Default: '1'
     * @return mixed (integer | boolean) Returns the number of rows on success or FALSE on failure.
     */
    public function update($iId, $sIsEnabled = '1')
    {
        return $this->orm->update('SysModsEnabled', 'enabled', $sIsEnabled, 'moduleId', $iId);
    }

}
