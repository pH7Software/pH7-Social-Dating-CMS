<?php
/**
 * @title            Spam Model Class
 * @desc             To prevent spam.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          0.5
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Security\Spam\Spam as SecMsg;

class Spam
{

    /**
     * Detect duplicate contents.
     *
     * @param string $sCheckContent
     * @param string $sFindColumn
     * @param string $sColumnId
     * @param integer $iFindId
     * @param string $sTable
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public static function detectDuplicate($sCheckContent, $sFindColumn, $sColumnId, $iFindId, $sTable)
    {
        $bReturn = false; // Default value

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sFindColumn . ' AS content FROM ' . Db::prefix($sTable) . 'WHERE ' . $sColumnId . ' = :id');
        $rStmt->bindValue(':id', $iFindId, \PDO::PARAM_INT);
        $rStmt->execute();
        while ($oRow = $rStmt->fetch(\PDO::FETCH_OBJ))
            if ($bReturn = SecMsg::detectDuplicate($sCheckContent, $oRow->content)) break; // TRUE = Duplicate content detected, FALSE otherwise.

        return $bReturn;
    }

}
