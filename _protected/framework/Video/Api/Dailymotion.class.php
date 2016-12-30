<?php
/**
 * @title            Dailymotion Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.0
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video\Api;
defined('PH7') or exit('Restricted access');

class Dailymotion extends Api implements IApi
{

    const
    API_URL = 'https://api.dailymotion.com/video/',
    PLAYER_URL = 'https://www.dailymotion.com/embed/video/';

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) Returns the video embed URL if it was found, FALSE otherwise.
     */
    public function getVideo($sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl
     * @return mixed (object | boolean) FALSE if unable to open the URL, otherwise $this object.
     */
    public function getInfo($sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '?fields=title,duration';
        return ($this->oData = $this->getData($sDataUrl)) ? $this : false;
    }

    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {

        if ($sMedia == 'preview')
        {
            return 'https://dailymotion.com/thumbnail/160x120/video/' . $this->getVideoId($sUrl);
        }
        else
        {
            $sParam = ($this->bAutoplay) ? '?autoPlay=1' : '';
            return '<iframe frameborder="0" width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) . $sParam . '"></iframe>';
        }
    }

    /**
     * @param string $sUrl
     * @return mixed (integer | boolean) Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId($sUrl)
    {
        preg_match('#/video/(\w+)_#i', $sUrl, $aMatch);
        if (!empty($aMatch[1]))
            return $aMatch[1];

        preg_match('#/embed/video/(\w+)#i', $sUrl, $aMatch);
        if (!empty($aMatch[1]))
            return $aMatch[1];

        return false;
    }

}
