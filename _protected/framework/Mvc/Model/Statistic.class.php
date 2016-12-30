<?php
/**
 * @title            Statistic Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db, PH7\Framework\Mvc\Model\Engine\Util\Various;

class Statistic
{

    /**
     * Set Views Statistics.
     *
     * @param integer $iId
     * @param string $sTable
     * @return void
     */
    public static function setView($iId, $sTable)
    {
        $sWhere = Various::convertTableToId($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET views = views+1 WHERE ' . $sWhere . ' = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * This method was created to avoid retrieving the column "views" with the general Model of the module,
     * since it uses the cache and therefore cannot retrieve the number of real-time views.
     *
     * @param integer $iId
     * @param string $sTable
     * @return integer Number of views.
     */
    public static function getView($iId, $sTable)
    {
        $sWhere = Various::convertTableToId($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT views FROM' . Db::prefix($sTable) . 'WHERE ' . $sWhere . ' = :id LIMIT 1');
        $rStmt->bindValue(':id', $iId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int) @$oRow->views;
    }

}
