<?php

/**
 * @title            Vimeo Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @see             http://ph7builder.com
 */

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

class Vimeo extends Api implements Apible
{
    public const API_URL = 'https://vimeo.com/api/v2/video/';
    public const PLAYER_URL = 'https://player.vimeo.com/video/';
    public const REGEX_VIDEO_ID = '#/(\d+)($|/)#i';

    /**
     * @return string|bool returns the video embed URL if it was found and is valid, FALSE otherwise
     */
    public function getVideo(string $sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @return Vimeo|bool FALSE if unable to open the url, otherwise Vimeo class
     */
    public function getInfo(string $sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '.json';

        $aData = $this->getData($sDataUrl);
        if (
            is_array($aData)
            && isset($aData[0])
            && is_object($aData[0])
            && self::isValidTextValue($aData[0]->title ?? null)
            && self::isValidDurationValue($aData[0]->duration ?? null)
        ) {
            $this->oData = $aData[0];

            return $this;
        }

        return false;
    }

    /**
     * @param int|string $iWidth
     * @param int|string $iHeight
     */
    public function getMeta(string $sUrl, string $sMedia, $iWidth, $iHeight): string
    {
        if ($sMedia === 'preview') {
            // First load the video information.
            if ($this->getInfo($sUrl) === false) {
                return '';
            }

            // Then retrieve the thumbnail.
            $mThumbnail = $this->oData->thumbnail_medium ?? null;

            return is_string($mThumbnail) ? trim($mThumbnail) : '';
        }
        $sParam = $this->bAutoplay ? '?autoplay=1&amp;' : '?';

        return '<iframe src="' . $this->getEmbedUrl($sUrl) . $sParam . 'title=0&amp;byline=0&amp;portrait=0" width="' . $iWidth . '" height="' . $iHeight . '" frameborder="0"></iframe>';
    }

    /**
     * @return int|bool returns the ID of the video if it was found, FALSE otherwise
     */
    public function getVideoId(string $sUrl)
    {
        preg_match(static::REGEX_VIDEO_ID, $sUrl, $aMatch);

        return !empty($aMatch[1]) ? $aMatch[1] : false;
    }
}
