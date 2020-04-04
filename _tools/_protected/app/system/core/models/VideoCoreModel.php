<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class VideoCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/video';
    const CACHE_TIME = 172800;
    const CREATED = 'createdDate';
    const UPDATED = 'updatedDate';

    /**
     * @param null|int $iProfileId
     * @param null|int $iAlbumId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sOrder
     *
     * @return array|\stdClass
     */
    public function album($iProfileId = null, $iAlbumId = null, $sApproved = '1', $iOffset, $iLimit, $sOrder = self::CREATED)
    {
        $this->cache->start(self::CACHE_GROUP, 'album' . $iProfileId . $iAlbumId . $sApproved . $iOffset . $iLimit . $sOrder, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $bIsProfileId = $iProfileId !== null;
            $bIsAlbumId = $iAlbumId !== null;

            $sSqlProfileId = $bIsProfileId ? ' a.profileId = :profileId AND ' : '';
            $sSqlAlbum = $bIsAlbumId ? ' a.albumId=:albumId AND ' : '';

            $sSqlQuery = 'SELECT a.*, m.username, m.firstName, m.sex FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) .
                'AS a INNER JOIN' . Db::prefix(DbTableName::MEMBER) . 'AS m ON a.profileId = m.profileId WHERE' .
                $sSqlProfileId . $sSqlAlbum . ' a.approved=:approved ORDER BY ' .
                $sOrder . ' DESC LIMIT :offset, :limit';

            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            if ($bIsProfileId) {
                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            }
            if ($bIsAlbumId) {
                $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
            }
            $rStmt->bindValue(':approved', $sApproved, \PDO::PARAM_STR);

            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);

            $rStmt->execute();
            $mData = ($bIsProfileId && $bIsAlbumId) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($mData);
        }

        return $mData;
    }

    /**
     * @param int $iProfileId
     * @param int $iAlbumId
     * @param null|int $iVideoId
     *
     * @return bool
     */
    public function deleteVideo($iProfileId, $iAlbumId, $iVideoId = null)
    {
        $bVideoId = $iVideoId !== null;

        $sSqlVideoId = $bVideoId ? ' AND videoId=:videoId ' : '';
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix(DbTableName::VIDEO) . 'WHERE profileId=:profileId AND albumId=:albumId' . $sSqlVideoId);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        if ($bVideoId) {
            $rStmt->bindValue(':videoId', $iVideoId, \PDO::PARAM_INT);
        }

        return $rStmt->execute();
    }
}
