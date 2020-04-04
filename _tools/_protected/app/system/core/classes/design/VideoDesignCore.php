<?php
/**
 * @title          Video Design Core Class
 * @desc           Class supports the viewing of videos in HTML5.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 * @version        1.3
 *
 * @history        01/13/2013 -Removed support Ogg Theora Vorbis | We do not support Ogg more, because now the WebM format is preferable and is now compatible with almost all browsers.
 * @history        03/29/2013 -Adding "video not found", if the requested video is not found on the server.
 */

namespace PH7;

use PH7\Framework\Date\Various;
use PH7\Framework\File\File;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Pattern\Statik;
use PH7\Framework\Video\Api as VideoApi;
use PH7\Framework\Video\InvalidApiProviderException;

class VideoDesignCore
{
    const PREVIEW_MEDIA_MODE = 'preview';
    const MOVIE_MEDIA_MODE = 'movie';

    const WEBM_EXT = '.webm';
    const MP4_EXT = '.mp4';

    /**
     * @internal Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Generates HTML contents Video.
     *
     * @param \stdClass $oData
     * @param string $sMedia Type of the media ('preview' or 'movie').
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return void
     */
    public static function generate($oData, $sMedia = self::MOVIE_MEDIA_MODE, $iWidth = 600, $iHeight = 400)
    {
        $sDurationTag = '<div class="video_duration">' . Various::secToTime($oData->duration) . '</div>';

        if ((new VideoCore)->isApi($oData->file)) {
            try {
                $oVideo = (new VideoApi)->getMeta($oData->file, $sMedia, $iWidth, $iHeight);

                if ($sMedia === self::PREVIEW_MEDIA_MODE) {
                    echo $sDurationTag, '<a href="', $oData->file, '" title="', $oData->title, '" data-popup="frame-video"><img src="', $oVideo, '" alt="', $oData->title, '" title="', $oData->title, '" /></a>';
                } else {
                    echo $oVideo;
                }
            } catch (InvalidApiProviderException $oE) {
                echo $oE->getMessage();
            }
        } else {
            $sDir = 'video/file/' . $oData->username . PH7_SH . $oData->albumId . PH7_SH;
            $sVidPath1 = $sDir . $oData->file . self::WEBM_EXT;
            $sVidPath2 = $sDir . $oData->file . self::MP4_EXT;

            // If the video is not found on the server, we show a video that shows an appropriate message.
            if (!(is_file(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sVidPath1) && is_file(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sVidPath2))) {
                $sVidPath1 = 'video/not_found.webm';
                $sVidPath2 = 'video/not_found.mp4';
            }

            if (is_file(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sDir . $oData->thumb)) {
                $oFile = new File;
                $sThumbName = $oFile->getFileWithoutExt($oData->thumb);
                $sThumbExt = $oFile->getFileExt($oData->thumb);
                unset($oFile);

                $aThumb = ['', '-1', '-2', '-3', '-4'];
                shuffle($aThumb);
                $sThumbUrl = PH7_URL_DATA_SYS_MOD . $sDir . $sThumbName . $aThumb[0] . PH7_DOT . $sThumbExt;
            } else {
                $sThumbUrl = PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'icon/' . UserDesignCore::NONE_IMG_FILENAME;
            }

            $sParam = self::isAutoplayVideo($sMedia) ? 'autoplay="autoplay"' : '';
            $sVideoTag = '
            <video poster="' . $sThumbUrl . '" width="' . $iWidth . '" height="' . $iHeight . '" controls="controls" ' . $sParam . '>
                <source src="' . PH7_URL_DATA_SYS_MOD . $sVidPath1 . '" type="video/webm" />
                <source src="' . PH7_URL_DATA_SYS_MOD . $sVidPath2 . '" type="video/mp4" />
                ' . t('Your browser is obsolete. Please use a browser that supports HTML5.') . '
            </video>
            <div class="center">
                <button class="bold btn btn-default btn-sm" onclick="Video.playPause()">' . t('Play/Pause') . '</button>
                <button class="btn btn-default btn-sm" onclick="Video.bigSize()">' . t('Big') . '</button>
                <button class="btn btn-default btn-sm" onclick="Video.normalSize()">' . t('Normal') . '</button>
                <button class="btn btn-default btn-sm" onclick="Video.smallSize()">' . t('Small') . '</button>
            </div>';

            if ($sMedia === self::PREVIEW_MEDIA_MODE) {
                echo $sDurationTag, '<a href="#watch', $oData->videoId, '" title="', $oData->title, '" data-popup="video"><img src="', $sThumbUrl, '" alt="', $oData->title, '" title="', $oData->title, '" /></a>
                <div class="hidden"><div id="watch', $oData->videoId, '">', $sVideoTag, '</div></div>';
            } else {
                echo $sVideoTag;
            }
        }
    }

    /**
     * @param string $sMedia
     *
     * @return bool
     */
    private static function isAutoplayVideo($sMedia)
    {
        return $sMedia === self::MOVIE_MEDIA_MODE && DbConfig::getSetting('autoplayVideo');
    }
}
