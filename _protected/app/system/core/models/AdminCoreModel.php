<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db, PH7\Framework\Mvc\Model\Engine\Util\Various;

// Abstract Class
class AdminCoreModel extends UserCoreModel
{

    public function browse($iOffset, $iLimit, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSql = ($sTable != 'Members')
            ? 'SELECT * FROM'.Db::prefix($sTable). 'WHERE username <> \'' . PH7_GHOST_USERNAME . '\' ORDER BY joinDate DESC LIMIT :offset, :limit'
            : 'SELECT m.*, g.name AS membershipName FROM' . Db::prefix($sTable). 'AS m INNER JOIN ' . Db::prefix('Memberships') . 'AS g ON m.groupId = g.groupId WHERE username <> \'' . PH7_GHOST_USERNAME . '\' ORDER BY joinDate DESC LIMIT :offset, :limit';

        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function searchUser($mWhat, $sWhere, $iGroupId, $iBanned, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlLimit = (!$bCount) ?  ' LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(profileId) AS totalUsers';

        $sSqlQuery = (!empty($iBanned)) ? '(ban = 1) AND ' : '';
        $sSqlQuery .= ($sWhere === 'all') ? '(username LIKE :what OR email LIKE :what OR firstName LIKE :what OR lastName LIKE :what OR ip LIKE :what)' : $sWhere . ' LIKE :what';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Members') . 'WHERE (username <> \'' . PH7_GHOST_USERNAME . '\') AND (groupId = :groupId) AND ' . $sSqlQuery . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':what', '%' . $mWhat . '%', \PDO::PARAM_STR);
        $rStmt->bindParam(':groupId', $iGroupId, \PDO::PARAM_INT);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int) $oRow->totalUsers;
        }
    }

    public function ban($iProfileId, $iBan, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $iProfileId = (int) $iProfileId;
        $iBan = (int) $iBan;

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET ban = :ban WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':ban', $iBan, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

}
