<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class VideoModel extends VideoCoreModel
{
    public function addAlbum($iProfileId, $sTitle, $sDescription, $sThumb, $sCreatedDate, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('AlbumsVideos') . '(profileId, name, description, thumb, createdDate, approved)
            VALUES (:profileId, :name, :description, :thumb, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $sThumb, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function addVideo($iProfileId, $iAlbumId, $sTitle, $sDescription, $sFile, $sThumb, $sDuration, $sCreatedDate, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Videos') . '(profileId, albumId, title, description, file, thumb, duration, createdDate, approved)
            VALUES (:profileId, :albumId, :title, :description, :file, :thumb, :duration, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':thumb', $sThumb, \PDO::PARAM_STR);
        $rStmt->bindValue(':duration', $sDuration, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function deleteAlbum($iProfileId, $iAlbumId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('AlbumsVideos') . 'WHERE profileId=:profileId AND albumId=:albumId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    public function getAlbumsName($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'albumName' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT albumId, name FROM' . Db::prefix('AlbumsVideos') . ' WHERE profileId = :profileId');
            (!empty($iProfileId)) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : '';

            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    public function video($iProfileId, $iAlbumId, $iVideoId = null, $iApproved = 1, $iOffset, $iLimit)
    {
        $this->cache->start(self::CACHE_GROUP, 'video' . $iProfileId . $iAlbumId . $iVideoId . $iApproved . $iOffset . $iLimit, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $iOffset = (int) $iOffset;
            $iLimit = (int) $iLimit;

            $sSqlVideoId = (!empty($iVideoId)) ? ' v.videoId=:videoId AND ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT v.*, a.name, m.username, m.firstName, m.sex FROM' . Db::prefix('Videos') . 'AS v INNER JOIN'
                . Db::prefix('AlbumsVideos') . 'AS a ON v.albumId = a.albumId INNER JOIN' . Db::prefix('Members') .
                'AS m ON v.profileId = m.profileId WHERE v.profileId=:profileId AND v.albumId=:albumId AND' . $sSqlVideoId . 'v.approved=:approved LIMIT :offset, :limit');

            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
            (!empty($iVideoId)) ? $rStmt->bindValue(':videoId', $iVideoId, \PDO::PARAM_INT) : '';
            $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();

            $oData = (!empty($iVideoId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
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
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(albumId) AS totalAlbums FROM' . Db::prefix('AlbumsVideos') . $sSqlProfileId);
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

    public function totalVideos($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'totalVideos' . $iProfileId, static::CACHE_TIME);

        if (!$iData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(videoId) AS totalVideos FROM' . Db::prefix('Videos') . 'WHERE profileId=:profileId');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) $oRow->totalVideos;
            unset($oRow);
            $this->cache->put($iData);
        }
        return $iData;
    }

    public function updateAlbum($iProfileId, $iAlbumId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('AlbumsVideos') . 'SET name =:name, description =:description, updatedDate =:updatedDate
            WHERE profileId=:profileId AND albumId=:albumId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updateVideo($iProfileId, $iAlbumId, $iVideoId, $sTitle, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Videos') . 'SET title =:title, description =:description, updatedDate =:updatedDate
            WHERE profileId=:profileId AND albumId=:albumId AND videoId=:videoId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':videoId', $iVideoId, \PDO::PARAM_INT);
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
        $sSqlSelect = (!$bCount) ? 'v.*' : 'COUNT(v.videoId) AS totalVideos';
        $sSqlWhere = (ctype_digit($mLooking)) ? ' WHERE v.videoId = :looking' : ' WHERE v.title LIKE :looking OR v.description LIKE :looking';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ', a.name, m.username, m.firstName, m.sex FROM' . Db::prefix('Videos') . 'AS v INNER JOIN'
                . Db::prefix('AlbumsVideos') . 'AS a ON v.albumId = a.albumId INNER JOIN' . Db::prefix('Members') . 'AS m ON v.profileId = m.profileId' . $sSqlWhere . ' AND v.approved=:approved' . $sSqlOrder . $sSqlLimit);

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
            $mData = (int) @$oRow->totalVideos;
            unset($oRow);
        }
        return $mData;
    }
}
