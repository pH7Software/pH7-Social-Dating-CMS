<?php
/**
 * @title            Advertisement Model Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PH7\DbTableName;
use PH7\Framework\Mvc\Model\Engine\Db;

class Ads extends Engine\Model
{
    /**
     * Adding an Advertisement Click.
     *
     * @param int $iAdsId
     *
     * @return void
     */
    public static function setClick($iAdsId)
    {
        $sSql = 'UPDATE' . Db::prefix(DbTableName::AD) . 'SET clicks = clicks+1 WHERE adsId = :id LIMIT 1';
        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindValue(':id', $iAdsId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }
}
