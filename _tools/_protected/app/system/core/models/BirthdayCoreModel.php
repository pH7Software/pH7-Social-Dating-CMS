<?php
/**
 * @title          Birthday Core Model
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;

class BirthdayCoreModel
{
    const ALL = 'all';
    const COUPLE = 'couple';
    const MALE = 'male';
    const FEMALE = 'female';

    /**
     * Gets Viewed Profile.
     *
     * @param string $sGender Constant (self::ALL, self::COUPLE, self::MALE, self::FEMALE). Default: self::ALL
     * @param bool $bCount Put TRUE for count birthdays or FALSE for the result of birthdays. Default: TRUE
     * @param string $sOrderBy Default: SearchCoreModel::LAST_ACTIVITY
     * @param int $iSort Default: SearchCoreModel::DESC
     * @param int $iOffset Default: NULL
     * @param int $iLimit Default: NULL
     *
     * @return array|int object for the birthdays list returned or integer for the total number birthdays returned.
     */
    public function get($sGender = self::ALL, $bCount = false, $sOrderBy = SearchCoreModel::LAST_ACTIVITY, $iSort = SearchCoreModel::DESC, $iOffset = null, $iLimit = null)
    {
        $bIsLimit = $iOffset !== null && $iLimit !== null;
        $bIsSex = $sGender !== self::ALL;

        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlLimit = !$bCount && $bIsLimit ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(profileId)';
        $sSqlWhere = $bIsSex ? ' AND (sex = :sex) ' : '';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $sSqlQuery = 'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE (username <> :ghostUsername) AND
            (groupId <> :visitorGroup) AND (groupId <> :pendingGroup) AND (ban = 0) AND (birthDate LIKE :date)' . $sSqlWhere .
            $sSqlOrder . $sSqlLimit;

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':date', '%' . (new CDateTime)->get()->date('-m-d'), PDO::PARAM_STR);

        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        $rStmt->bindValue(':visitorGroup', UserCoreModel::VISITOR_GROUP, PDO::PARAM_INT);
        $rStmt->bindValue(':pendingGroup', UserCoreModel::PENDING_GROUP, PDO::PARAM_INT);

        if ($bIsSex) {
            $rStmt->bindValue(':sex', $sGender, PDO::PARAM_STR);
        }

        if (!$bCount && $bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }
        Db::free($rStmt);

        return $mData;
    }
}
