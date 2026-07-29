<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\File\File;

class PictureCore
{
    public function deleteAlbum($iAlbumId, $sUsername, File $oFile): bool
    {
        $sUsername = (string)$sUsername;
        $sSafeUsername = File::getFileBasename($sUsername);
        $iAlbumId = (int)$iAlbumId;

        if ($sSafeUsername === '' || $sSafeUsername !== $sUsername || $iAlbumId < 1) {
            return false;
        }

        $sAlbumPath = PH7_PATH_PUBLIC_DATA_SYS_MOD .
            'picture/img/' . $sSafeUsername . PH7_DS . $iAlbumId . PH7_DS;

        return $oFile->deleteDir($sAlbumPath);
    }

    /**
     * @param int    $iAlbumId
     * @param string $sUsername
     * @param string $sPictureLink (file with the extension)
     *
     * @return void
     */
    public function deletePhoto($iAlbumId, $sUsername, $sPictureLink)
    {
        $sUsername = (string)$sUsername;
        $sSafeUsername = File::getFileBasename($sUsername);
        $sPictureLink = (string)$sPictureLink;
        $sSafePictureLink = File::getFileBasename($sPictureLink);
        $iAlbumId = (int)$iAlbumId;

        if (
            $sSafeUsername === ''
            || $sSafeUsername !== $sUsername
            || $sSafePictureLink === ''
            || $sSafePictureLink !== $sPictureLink
            || $iAlbumId < 1
        ) {
            return;
        }

        $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $sSafeUsername . PH7_DS . $iAlbumId . PH7_DS;

        /** Array to the new format (>= PHP5.4) **/
        $aFiles = [
            $sDir . $sSafePictureLink, // Original
            $sDir . str_replace('original', '400', $sSafePictureLink),
            $sDir . str_replace('original', '600', $sSafePictureLink),
            $sDir . str_replace('original', '800', $sSafePictureLink),
            $sDir . str_replace('original', '1000', $sSafePictureLink),
            $sDir . str_replace('original', '1200', $sSafePictureLink)
        ];

        (new File())->deleteFile($aFiles);
        unset($aFiles);
    }

    public static function clearCache()
    {
        (new Cache())->start(
            PictureCoreModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
