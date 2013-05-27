<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @version          1.0
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Str\Str,
PH7\Framework\Http\Http,
PH7\Framework\Mvc\Model\DbConfig;

class Api extends Video
{

    protected $oStr, $oData, $bDefaultVideo, $bAutoplay;

    public function __construct()
    {
        $this->oStr = new Str;
        $this->sDefaultVideo = DbConfig::getSetting('defaultVideo');
        $this->bAutoplay = DbConfig::getSetting('autoplayVideo');
    }

    /**
     * @param string $sUrl
     * @return object Class
     */
    public function getVideo($sUrl)
    {
        $sClass = $this->clear($sUrl);
        switch ($sClass)
        {
            case 'youtube':
            case 'youtu':
              $sClass = (new Api\Youtube)->getVideo($sUrl);
            break;

            case 'vimeo':
              $sClass = (new Api\Vimeo)->getVideo($sUrl);
            break;

            case 'dailymotion':
              $sClass = (new Api\Dailymotion)->getVideo($sUrl);
            break;

            case 'metacafe':
              $sClass = (new Api\Metacafe)->getVideo($sUrl);
            break;

            default:
              return false;
        }
        return $sClass;
    }

    /**
     * @param string $sUrl
     * @return object Class
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the Api Video is invalid.
     */
    public function getInfo($sUrl)
    {
        $sClass = $this->clear($sUrl);

        switch ($sClass)
        {
            case 'youtube':
            case 'youtu':
                $sClass = (new Api\Youtube)->getInfo($sUrl);
            break;

            case 'vimeo':
                $sClass = (new Api\Vimeo)->getInfo($sUrl);
            break;

            case 'dailymotion':
                $sClass = (new Api\Dailymotion)->getInfo($sUrl);
            break;

            case 'metacafe':
                $sClass = (new Api\Metacafe)->getInfo($sUrl);
            break;

            default:
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Invalid Api Video Type! Bad Type is: \''  . $sClass . '\'');
                return; // Stop it
        }

        return $sClass;
    }

    /**
     * @param string $sUrl
     * @param string $sMedia (preview or movie)
     * @param integer $iWidth
     * @param integer $iHeight
     * @return object Class
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the Video Api is invalid.
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {
        $sClass = $this->clear($sUrl);

        $sMedia = ( isset($sMedia) ? $sMedia : 'movie' );
        $iWidth = ( isset($iWidth) ? $iWidth : $this->iWidth );
        $iHeight = (isset($iHeight) ? $iHeight : $this->iHeight );

        switch ($sClass)
        {
            case 'youtube':
            case 'youtu':
                $sClass = (new Api\Youtube)->getMeta($sUrl, $sMedia, $iWidth, $iHeight);
            break;

            case 'vimeo':
                $sClass = (new Api\Vimeo)->getMeta($sUrl, $sMedia, $iWidth, $iHeight);
            break;

            case 'dailymotion':
                $sClass = (new Api\Dailymotion)->getMeta($sUrl, $sMedia, $iWidth, $iHeight);
            break;

            case 'metacafe':
                $sClass = (new Api\Metacafe)->getMeta($sUrl, $sMedia, $iWidth, $iHeight);
            break;

            default:
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Invalid Api Video Type! Bad Type is: \''  . $sClass . '\'');
                return; // Stop it
        }

        return $sClass;
    }

    /**
     * Gets title (it can be redefined if the recovery of the data information is more specific).
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (string | boolean) The title with escape function if found otherwise returns false.
     */
    public function getTitle()
    {
        return (!empty($this->oData->title) ? $this->oStr->escape($this->oData->title, true) : false);
    }

    /**
     * Gets Description (it can be redefined if the recovery of the data information is more specific).
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (string | boolean) The description with escape function if found otherwise returns false.
     */
    public function getDescription()
    {
        return (!empty($this->oData->description) ? $this->oStr->escape($this->oData->description, true) : false);
    }

    /**
     * Gets Duration video (it can be redefined if the recovery of the data information is more specific).
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (integer | boolean) The duration video if found otherwise returns false.
     */
    public function getDuration()
    {
        return (!empty($this->oData->duration) ? (int)$this->oData->duration : false);
    }

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) The embed URL if id is valid, false otherwise.
     */
    public function getEmbedUrl($sUrl)
    {
        if (!$this->getVideoId($sUrl)) return false;

        return static::PLAYER_URL . $this->getVideoId($sUrl);
    }

    /**
     * Generic method (but still specialized in Youtube API while remaining open to other APIs)
     * to retrieve the ID of the video. It can be redefined if the recovery of the video ID is more specific.
     *
     * @param string $sUrl
     * @return string string
     */
    public function getVideoId($sUrl)
    {
        $aData = parse_url($sUrl);
        $sUrl = str_replace(array('://', '?', '=', '//', $aData['scheme'], $aData['host'], 'v', 'watch', 'feature', 'player_embedded'), '', $sUrl);
        $sUrl = preg_replace('#^/#', '', $sUrl);
        $sUrl = preg_replace('#^([^/&=\?]+)(?:.+)?$#i', '$1', $sUrl);
        $sUrl = str_replace(array('&', '/'), '', $sUrl); // To finish the cleaning
        return $sUrl;
    }

    /**
     * Retrieve information on the video site where it is hosted.
     *
     * @access protected
     * @param string $sUrl
     * @return mixed Returns OBJECT JSON on success or FALSE on failure.
     */
    protected function getData($sUrl)
    {
        $sData = (new \PH7\Framework\File\File)->getUrlContents($sUrl);
        $oData = json_decode($sData);
        return $oData;
    }

    /**
     * @access protected
     * @param string $sUrl
     * @return string
     */
    protected function clear($sUrl)
    {
        $oHttp = new Http;
        if ($oHttp->detectSubdomain($sUrl))
        {
            // Removes the subdomain with its dot (e.g. mysub.domain.com becomes domain.com).
            $sUrl = str_replace($oHttp->getSubdomain($sUrl) . PH7_DOT, '', $sUrl);
        }
        unset($oHttp);

        return preg_replace('#(^https?://|www\.|\.[a-z]{2,4}/?(.+)?$)#i', '', $sUrl);
    }

}
