<?php
/**
 * @title            Abstract API class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.0
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video\Api;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\File,
PH7\Framework\Str\Str,
PH7\Framework\Mvc\Model\DbConfig;

abstract class Api
{

    protected $oStr, $oData, $sApiKey, $bDefaultVideo, $bAutoplay;

    public function __construct()
    {
        $this->oStr = new Str;
        $this->sDefaultVideo = DbConfig::getSetting('defaultVideo');
        $this->bAutoplay = DbConfig::getSetting('autoplayVideo');
    }

    /**
     * Set API key (currentyl required only by Youtube API class).
     *
     * @param string $sApiKey
     * @return void
     */
    public function setKey($sApiKey)
    {
        $this->sApiKey = trim($sApiKey);
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
     * Gets description (it can be redefined if the recovery of the data information is more specific).
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (string | boolean) The description with escape function if found otherwise returns false.
     */
    public function getDescription()
    {
        return (!empty($this->oData->description) ? $this->oStr->escape($this->oData->description, true) : false);
    }

    /**
     * Gets video duration (it can be redefined if the recovery of the data information is more specific).
     *
     * @see \PH7\Framework\Video\Api::getInfo();
     * @return mixed (integer | boolean) The video duration if found, FALSE otherwise.
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
        $sUrl = str_replace(array('://', 'v=', 'v/', '?', '=', '//', $aData['scheme'], $aData['host'], 'watch', 'feature', 'player_embedded'), '', $sUrl);
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
     * @return mixed (object | boolean) Returns data object on success or FALSE on failure.
     */
    protected function getData($sUrl)
    {
        $sData = (new File)->getUrlContents($sUrl);
        $oData = json_decode($sData);
        return $oData;
    }

}
