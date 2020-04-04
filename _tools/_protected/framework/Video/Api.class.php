<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Video;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;

class Api
{
    const REGEX_URL_PATTERN = '#(^https?://|www\.|\.[a-z]{2,4}/?(.+)?$)#i';

    const DEF_VIDEO_WIDTH = 480;
    const DEF_VIDEO_HEIGHT = 295;

    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the video embed URL.
     *
     * @throws InvalidApiProviderException
     */
    public function getVideo($sUrl)
    {
        $sVideoPlatform = $this->getVideoPlatformNameFromUrl($sUrl);
        $oApiProvider = ProviderFactory::create($sVideoPlatform);

        return $oApiProvider->getVideo($sUrl);
    }

    /**
     * @param string $sUrl The video URL.
     *
     * @return Api\IApi|bool The Video API class (e.g. Api\Youtube, Api\Vimeo, ..) or FALSE if the data cannot be retrieved.
     *
     * @throws InvalidApiProviderException
     * @throws Api\InvalidApiKeyException If the YouTube API is invalid.
     */
    public function getInfo($sUrl)
    {
        $sVideoPlatform = $this->getVideoPlatformNameFromUrl($sUrl);
        $oApiProvider = ProviderFactory::create($sVideoPlatform);

        return $oApiProvider->getInfo($sUrl);
    }

    /**
     * @param string $sUrl
     * @param string $sMedia (preview or movie)
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return string The HTML video integration code.
     *
     * @throws InvalidApiProviderException
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {
        $sMedia = isset($sMedia) ? $sMedia : 'movie';
        $iWidth = isset($iWidth) ? $iWidth : self::DEF_VIDEO_WIDTH;
        $iHeight = isset($iHeight) ? $iHeight : self::DEF_VIDEO_HEIGHT;

        $sVideoPlatform = $this->getVideoPlatformNameFromUrl($sUrl);
        $oApiProvider = ProviderFactory::create($sVideoPlatform);

        return $oApiProvider->getMeta($sUrl, $sMedia, $iWidth, $iHeight);
    }

    /**
     * @param string $sUrl The embed URL of the video.
     *
     * @return string The name for the specific video platform.
     */
    private function getVideoPlatformNameFromUrl($sUrl)
    {
        $oHttp = new Http;
        if ($oHttp->detectSubdomain($sUrl)) {
            // Removes the subdomain with its dot (e.g. mysub.domain.com becomes domain.com).
            $sUrl = str_replace($oHttp->getSubdomain($sUrl) . PH7_DOT, '', $sUrl);
        }
        unset($oHttp);

        return preg_replace(static::REGEX_URL_PATTERN, '', $sUrl);
    }
}
