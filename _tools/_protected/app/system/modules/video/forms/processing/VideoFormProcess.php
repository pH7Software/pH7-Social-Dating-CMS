<?php
/**
 * @title          Video Form Process Class
 * @desc           Class that allows to download the video to the server and save the information about the video in the database.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form / Processing
 * @version        1.1
 *
 * @history        01/13/12 -Removed support Ogg Theora Vorbis | We do not support Ogg more, because now WebM provides a better compression to quality ratio and it is supported in more browsers.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File as F;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;
use PH7\Framework\Video as V;
use PH7\Framework\Video\Api\IApi;

class VideoFormProcess extends Form
{
    const THUMBNAIL_VIDEO_WIDTH = 320;
    const THUMBNAIL_VIDEO_HEIGHT = 240;

    const WEBM_EXT = '.webm';
    const MP4_EXT = '.mp4';

    public function __construct()
    {
        parent::__construct();

        /**
         * This can cause minor errors (eg if a user sent a file that is not a video).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        /**
         * Check if the video album ID is valid. The value must be numeric.
         * This test is necessary because when the selection exists but that no option is available (this can when a user wants to add a video but he has no album)
         * the return value is of type "string" and the value is "1".
         */
        if (!is_numeric($this->httpRequest->post('album_id'))) {
            \PFBC\Form::setError(
                'form_video',
                t('Please add a category before you add a video.')
            );
            return; // Stop execution of the method.
        }

        $sAlbumTitle = MediaCore::cleanTitle($this->httpRequest->post('album_title'));
        $iAlbumId = (int)$this->httpRequest->post('album_id');

        // Default URL Thumbnail
        $sThumb = '';

        $sEmbedUrl = $this->httpRequest->post('embed_code');
        if (!empty($sEmbedUrl)) {
            try {
                $sFile = (new V\Api)->getVideo($sEmbedUrl);

                try {
                    if (!$oInfo = (new V\Api)->getInfo($sEmbedUrl)) {
                        \PFBC\Form::setError(
                            'form_video',
                            t('Unable to retrieve information from the video. Are you sure that the URL of the video is correct?')
                        );
                        return;
                    }
                } catch (Framework\Video\Api\InvalidApiKeyException $oE) {
                    // Problem with the API service from the video platform...? Display the error message.
                    \PFBC\Form::setError('form_video', $oE->getMessage());
                    return;
                }
            } catch (V\InvalidApiProviderException $oE) {
                \PFBC\Form::setError(
                    'form_video',
                    t('Oops! The embed video link looks incorrect? Please make sure that the link is correct.')
                );
                return;
            }

            $sTitle = $this->getApiVideoTitle($oInfo);
            $sDescription = $this->getApiVideoDescription($oInfo);
            $sDuration = ($oInfo->getDuration() ? $oInfo->getDuration() : '0'); // Time in seconds
        } elseif (!empty($_FILES['video']['tmp_name'])) {
            try {
                $oVideo = new V\Video($_FILES['video']);
            } catch (Framework\File\MissingProgramException $oE) {
                \PFBC\Form::setError('form_video', $oE->getMessage());
                return;
            }

            if (!$oVideo->validate()) {
                \PFBC\Form::setError('form_video', Form::wrongVideoFileTypeMsg());
                return;
            } elseif (!$oVideo->check()) {
                \PFBC\Form::setError(
                    'form_video',
                    t('Video file cannot be larger than %0%', F\Various::bytesToSize($oVideo->getMaxSize()))
                );
                return;
            } else {
                $sTitle = $this->getVideoTitle($oVideo);
                $sDescription = $this->httpRequest->post('description');
                $sDuration = $oVideo->getDuration();

                $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $this->session->get('member_username') . PH7_DS . $iAlbumId . PH7_DS;
                $sFileName = Various::genRnd($oVideo->getFileName(), 20);
                $sThumbExt = VideoCore::DEFAULT_THUMBNAIL_EXT;

                $sThumb = $sFileName . $sThumbExt;
                $sThumb1 = $sFileName . '-1' . $sThumbExt;
                $sThumb2 = $sFileName . '-2' . $sThumbExt;
                $sThumb3 = $sFileName . '-3' . $sThumbExt;
                $sThumb4 = $sFileName . '-4' . $sThumbExt;
                $sFile = $sFileName;

                $oVideo->thumbnail(
                    $sPath . $sThumb,
                    1,
                    self::THUMBNAIL_VIDEO_WIDTH,
                    self::THUMBNAIL_VIDEO_HEIGHT
                );
                $oVideo->thumbnail(
                    $sPath . $sThumb1,
                    3,
                    self::THUMBNAIL_VIDEO_WIDTH,
                    self::THUMBNAIL_VIDEO_HEIGHT
                );
                $oVideo->thumbnail(
                    $sPath . $sThumb2,
                    4,
                    self::THUMBNAIL_VIDEO_WIDTH,
                    self::THUMBNAIL_VIDEO_HEIGHT
                );
                $oVideo->thumbnail(
                    $sPath . $sThumb3,
                    6,
                    self::THUMBNAIL_VIDEO_WIDTH,
                    self::THUMBNAIL_VIDEO_HEIGHT
                );
                $oVideo->thumbnail(
                    $sPath . $sThumb4,
                    8,
                    self::THUMBNAIL_VIDEO_WIDTH,
                    self::THUMBNAIL_VIDEO_HEIGHT
                );

                $oVideo->rename($sPath . $sFile . self::WEBM_EXT);
                $oVideo->rename($sPath . $sFile . self::MP4_EXT);
                //$oVideo->save($sPath . $sFile); // Original file type
            }
        } else {
            \PFBC\Form::setError('form_video', t('You have to choose video type.'));
            return;
        }

        $sTitle = MediaCore::cleanTitle($sTitle);
        $sApproved = DbConfig::getSetting('videoManualApproval') == 0 ? '1' : '0';

        (new VideoModel)->addVideo(
            $this->session->get('member_id'),
            $iAlbumId,
            $sTitle,
            $sDescription,
            $sFile,
            $sThumb,
            $sDuration,
            $this->dateTime->get()->dateTime('Y-m-d H:i:s'),
            $sApproved
        );

        Video::clearCache();

        $sModerationText = t('Your video has been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
        $sText = t('Your video has been successfully added!');
        $sMsg = $sApproved === '0' ? $sModerationText : $sText;

        Header::redirect(
            Uri::get(
                'video',
                'main',
                'album',
                $this->session->get('member_username') . ',' . $sAlbumTitle . ',' . $iAlbumId
            ),
            $sMsg
        );
    }

    /**
     * Creates a nice title if no title is specified.
     *
     * @param IApi $oInfo
     *
     * @return string
     */
    private function getApiVideoTitle(IApi $oInfo)
    {
        if ($this->httpRequest->postExists('title') &&
            $this->str->length($this->str->trim($this->httpRequest->post('title'))) > 2
        ) {
            return $this->httpRequest->post('title');
        }

        return $oInfo->getTitle() ? $oInfo->getTitle() : t('Untitled');
    }

    /**
     * @param IApi $oInfo
     *
     * @return string
     */
    private function getApiVideoDescription(IApi $oInfo)
    {
        if ($this->httpRequest->postExists('description')) {
            return $this->httpRequest->post('description');
        }

        return $oInfo->getDescription() ? $oInfo->getDescription() : '';
    }

    /**
     * Creates a nice title if no title is specified.
     *
     * @param V\Video $oVideo
     *
     * @return string
     */
    private function getVideoTitle(V\Video $oVideo)
    {
        if ($this->httpRequest->postExists('title') &&
            $this->str->length($this->str->trim($this->httpRequest->post('title'))) > 2
        ) {
            return $this->httpRequest->post('title');
        }

        return $this->str->upperFirst(
            str_replace(
                ['-', '_'],
                ' ',
                str_ireplace(PH7_DOT . $oVideo->getExt(), '', escape($_FILES['video']['name'], true))
            )
        );
    }
}
