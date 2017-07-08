<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class ModeratorModel extends ModeratorCoreModel
{

    public function getAlbumsPicture($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, a.* FROM' .
            Db::prefix('AlbumsPictures') . 'AS a INNER JOIN' . Db::prefix('Members') .
            'AS m USING(profileId) WHERE a.approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getPictures($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, p.* FROM' .
            Db::prefix('Pictures') . 'AS p INNER JOIN' . Db::prefix('Members') .
            'AS m USING(profileId) WHERE approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getAlbumsVideo($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, a.* FROM' .
            Db::prefix('AlbumsVideos') . 'AS a INNER JOIN' . Db::prefix('Members') .
            'AS m USING(profileId) WHERE a.approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getVideos($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, v.* FROM' .
            Db::prefix('Videos') . 'AS v INNER JOIN' . Db::prefix('Members') .
            'AS m USING(profileId) WHERE approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getAvatars($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Members') .
            'WHERE approvedAvatar = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getBackgrounds($iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId, m.username, b.* FROM' .
            Db::prefix('MembersBackground') . 'AS b INNER JOIN' . Db::prefix('Members') .
            'AS m USING(profileId) WHERE approved = \'0\' LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function approvedPictureAlbum($iAlbumId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('AlbumsPictures') .
            'SET approved = :status  WHERE albumId = :albumId');
        $rStmt->bindParam(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function approvedPicture($iPictureId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Pictures') .
            'SET approved = :status  WHERE pictureId = :pictureId');
        $rStmt->bindParam(':pictureId', $iPictureId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function approvedVideoAlbum($iAlbumId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('AlbumsVideos') .
            'SET approved = :status  WHERE albumId = :albumId');
        $rStmt->bindParam(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function approvedVideo($iVideoId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Videos') .
            'SET approved = :status  WHERE videoId = :videoId');
        $rStmt->bindParam(':videoId', $iVideoId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function approvedAvatar($iProfileId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Members') .
            'SET approvedAvatar = :status WHERE profileId = :profileId');
        $rStmt->bindParam(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function approvedBackground($iProfileId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('MembersBackground') .
            'SET approved = :status WHERE profileId = :profileId');
        $rStmt->bindParam(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function deletePictureAlbum($iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('AlbumsPictures') .
            'WHERE albumId = :albumId');
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function deleteVideoAlbum($iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('AlbumsVideos') .
            'WHERE albumId = :albumId');
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

}
