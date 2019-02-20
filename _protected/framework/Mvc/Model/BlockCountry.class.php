<?php
/**
 * @title            Security Model Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PDO;
use PH7\DbTableName;
use PH7\Framework\Mvc\Model\Engine\Db;

class BlockCountry extends Engine\Model
{
    const CACHE_GROUP = 'db/sys/core/block_country';
    const CACHE_TIME = 7776000; // 90 days

    /**
     * @param string $sCountryCode
     *
     * @return bool
     */
    public function isBlocked($sCountryCode)
    {
        $this->cache->start(static::CACHE_GROUP, 'lookup' . $sCountryCode, static::CACHE_TIME);

        if (!$bIsBlocked = $this->cache->get()) {
            $sSqlQuery = 'SELECT COUNT(countryCode) FROM' . Db::prefix(DbTableName::BLOCK_COUNTRY) . 'WHERE countryCode = :countryCode LIMIT 1';
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->bindValue(':countryCode', $sCountryCode, PDO::PARAM_STR);
            $rStmt->execute();
            $bIsBlocked = $rStmt->fetchColumn() == 1;
            Db::free($rStmt);
            $this->cache->put($bIsBlocked);
        }

        return $bIsBlocked;
    }

    /**
     * @return array
     */
    public function getBlockedCountries()
    {
        $this->cache->start(static::CACHE_GROUP, 'blockedCountries', static::CACHE_TIME);

        if (!$aBlockedCountries = $this->cache->get()) {
            $sSqlQuery = 'SELECT countryCode FROM' . Db::prefix(DbTableName::BLOCK_COUNTRY);
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->execute();
            $aCountries = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);

            $aBlockedCountries = [];
            foreach ($aCountries as $oCountry) {
                $aBlockedCountries[] = $oCountry->countryCode;
            }
            $this->cache->put($aBlockedCountries);
        }

        return $aBlockedCountries;
    }

    /**
     * @param string $sCountryCode e.g. en, fr, be, ru, nl, ...
     *
     * @return bool|int
     */
    public function add($sCountryCode)
    {
        return $this->orm->insert(DbTableName::BLOCK_COUNTRY, ['countryCode' => $sCountryCode]);
    }

    public function clear()
    {
        $oDb = Db::getInstance();
        $oDb->exec('TRUNCATE' . Db::prefix(DbTableName::BLOCK_COUNTRY));
        unset($oDb);
    }
}
