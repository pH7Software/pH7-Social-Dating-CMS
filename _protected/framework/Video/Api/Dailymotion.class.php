<?php
/**
 * @title            Dailymotion Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @link             http://ph7builder.com
 */

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

class Dailymotion extends Api implements Apible
{
    const API_URL = 'https://api.dailymotion.com/video/';
    const PLAYER_URL = 'https://www.dailymotion.com/embed/video/';
    const REGEX_EMBED_FORMAT1 = '#/video/(\w+)#i';
    const REGEX_EMBED_FORMAT2 = '#/embed/video/(\w+)#i';
    const REGEX_SHARING_FORMAT = '#//dai\.ly/(\w+)#i'; // short sharing URL version

    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the video embed URL if it was found, FALSE otherwise.
     */
    public function getVideo(string $sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl
     *
     * @return Dailymotion|bool FALSE if unable to open the URL, otherwise Dailymotion class.
     */
    public function getInfo(string $sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '?fields=title,duration';

        return $this->oData = $this->getData($sDataUrl) ? $this : false;
    }

    /**
     * @param string $sUrl
     * @param string $sMedia
     * @param int|string $iWidth
     * @param int|string $iHeight
     *
     * @return string
     */
    public function getMeta(string $sUrl, string $sMedia, $iWidth, $iHeight): string
    {
        if ($sMedia === 'preview') {
            return 'https://dailymotion.com/thumbnail/160x120/video/' . $this->getVideoId($sUrl);
        } else {
            $sParam = $this->bAutoplay ? '?autoPlay=1' : '';

            return '<iframe frameborder="0" width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) . $sParam . '"></iframe>';
        }
    }

    /**
     * @param string $sUrl
     *
     * @return int|bool Returns the ID of the video if it was found, FALSE otherwise.
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
