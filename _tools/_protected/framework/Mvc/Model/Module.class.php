<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PH7\DbTableName;
use PH7\Framework\Mvc\Model\Engine\Db;

class Module extends Engine\Model
{
    /**
     * Cache lifetime set to 2 days.
     */
    const CACHE_TIME = 172800;

    const CACHE_GROUP = 'db/sys/core/enabled_modules';

    const YES = '1'; // Enabled
    const NO = '0'; // Disabled

    /**
     * Get all modules status (enabled and disabled).
     *
     * @param string|null $sFolderName Name of the module folder.
     *
     * @return \stdClass|array
     */
    public function get($sFolderName = null)
    {
        $this->cache->start(
            static::CACHE_GROUP,
            'list' . $sFolderName,
            static::CACHE_TIME
        );

        if (!$oData = $this->cache->get()) {
            $bIsFolderName = $sFolderName !== null;
            $sSelect = $bIsFolderName ? 'enabled' : '*';
            $sSqlWhere = $bIsFolderName ? 'WHERE folderName = :modName LIMIT 1' : '';

            $rStmt = Db::getInstance()->prepare(
                'SELECT ' . $sSelect . ' FROM ' . DB::prefix(DbTableName::SYS_MOD_ENABLED) . $sSqlWhere
            );
            if ($bIsFolderName) {
                $rStmt->bindValue(':modName', $sFolderName, \PDO::PARAM_STR);
            }
            $rStmt->execute();
            $sFetchMethod = $bIsFolderName ? 'fetch' : 'fetchAll';
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
     * @param string $sIsEnabled '1' = Enabled | '0' = Disabled. Need to be string because in DB it is an "enum"
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    public function update($iId, $sIsEnabled = self::YES)
    {
        return $this->orm->update(
            DbTableName::SYS_MOD_ENABLED,
            'enabled',
            $sIsEnabled,
            'moduleId',
            $iId
        );
    }
}
