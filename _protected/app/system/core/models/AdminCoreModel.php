<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

// Abstract Class
class AdminCoreModel extends UserCoreModel
{
    const CACHE_GROUP = 'db/sys/mod/admin';
    const CACHE_TIME = 10368000;

    /**
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sTable
     *
     * @return array
     */
    public function browse($iOffset, $iLimit, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlQuery = 'SELECT m.*, g.name AS membershipName FROM' . Db::prefix($sTable) .
            'AS m INNER JOIN ' . Db::prefix(DbTableName::MEMBERSHIP) .
            'AS g ON m.groupId = g.groupId LEFT JOIN' . Db::prefix(DbTableName::MEMBER_INFO) .
            'AS i ON m.profileId = i.profileId WHERE username <> \'' . PH7_GHOST_USERNAME . '\' ORDER BY joinDate DESC LIMIT :offset, :limit';
        if ($sTable !== DbTableName::MEMBER) {
            // Redefine query if it isn't the "members" table
            $sSqlQuery = 'SELECT * FROM' . Db::prefix($sTable) .
                'WHERE username <> \'' . PH7_GHOST_USERNAME . '\' ORDER BY joinDate DESC LIMIT :offset, :limit';
        }

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int|string $mWhat
     * @param string $sWhere
     * @param int $iGroupId
     * @param int $iBanned
     * @param bool $bCount
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array
     */
    public function searchUser($mWhat, $sWhere, $iGroupId, $iBanned, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mWhat = trim($mWhat);

        $sSqlLimit = !$bCount ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? 'm.*, g.name AS membershipName' : 'COUNT(m.profileId)';

        $sSqlConditions = !empty($iBanned) ? '(ban = 1) AND ' : '';
        if ($sWhere === 'all') {
            $sSqlConditions .= '(m.username LIKE :what OR m.email LIKE :what OR m.firstName LIKE :what OR m.lastName LIKE :what OR m.ip LIKE :what)';
        } else {
            $sSqlConditions .= '(m.' . $sWhere . ' LIKE :what)';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $sSqlQuery = 'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m INNER JOIN ' . Db::prefix(DbTableName::MEMBERSHIP) . 'AS g ON m.groupId = g.groupId LEFT JOIN' . Db::prefix(DbTableName::MEMBER_INFO) . 'AS i ON m.profileId = i.profileId WHERE (username <> \'' . PH7_GHOST_USERNAME . '\') AND (m.groupId = :groupId) AND ' . $sSqlConditions . $sSqlOrder . $sSqlLimit;
        $rStmt = Db::getInstance()->prepare($sSqlQuery);

        $rStmt->bindValue(':what', '%' . $mWhat . '%', PDO::PARAM_STR);
        $rStmt->bindParam(':groupId', $iGroupId, PDO::PARAM_INT);

        if (!$bCount) {
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

    /**
     * @param int $iProfileId
     * @param int $iBan
     * @param string $sTable
     *
     * @return bool
     */
    public function ban($iProfileId, $iBan, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $iProfileId = (int)$iProfileId;
        $iBan = (int)$iBan;

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET ban = :ban WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':ban', $iBan, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Get the Root Admin IP address.
     *
     * @return string
     */
    public function getRootIp()
    {
        $this->cache->start(self::CACHE_GROUP, 'rootip', static::CACHE_TIME);

        if (!$sIp = $this->cache->get()) {
            $sIp = $this->orm->getOne(DbTableName::ADMIN, 'profileId', 1, 'ip')->ip;
            $this->cache->put($sIp);
        }

        return $sIp;
    }
}
