<?php
/**
 * @title            Youtube Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.0
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video\Api;
defined('PH7') or exit('Restricted access');

class Youtube extends Api implements IApi
{

    const
    API_URL = 'http://gdata.youtube.com/feeds/api/videos/',
    PLAYER_URL = 'http://youtube.com/v/';

    public function getVideo($sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    public function getInfo($sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '?v=2&alt=jsonc';
        if ($oData = $this->getData($sDataUrl))
        {
            $this->oData = $oData->data;
            return $this;
        }

        return false;
    }

    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {

        if ($sMedia == 'preview')
        {
            $aThumb = ['default', 1, 2, 3];
            shuffle($aThumb);
            return 'http://i' . mt_rand(1,4) . '.ytimg.com/vi/' . $this->getVideoId($sUrl) . PH7_SH . $aThumb[0] . '.jpg';
        }
        else
        {
            $sParam = ($this->bAutoplay) ? '?autoplay=1' : '';
            return '<iframe width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) .$sParam . '&amp;rel=0" frameborder="0" allowfullscreen></iframe>';
        }
    }

}

