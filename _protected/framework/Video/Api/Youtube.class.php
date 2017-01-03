<?php
/**
 * @title            Youtube Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.2
 * @link             http://hizup.com
 * @history          28/03/2016 - Since pH7CMS 1.3.7, it's now compatible with Youtube API v3. Since Youtube API v3, it requires a Google API key. This is available through pH7CMS's admin panel.
 */

namespace PH7\Framework\Video\Api;
defined('PH7') or exit('Restricted access');

class Youtube extends Api implements IApi
{

    const
    API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=',
    PLAYER_URL = 'https://youtube.com/v/';

    private $_oContentDetails;

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) Returns the embed video URL if found, FALSE otherwise.
     */
    public function getVideo($sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl URL video (e.g., https://www.youtube.com/watch?v=q-1eHnBOg4A).
     * @return mixed (object | boolean) FALSE if unable to open the API URL, otherwise $this object.
     * @throws \PH7\Framework\Video\Api\Exception If the is a problem with Youtube API service.
     */
    public function getInfo($sUrl)
    {
        $sDataUrl = static::API_URL . $this->getVideoId($sUrl) . '&key=' . $this->sApiKey . '&part=snippet,contentDetails,statistics,status';

        if ($oData = $this->getData($sDataUrl))
        {
            // Use Youtube's API to get the Youtube video's data only if the API key has been set, otherwise it won't work
            if (!empty($this->sApiKey) && strlen($this->sApiKey) > 10)
            {
                if (!empty($oData->error->errors[0]->message))
                {
                    throw new Exception('YouTube API: ' . $oData->error->errors[0]->message);
                }
                else
                {
                    $this->oData = $oData->items[0]->snippet;
                    $this->_oContentDetails = $oData->items[0]->contentDetails; // Need only for getting the video duration
                }
            }
            return $this;
        }

        return false;
    }

    /**
     * Redefine this method to the specific needs of Youtube API.
     *
     * @see \PH7\Framework\Video\Api\Youtube::getInfo();
     * @return mixed (integer | boolean) The video duration if found, FALSE otherwise.
     */
    public function getDuration()
    {
        return $this->getDurationTime($this->_oContentDetails->duration);
    }

    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {

        if ($sMedia == 'preview')
        {
            $aThumb = ['default', 1, 2, 3];
            shuffle($aThumb);
            return 'https://i' . mt_rand(1,4) . '.ytimg.com/vi/' . $this->getVideoId($sUrl) . PH7_SH . $aThumb[0] . '.jpg';
        }
        else
        {
            $sParam = ($this->bAutoplay) ? '?autoplay=1&amp;' : '?';
            return '<iframe width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) . $sParam . 'rel=0" frameborder="0" allowfullscreen></iframe>';
        }
    }

    /**
     * Get the Youtube duration time.
     * @author Yahia/Chris Z-S – I've been inspired by Yahia example <http://stackoverflow.com/a/26178914>
     * @param string $sDuration Youtube duration format (e.g., PT4M13S).
     * @return integer Youtube Duration in seconds.
     */
    protected function getDurationTime($sDuration)
    {
        preg_match_all('/[0-9]+[HMS]/', $sDuration, $aMatches);
        $iDuration = 0; // Default value

        foreach ($aMatches as $aMatch)
        {
            foreach ($aMatch as $iPors)
            {
                switch( substr($iPors, strlen($iPors)-1) )
                {
                    case 'H':
                        $iDuration += substr($iPors, 0, strlen($iPors)-1)*60*60;
                    break;

                    case 'M':
                        $iDuration += substr($iPors, 0, strlen($iPors)-1)*60;
                    break;

                    case 'S':
                        $iDuration += substr($iPors, 0, strlen($iPors)-1);
                    break;
                }
            }
        }
        return $iDuration;
    }

}

