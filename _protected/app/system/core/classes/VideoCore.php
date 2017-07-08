<?php
/**
 * @title          Video Core Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 * @version        1.0
 */

namespace PH7;

use PH7\Framework\File\File;

class VideoCore
{

    /**
     * Check if this is a url, if so, this is a video from an external site.
     *
     * @param string $sFile
     * @return boolean
     */
    public function isApi($sFile)
    {
        return preg_match('#(^https?://(www\.)?.+\.[a-z]{2,8})#i', $sFile);
    }

    /**
     * @param integer $iAlbumId
     * @param string $sUsername
     * @param string $sVideoLink (file with the extension)
     * @param string $sVideoExt Separate the different extensions with commas. (extension with the point. e.g., .ogg,.webm,.mp4) Default: .webm,.mp4
     * @param string $sThumbExt (extension of thumbnail with the point) Default: .jpg
     * @return void
     */
    public function deleteVideo($iAlbumId, $sUsername, $sVideoLink, $sVideoExt = '.webm,.mp4', $sThumbExt = '.jpg')
    {
        $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $sUsername . PH7_DS . $iAlbumId . PH7_DS;

        $oFile = new File;
        $sThumbName = $oFile->getFileWithoutExt($sVideoLink);

        // Delete video file
        $aVideoExt = explode(',', $sVideoExt);
        foreach ($aVideoExt as $sExt)
            $oFile->deleteFile($sDir . $sVideoLink . $sExt);

        // Delete thumbnail
        $oFile->deleteFile($sDir . $sThumbName . $sThumbExt);
        $oFile->deleteFile($sDir . $sThumbName . '-1' . $sThumbExt);
        $oFile->deleteFile($sDir . $sThumbName . '-2' . $sThumbExt);
        $oFile->deleteFile($sDir . $sThumbName . '-3' . $sThumbExt);
        $oFile->deleteFile($sDir . $sThumbName . '-4' . $sThumbExt);
        unset($oFile);
    }

}
