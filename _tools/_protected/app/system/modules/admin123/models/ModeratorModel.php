<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;

class ModeratorModel extends ModeratorCoreModel
{
    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getAlbumsPicture($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlQuery = 'SELECT m.profileId, m.username, a.* FROM' .
            Db::prefix(DbTableName::ALBUM_PICTURE) . 'AS a INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m USING(profileId) WHERE a.approved = \'0\' LIMIT :offset, :limit';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getPictures($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, p.* FROM' .
            Db::prefix(DbTableName::PICTURE) . 'AS p INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m USING(profileId) WHERE approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getAlbumsVideo($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, a.* FROM' .
            Db::prefix(DbTableName::ALBUM_VIDEO) . 'AS a INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m USING(profileId) WHERE a.approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getVideos($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, v.* FROM' .
            Db::prefix(DbTableName::VIDEO) . 'AS v INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m USING(profileId) WHERE approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getAvatars($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER) .
            'WHERE approvedAvatar = 0 LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getBackgrounds($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, b.* FROM' .
            Db::prefix(DbTableName::MEMBER_BACKGROUND) . 'AS b INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m USING(profileId) WHERE approved = 0 LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iAlbumId
     * @param string $sStatus
     *
     * @return bool
     */
    public function approvedPictureAlbum($iAlbumId, $sStatus = '1')
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::ALBUM_PICTURE) .
            'SET approved = :status  WHERE albumId = :albumId');
        $rStmt->bindParam(':albumId', $iAlbumId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $sStatus, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iPictureId
     * @param string $sStatus
     *
     * @return bool
     */
    public function approvedPicture($iPictureId, $sStatus = '1')
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::PICTURE) .
            'SET approved = :status  WHERE pictureId = :pictureId');
        $rStmt->bindParam(':pictureId', $iPictureId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $sStatus, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iAlbumId
     * @param string $sStatus
     *
     * @return bool
     */
    public function approvedVideoAlbum($iAlbumId, $sStatus = '1')
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::ALBUM_VIDEO) .
            'SET approved = :status  WHERE albumId = :albumId');
        $rStmt->bindParam(':albumId', $iAlbumId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $sStatus, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iVideoId
     * @param string $sStatus
     *
     * @return bool
     */
    public function approvedVideo($iVideoId, $sStatus = '1')
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::VIDEO) .
            'SET approved = :status  WHERE videoId = :videoId');
        $rStmt->bindParam(':videoId', $iVideoId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $sStatus, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iStatus
     *
     * @return bool
     */
    public function approvedAvatar($iProfileId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::MEMBER) .
            'SET approvedAvatar = :status WHERE profileId = :profileId');
        $rStmt->bindParam(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iStatus
     *
     * @return bool
     */
    public function approvedBackground($iProfileId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::MEMBER_BACKGROUND) .
            'SET approved = :status WHERE profileId = :profileId');
        $rStmt->bindParam(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iAlbumId
     *
     * @return bool
     */
    public function deletePictureAlbum($iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::ALBUM_PICTURE) .
            'WHERE albumId = :albumId');
        $rStmt->bindValue(':albumId', $iAlbumId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iAlbumId
     *
     * @return bool
     */
    public function deleteVideoAlbum($iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) .
            'WHERE albumId = :albumId');
        $rStmt->bindValue(':albumId', $iAlbumId, PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
