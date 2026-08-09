<?php

/**
 * @title            Dailymotion Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @see             http://ph7builder.com
 */

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

class Dailymotion extends Api implements Apible
{
    public const API_URL = 'https://api.dailymotion.com/video/';
    public const PLAYER_URL = 'https://www.dailymotion.com/embed/video/';
    public const REGEX_EMBED_FORMAT1 = '#/video/(\w+)#i';
    public const REGEX_EMBED_FORMAT2 = '#/embed/video/(\w+)#i';
    public const REGEX_SHARING_FORMAT = '#//dai\.ly/(\w+)#i'; // short sharing URL version

    /**
     * @return string|bool returns the video embed URL if it was found, FALSE otherwise
     */
    public function getVideo(string $sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @return Dailymotion|bool FALSE if unable to open the URL, otherwise Dailymotion class
     */
    public function getInfo(string $sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '?fields=title,duration';
        $oData = $this->getData($sDataUrl);
        if (
            !is_object($oData)
            || !self::isValidTextValue($oData->title ?? null)
            || !self::isValidDurationValue($oData->duration ?? null)
        ) {
            return false;
        }

        $this->oData = $oData;

        return $this;
    }

    /**
     * @param int|string $iWidth
     * @param int|string $iHeight
     */
    public function getMeta(string $sUrl, string $sMedia, $iWidth, $iHeight): string
    {
        if ($sMedia === 'preview') {
            return 'https://dailymotion.com/thumbnail/160x120/video/' . $this->getVideoId($sUrl);
        }
        $sParam = $this->bAutoplay ? '?autoPlay=1' : '';

        return '<iframe frameborder="0" width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) . $sParam . '"></iframe>';
    }

    /**
     * @return int|bool returns the ID of the video if it was found, FALSE otherwise
     */
    public function getVideoId(string $sUrl)
    {
        preg_match(static::REGEX_EMBED_FORMAT1, $sUrl, $aMatch);
        if (!empty($aMatch[1])) {
            return $aMatch[1];
        }

        preg_match(static::REGEX_EMBED_FORMAT2, $sUrl, $aMatch);
        if (!empty($aMatch[1])) {
            return $aMatch[1];
        }

        preg_match(static::REGEX_SHARING_FORMAT, $sUrl, $aMatch);
        if (!empty($aMatch[1])) {
            return $aMatch[1];
        }

        return false;
    }
}
