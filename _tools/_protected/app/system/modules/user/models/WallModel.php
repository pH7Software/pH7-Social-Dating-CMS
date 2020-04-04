<?php
/**
 * @title          Wall Model
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / User / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class WallModel extends Model
{
    /**
     * @param int $iProfileId
     * @param string $sPost
     * @param string $sCreatedDate
     *
     * @return bool
     */
    public function add($iProfileId, $sPost, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_WALL) . '(profileId, post, createdDate) VALUES (:profileId, :post, :createdDate)');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':post', $sPost, PDO::PARAM_STR);
        $rStmt->bindValue(':dateTime', $sCreatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param string $sPost
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function edit($iProfileId, $sPost, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::MEMBER_WALL) . 'SET post = :post, updatedDate = :updatedDate WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':post', $sPost, PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iWallId
     *
     * @return bool
     */
    public function delete($iProfileId, $iWallId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::MEMBER_WALL) . 'WHERE :profileId=:profileId AND wallId=:wallId');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':wallId', $iWallId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int|null $iWallId
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function get($iProfileId, $iWallId = null, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlWallId = !empty($iWallId) ? ' AND wallId=:wallId ' : '';
        $sSqlQuery = 'SELECT * FROM' . Db::prefix(DbTableName::MEMBER_WALL) . ' AS w LEFT JOIN' .
            Db::prefix(DbTableName::MEMBER) . 'AS m ON w.profileId = m.profileId WHERE :profileId=:profileId ' .
            $sSqlWallId . ' ORDER BY dateTime DESC LIMIT :offset, :limit';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        if (!empty($iWallId)) {
            $rStmt->bindValue(':wallId', $iWallId, PDO::PARAM_INT);
        }
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();
        $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aRow;
    }

    /**
     * @param int $iProfileId
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getCommentProfile($iProfileId, $iOffset, $iLimit)
    {
        return (new CommentCoreModel)->read(
            $iProfileId,
            '1',
            $iOffset,
            $iLimit,
            'profile'
        );
    }
}
