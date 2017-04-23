<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class PictureModel extends PictureCoreModel
{
    public function addAlbum($iProfileId, $sTitle, $sDescription, $sThumb, $sCreatedDate, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('AlbumsPictures') . '(profileId, name, description, thumb, createdDate, approved)
            VALUES (:profileId, :name, :description, :thumb, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $sThumb, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function addPhoto($iProfileId, $iAlbumId, $sTitle, $sDescription, $sFile, $sCreatedDate, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Pictures') . '(profileId, albumId, title, description, file, createdDate, approved)
            VALUES (:profileId, :albumId, :title, :description, :file, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function deleteAlbum($iProfileId, $iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('AlbumsPictures') . 'WHERE profileId=:profileId AND albumId=:albumId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function getAlbumsName($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'albumName' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT albumId, name FROM' . Db::prefix('AlbumsPictures') . ' WHERE profileId = :profileId');
            (!empty($iProfileId)) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : '';

            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    public function photo($iProfileId, $iAlbumId, $iPictureId = null, $iApproved = 1, $iOffset, $iLimit)
    {
        $this->cache->start(self::CACHE_GROUP, 'photo' . $iProfileId . $iAlbumId . $iPictureId . $iApproved . $iOffset . $iLimit, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $iOffset = (int) $iOffset;
            $iLimit = (int) $iLimit;

            $sSqlPictureId = (!empty($iPictureId)) ? ' p.pictureId=:pictureId AND ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT p.*, a.name, m.username, m.firstName, m.sex FROM' . Db::prefix('Pictures') . 'AS p INNER JOIN' .
                Db::prefix('AlbumsPictures') . 'AS a ON p.albumId = a.albumId INNER JOIN' . Db::prefix('Members') .
                'AS m ON p.profileId = m.profileId WHERE p.profileId=:profileId AND p.albumId=:albumId AND' . $sSqlPictureId . 'p.approved=:approved LIMIT :offset, :limit');

            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
            (!empty($iPictureId)) ? $rStmt->bindValue(':pictureId', $iPictureId, \PDO::PARAM_INT) : '';
            $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();

            $oData = (!empty($iPictureId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    public function totalAlbums($iProfileId = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalAlbums' . $iProfileId, static::CACHE_TIME);

        if (!$iData = $this->cache->get())
        {
            $sSqlProfileId = (!empty($iProfileId)) ? ' WHERE profileId=:profileId' : '';
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(albumId) AS totalAlbums FROM' . Db::prefix('AlbumsPictures') . $sSqlProfileId);
            (!empty($iProfileId)) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : '';
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) $oRow->totalAlbums;
            unset($oRow);
            $this->cache->put($iData);
        }
        return $iData;
    }

    public function totalPhotos($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPhotos' . $iProfileId, static::CACHE_TIME);

        if (!$iData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(pictureId) AS totalPhotos FROM' . Db::prefix('Pictures') . 'WHERE profileId=:profileId');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) $oRow->totalPhotos;
            unset($oRow);
            $this->cache->put($iData);
        }
        return $iData;
    }

    public function updateAlbum($iProfileId, $iAlbumId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('AlbumsPictures') .
            'SET name =:name, description =:description, updatedDate =:updatedDate WHERE profileId=:profileId AND albumId=:albumId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updatePhoto($iProfileId, $iAlbumId, $iPictureId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Pictures') .
            'SET title =:title, description =:description, updatedDate =:updatedDate WHERE profileId=:profileId AND albumId=:albumId AND pictureId=:pictureId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':pictureId', $iPictureId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * @param integer|string $mLooking
     * @param boolean $bCount
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @param integer $iApproved
     *
     * @return integer|object
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit, $iApproved = 1)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;
        $mLooking = trim($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? 'p.*' : 'COUNT(p.pictureId) AS totalPictures';
        $sSqlWhere = (ctype_digit($mLooking)) ? ' WHERE p.pictureId = :looking' : ' WHERE p.title LIKE :looking OR p.description LIKE :looking';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ', a.name, m.username, m.firstName, m.sex FROM' . Db::prefix('Pictures') . 'AS p INNER JOIN' .
            Db::prefix('AlbumsPictures') . 'AS a ON p.albumId = a.albumId INNER JOIN' . Db::prefix('Members') .
            'AS m ON p.profileId = m.profileId' . $sSqlWhere . ' AND p.approved=:approved' . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int) @$oRow->totalPictures;
            unset($oRow);
        }
        return $mData;
    }
}
