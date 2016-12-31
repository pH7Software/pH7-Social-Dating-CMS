<?php
/**
 * @title          Wall Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / User / Model
 * @version        0.2
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class WallModel extends Framework\Mvc\Model\Engine\Model
{

    public function add($iProfileId, $sPost, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersWall') . '(profileId, post, createdDate) VALUES (:profileId, :post, :createdDate)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':post', $sPost, \PDO::PARAM_STR);
        $rStmt->bindValue(':dateTime', $sCreatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function edit($iProfileId, $sPost, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('MembersWall') . 'SET post = :post, updatedDate = :updatedDate WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':post', $sPost, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function delete($iProfileId, $iWallId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM'.Db::prefix('MembersWall') . 'WHERE :profileId=:profileId AND wallId=:wallId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':wallId', $iWallId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function get($iProfileId, $iWallId = null, $iOffset, $iLimit)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlWallId = (!empty($iWallId)) ? ' AND wallId=:wallId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM'.Db::prefix('MembersWall') . ' AS w LEFT JOIN'.Db::prefix('Members') . 'AS m ON w.profileId = m.profileId WHERE :profileId=:profileId ' . $sSqlWallId . ' ORDER BY dateTime DESC LIMIT :offset, :limit');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        if (!empty($iWallId)) $rStmt->bindValue(':wallId', $iWallId, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    public function getCommentProfile($iProfileId, $iOffset, $iLimit)
    {
        return (new CommentCoreModel)->read($iProfileId, 1, $iOffset, $iLimit, 'profile');
    }

}
