<?php
/**
 * @title            Advertisement Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db;

class Ads extends Engine\Model
{

    /**
     * Adding an Advertisement Click.
     *
     * @param integer $iAdsId
     * @return void
     */
    public static function setClick($iAdsId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Ads') . 'SET clicks = clicks+1 WHERE adsId = :id LIMIT 1');
        $rStmt->bindValue(':id', $iAdsId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

}
