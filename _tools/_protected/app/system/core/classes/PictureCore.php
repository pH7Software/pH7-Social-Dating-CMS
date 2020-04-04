<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\File\File;

class PictureCore
{
    /**
     * @param integer $iAlbumId
     * @param string $sUsername
     * @param string $sPictureLink (file with the extension)
     *
     * @return void
     */
    public function deletePhoto($iAlbumId, $sUsername, $sPictureLink)
    {
        $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $sUsername . PH7_DS . $iAlbumId . PH7_DS;

        /** Array to the new format (>= PHP5.4) **/
        $aFiles = [
            $sDir . $sPictureLink, // Original
            $sDir . str_replace('original', '400', $sPictureLink),
            $sDir . str_replace('original', '600', $sPictureLink),
            $sDir . str_replace('original', '800', $sPictureLink),
            $sDir . str_replace('original', '1000', $sPictureLink),
            $sDir . str_replace('original', '1200', $sPictureLink)
        ];

        (new File)->deleteFile($aFiles);
        unset($aFiles);
    }

    public static function clearCache()
    {
        (new Cache)->start(
            PictureCoreModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
