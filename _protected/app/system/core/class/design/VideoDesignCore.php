<?php
/**
 * @title          Video Design Core Class
 * @desc           Class supports the viewing of videos in HTML5.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 * @version        1.3
 *
 * @history        01/13/2013 -Removed support Ogg Theora Vorbis | We do not support Ogg more, because now the WebM format is preferable and is now compatible with almost all browsers.
 * @history        03/29/2013 -Adding "video not found", if the requested video is not found on the server.
 */
namespace PH7;

use
PH7\Framework\Date\Various,
PH7\Framework\Video\Api,
PH7\Framework\Security\Validate\Validate,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\File\File;

class VideoDesignCore
{

    /**
     * Block constructing.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Generates HTML contents Video.
     *
     * @param object $oData
     * @param string $sMedia Type of the media (preview or movie). Default value is 'movie'.
     * @param integer $iWidth Default 600
     * @param integer $iHeight Default 400
     * @return void
     */
    public static function generate($oData, $sMedia = 'movie', $iWidth = 600, $iHeight = 400)
    {
        $sDurationTag = '<div class="video_duration">' . Various::secToTime($oData->duration) . '</div>';

        if ( (new VideoCore)->isApi($oData->file) )
        {
            $oVideo = (new Api)->getMeta($oData->file, $sMedia, $iWidth, $iHeight);

            if ($sMedia == 'preview')

                echo $sDurationTag, '<a href="', $oData->file, '" title="', $oData->title, '" data-popup="frame-video"><img src="', $oVideo, '" alt="', $oData->title, '" title="', $oData->title, '" /></a>';
            else
                echo $oVideo;
        }
        else
        {
            $sDir = PH7_URL_DATA_SYS_MOD . 'video/file/' . $oData->username . PH7_DS . $oData->albumId . PH7_DS;
            $sVidFullPath1 = $sDir . $oData->file . '.webm';
            $sVidFullPath2 = $sDir . $oData->file . '.mp4';

            // If the video is not found on the server, we show a video that shows an appropriate message.
            if ( !(is_file($sVidFullPath1) && is_file($sVidFullPath2)) )
            {
                $sVidFullPath1 = PH7_URL_DATA_SYS_MOD . 'video/not_found.webm';
                $sVidFullPath2 = PH7_URL_DATA_SYS_MOD . 'video/not_found.mp4';
            }

            if ((new Validate)->url($sDir . $oData->thumb))
            {
                $oFile = new File;
                $sThumbName = $oFile->getFileWithoutExt($oData->thumb);
                $sThumbExt = $oFile->getFileExt($oData->thumb);
                unset($oFile);

                $aThumb = ['', '-1', '-2', '-3', '-4'];
                shuffle($aThumb);
                $sThumb = $sDir . $sThumbName . $aThumb[0] . PH7_DOT . $sThumbExt;
            }
            else
            {
                $sThumb = PH7_URL_TPL . PH7_TPL_NAME . PH7_DS . PH7_IMG . 'icon/none.jpg';
            }

            $sParam = (DbConfig::getSetting('autoplayVideo')) ? 'autoplay="autoplay"' : '';
            $sVideoTag = '
            <video poster="' . $sThumb . '" width="' . $iWidth . '" height="' . $iHeight . '" controls="controls" ' . $sParam . '>
                <source src="' . $sVidFullPath1 . '" type="video/webm" />
                <source src="' . $sVidFullPath2 . '" type="video/mp4" />
                ' . t('Your browser is obsolete. Please use a browser that supports HTML5.') . '
            </video>
            <div class="center">
                <button class="bold" onclick="Video.playPause()">' . t('Play/Pause') . '</button>
                <button onclick="Video.bigSize()">' . t('Big') . '</button>
                <button onclick="Video.normalSize()">' . t('Normal') . '</button>
                <button onclick="Video.smallSize()">' . t('Small') . '</button>
            </div>';

            if ($sMedia == 'preview')
                echo $sDurationTag, '<a href="#watch', $oData->videoId, '" title="', $oData->title, '" data-popup="video"><img src="', $sThumb, '" alt="', $oData->title, '" title="', $oData->title, '" /></a>
                <div class="hidden"><div id="watch', $oData->videoId, '">', $sVideoTag, '</div></div>';
            else
                echo $sVideoTag;
        }

    }

}
