<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class PictureCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const
    CACHE_GROUP = 'db/sys/mod/picture',
    CACHE_TIME = 172800,
    CREATED = 'createdDate',
    UPDATED = 'updatedDate';

    public function album($iProfileId = null, $iAlbumId = null, $iApproved = 1, $iOffset, $iLimit, $sOrder = self::CREATED)
    {
        $this->cache->start(self::CACHE_GROUP, 'album' . $iProfileId . $iAlbumId . $iApproved . $iOffset . $iLimit . $sOrder, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $iOffset = (int) $iOffset;
            $iLimit = (int) $iLimit;

            $sSqlProfileId = (!empty($iProfileId)) ? ' a.profileId = :profileId AND ' : '';
            $sSqlAlbum  = (!empty($iAlbumId)) ? ' a.albumId=:albumId AND ' : '';
            $rStmt = Db::getInstance()->prepare('SELECT a.*, m.username, m.firstName, m.sex FROM' . Db::prefix('AlbumsPictures') . 'AS a INNER JOIN' . Db::prefix('Members') . 'AS m ON a.profileId = m.profileId WHERE' . $sSqlProfileId . $sSqlAlbum . ' a.approved=:approved ORDER BY ' . $sOrder . ' DESC LIMIT :offset, :limit');
            (!empty($iProfileId)) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : '';
            (!empty($iAlbumId)) ? $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT) : '';
            $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);

            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);

            $rStmt->execute();
            $oData = (isset($iProfileId, $iAlbumId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }
        return $oData;
    }

    public function deletePhoto($iProfileId, $iAlbumId, $iPictureId = null)
    {
        $sSqlPictureId = (!empty($iPictureId)) ? ' AND pictureId=:pictureId ' : '';
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Pictures') . 'WHERE profileId=:profileId AND albumId=:albumId' . $sSqlPictureId);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':albumId', $iAlbumId, \PDO::PARAM_INT);
        if (!empty($iPictureId))
            $rStmt->bindValue(':pictureId', $iPictureId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

}
