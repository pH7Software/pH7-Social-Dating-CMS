<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class VideoModel extends VideoCoreModel
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
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::ALBUM_VIDEO) . '(profileId, name, description, thumb, createdDate, approved)
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
     * @param string $sThumb
     * @param string $sDuration
     * @param string $sCreatedDate
     * @param string $sApproved
     *
     * @return bool
     */
    public function addVideo($iProfileId, $iAlbumId, $sTitle, $sDescription, $sFile, $sThumb, $sDuration, $sCreatedDate, $sApproved = '1')
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::VIDEO) . '(profileId, albumId, title, description, file, thumb, duration, createdDate, approved)
            VALUES (:profileId, :albumId, :title, :description, :file, :thumb, :duration, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $sThumb, \PDO::PARAM_STR);
        $rStmt->bindValue(':duration', $sDuration, \PDO::PARAM_STR);
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
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'WHERE profileId = :profileId AND albumId = :albumId');
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
            $rStmt = Db::getInstance()->prepare('SELECT albumId, name FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) . ' WHERE profileId = :profileId');
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
     * @param int|null $iVideoId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array|\stdClass
     */
    public function video($iProfileId, $iAlbumId, $iVideoId = null, $sApproved = '1', $iOffset, $iLimit)
    {
        $this->cache->start(self::CACHE_GROUP, 'video' . $iProfileId . $iAlbumId . $iVideoId . $sApproved . $iOffset . $iLimit, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sSqlVideoId = !empty($iVideoId) ? ' v.videoId = :videoId AND ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT v.*, a.name, m.username, m.firstName, m.sex FROM' . Db::prefix(DbTableName::VIDEO) . 'AS v INNER JOIN'
                . Db::prefix(DbTableName::ALBUM_VIDEO) . 'AS a ON v.albumId = a.albumId INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
                'AS m ON v.profileId = m.profileId WHERE v.profileId = :profileId AND v.albumId = :albumId AND' . $sSqlVideoId . 'v.approved = :approved LIMIT :offset, :limit');

            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
            if (!empty($iVideoId)) {
                $rStmt->bindValue(':videoId', $iVideoId, \PDO::PARAM_INT);
            }
            $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();

            $mData = !empty($iVideoId) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
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
            $sSqlProfileId = $iProfileId !== null ? ' WHERE profileId = :profileId' : '';
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(albumId) FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) . $sSqlProfileId);
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
    public function totalVideos($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalVideos' . $iProfileId, static::CACHE_TIME);

        if (!$iTotalVideos = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(videoId) FROM' . Db::prefix(DbTableName::VIDEO) . 'WHERE profileId = :profileId');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $iTotalVideos = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iTotalVideos);
        }

        return $iTotalVideos;
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
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'SET name = :name, description = :description, updatedDate = :updatedDate
            WHERE profileId = :profileId AND albumId = :albumId');

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
     * @param int $iVideoId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updateVideo($iProfileId, $iAlbumId, $iVideoId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::VIDEO) . 'SET title = :title, description = :description, updatedDate = :updatedDate
            WHERE profileId = :profileId AND albumId = :albumId AND videoId = :videoId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':videoId', $iVideoId, \PDO::PARAM_INT);
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

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'v');

        $sSqlSelect = !$bCount ? 'v.*, a.name, m.username, m.firstName, m.sex' : 'COUNT(v.videoId)';
        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlWhere = $bDigitSearch ? ' WHERE v.videoId = :looking' : ' WHERE v.title LIKE :looking OR v.description LIKE :looking';

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::VIDEO) . 'AS v INNER JOIN'
            . Db::prefix(DbTableName::ALBUM_VIDEO) . 'AS a ON v.albumId = a.albumId INNER JOIN' . Db::prefix(DbTableName::MEMBER) .
            'AS m ON v.profileId = m.profileId' . $sSqlWhere . ' AND v.approved = :approved' . $sSqlOrder . $sSqlLimit
        );

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
