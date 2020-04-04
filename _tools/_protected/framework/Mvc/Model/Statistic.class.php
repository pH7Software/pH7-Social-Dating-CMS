<?php
/**
 * @title            Statistic Model Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class Statistic
{
    /**
     * Set Views Statistics.
     *
     * @param int $iId
     * @param string $sTable
     *
     * @return void
     */
    public static function setView($iId, $sTable)
    {
        $sWhere = Various::convertTableToId($sTable);

        $sSqlQuery = 'UPDATE' . Db::prefix($sTable) . 'SET views = views+1 WHERE ' . $sWhere . ' = :id LIMIT 1';
        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * This method was created to avoid retrieving the column "views" with the general Model of the module,
     * since it uses the cache and therefore cannot retrieve the number of real-time views.
     *
     * @param int $iId
     * @param string $sTable
     *
     * @return int Number of views.
     */
    public static function getView($iId, $sTable)
    {
        $sWhere = Various::convertTableToId($sTable);

        $sSqlQuery = 'SELECT views FROM' . Db::prefix($sTable) . 'WHERE ' . $sWhere . ' = :id LIMIT 1';
        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        $iViews = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iViews;
    }
}
