<?php
/**
 * @title          Birthday Core Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.0
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;

class BirthdayCoreModel
{

    const ALL = 'all', COUPLE = 'couple', MALE = 'male', FEMALE = 'female';

    /**
     * Gets Viewed Profile.
     *
     * @param string $sGender Constant (self::ALL, self::COUPLE, self::MALE, self::FEMALE). Default: self::ALL
     * @param boolean $bCount Put TRUE for count birthdays or FALSE for the result of birthdays. Default: TRUE
     * @param string $sOrderBy Default: SearchCoreModel::LAST_ACTIVITY
     * @param integer $iSort Default: SearchCoreModel::DESC
     * @param integer $iOffset Default: NULL
     * @param integer $iLimit Default: NULL
     * @return mixed (object | integer) object for the birthdays list returned or integer for the total number birthdays returned.
     */
    public function get($sGender = self::ALL, $bCount = false, $sOrderBy = SearchCoreModel::LAST_ACTIVITY, $iSort = SearchCoreModel::DESC, $iOffset = null, $iLimit = null)
    {
        $bIsLimit = (null !== $iOffset && null !== $iLimit);
        $bIsSex = ($sGender !== self::ALL);

        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlLimit = (!$bCount && $bIsLimit) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(profileId) AS totalBirths';
        $sSqlWhere = ($bIsSex) ? ' AND (sex = :sex) ' : '';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Members') . 'WHERE (username <> \'' . PH7_GHOST_USERNAME . '\') AND (groupId <> 1) AND (groupId <> 9) AND (birthDate LIKE :date)' . $sSqlWhere . $sSqlOrder . $sSqlLimit);
        $rStmt->bindValue(':date', '%' . (new CDateTime)->get()->date('-m-d'), \PDO::PARAM_STR);
        if ($bIsSex) $rStmt->bindValue(':sex', $sGender, \PDO::PARAM_STR);

        if (!$bCount && $bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int)$oRow->totalBirths;
        }
    }

}
