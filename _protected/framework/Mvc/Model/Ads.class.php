<?php
/**
 * @title            Advertisement Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013, Pierre-Henry Soria. All Rights Reserved.
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
     * @param string $sLink
     * @return void
     */
    public static function setClick($iAdsId, $sLink)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('AdsClicks') . 'SET adsId = :adsId, url = :url, ip = :ip, dateTime = :dateTime');
        $rStmt->bindValue(':adsId', $iAdsId, \PDO::PARAM_INT);
        $rStmt->bindValue(':url', $sLink, \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', \PH7\Framework\Ip\Ip::get(), \PDO::PARAM_STR);
        $rStmt->bindValue(':dateTime', (new \PH7\Framework\Date\CDateTime)->get()->dateTime('Y-m-d H:i:s'), \PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

}
