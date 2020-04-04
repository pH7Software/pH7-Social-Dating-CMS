<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class PictureModel extends PictureCoreModel
{
    /**
     * @param int $iProfileId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sThumb
     * @param string $sCreatedDate
     * @param string $sApproved
     *
     * @return bool
     */
    public function addAlbum($iProfileId, $sTitle, $sDescription, $sThumb, $sCreatedDate, $sApproved = '1')
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::ALBUM_PICTURE) . '(profileId, name, description, thumb, createdDate, approved)
            VALUES (:profileId, :name, :description, :thumb, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $sThumb, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sFile
     * @param string $sCreatedDate
     * @param string $sApproved
     *
     * @return bool
     */
    public function addPhoto($iProfileId, $iAlbumId, $sTitle, $sDescription, $sFile, $sCreatedDate, $sApproved = '1')
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::PICTURE) . '(profileId, albumId, title, description, file, createdDate, approved)
            VALUES (:profileId, :albumId, :title, :description, :file, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     *
     * @return bool
     */
    public function deleteAlbum($iProfileId, $iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::ALBUM_PICTURE) . 'WHERE profileId=:profileId AND albumId=:albumId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     *
     * @return array
     */
    public function getAlbumsName($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'albumName' . $iProfileId, static::CACHE_TIME);

        if (!$aData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT albumId, name FROM' . Db::prefix(DbTableName::ALBUM_PICTURE) . ' WHERE profileId = :profileId');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $aData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aData);
        }

        return $aData;
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     * @param int|null $iPictureId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array|\stdClass
     */
    public function photo($iProfileId, $iAlbumId, $iPictureId = null, $sApproved = '1', $iOffset, $iLimit)
    {
        $this->cache->start(self::CACHE_GROUP, 'photo' . $iProfileId . $iAlbumId . $iPictureId . $sApproved . $iOffset . $iLimit, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sSqlPictureId = !empty($iPictureId) ? ' p.pictureId=:pictureId AND ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT p.*, a.name, m.username, m.firstName, m.sex FROM' . Db::prefix(DbTableName::PICTURE) . 'AS p INNER JOIN' .
                Db::prefix(DbTableName::ALBUM_PICTURE) . 'AS a ON p.albumId = a.albumId INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
                'AS m ON p.profileId = m.profileId WHERE p.profileId=:profileId AND p.albumId=:albumId AND' . $sSqlPictureId . 'p.approved=:approved LIMIT :offset, :limit');

            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
            if (!empty($iPictureId)) {
                $rStmt->bindValue(':pictureId', $iPictureId, \PDO::PARAM_INT);
            }
            $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();

            $mData = !empty($iPictureId) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($mData);
        }

        return $mData;
    }

    /**
     * @param int|null $iProfileId
     *
     * @return int
     */
    public function totalAlbums($iProfileId = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalAlbums' . $iProfileId, static::CACHE_TIME);

        if (!$iTotalAlbums = $this->cache->get()) {
            $sSqlProfileId = $iProfileId !== null ? ' WHERE profileId=:profileId' : '';

            $rStmt = Db::getInstance()->prepare('SELECT COUNT(albumId) FROM' . Db::prefix(DbTableName::ALBUM_PICTURE) . $sSqlProfileId);
            if ($iProfileId !== null) {
                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            }
            $rStmt->execute();
            $iTotalAlbums = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalAlbums);
        }

        return $iTotalAlbums;
    }

    /**
     * @param int $iProfileId
     *
     * @return int
     */
    public function totalPhotos($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalPhotos' . $iProfileId, static::CACHE_TIME);

        if (!$iTotalPhotos = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(pictureId) FROM' . Db::prefix(DbTableName::PICTURE) . 'WHERE profileId = :profileId');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $iTotalPhotos = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalPhotos);
        }

        return $iTotalPhotos;
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updateAlbum($iProfileId, $iAlbumId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::ALBUM_PICTURE) .
            'SET name =:name, description =:description, updatedDate =:updatedDate WHERE profileId=:profileId AND albumId=:albumId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     * @param int $iPictureId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updatePhoto($iProfileId, $iAlbumId, $iPictureId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::PICTURE) .
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
     * @param int|string $mLooking
     * @param bool $bCount
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sApproved
     *
     * @return int|\stdClass
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit, $sApproved = '1')
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'p');

        $sSqlSelect = !$bCount ? 'p.*, a.name, m.username, m.firstName, m.sex' : 'COUNT(p.pictureId)';
        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlWhere = $bDigitSearch ? ' WHERE p.pictureId = :looking' : ' WHERE p.title LIKE :looking OR p.description LIKE :looking';

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::PICTURE) . 'AS p INNER JOIN' .
            Db::prefix(DbTableName::ALBUM_PICTURE) . 'AS a ON p.albumId = a.albumId INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON p.profileId = m.profileId' . $sSqlWhere . ' AND p.approved = :approved' . $sSqlOrder . $sSqlLimit);

        if ($bDigitSearch) {
            $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);
        }
        $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }

        Db::free($rStmt);

        return $mData;
    }
}
