<?php
/**
 * @title            Abstract API class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @link             http://ph7builder.com
 */

declare(strict_types=1);

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Str\Str;

abstract class Api
{
    protected Str $oStr;

    /** @var \stdClass|\DOMXPath */
    protected $oData;

    protected string $sApiKey;

    protected bool $bAutoplay;

    public function __construct()
    {
        $this->oStr = new Str;
        $this->bAutoplay = (bool)DbConfig::getSetting('autoplayVideo');
    }

    /**
     * Set API key (currently only required by YouTube API class).
     */
    public function setKey(string $sApiKey): void
    {
        $this->sApiKey = trim($sApiKey);
    }

    /**
     * Gets title (it can be redefined if the recovery of the data information is more specific).
     *
     * @see Api::getInfo();
     *
     * @return string|bool The title with escape function if found otherwise returns false.
     */
    public function getTitle()
    {
        return !empty($this->oData->title) ? $this->oStr->escape($this->oData->title, true) : false;
    }

    /**
     * Gets description (it can be redefined if the recovery of the data information is more specific).
     *
     * @see Api::getInfo();
     *
     * @return string|bool The description with escape function if found otherwise returns false.
     */
    public function getDescription()
    {
        return !empty($this->oData->description) ? $this->oStr->escape($this->oData->description, true) : false;
    }

    /**
     * Gets video duration (it can be redefined if the recovery of the data information is more specific).
     *
     * @see Api::getInfo();
     *
     * @return int|bool The video duration if found, FALSE otherwise.
     */
    public function getDuration()
    {
        return !empty($this->oData->duration) ? (int)$this->oData->duration : false;
    }

    /**
     * @param string $sUrl
     *
     * @return string|bool The embed URL if id is valid, false otherwise.
     */
    public function getEmbedUrl(string $sUrl)
    {
        if (!$this->getVideoId($sUrl)) {
            return false;
        }

        return static::PLAYER_URL . $this->getVideoId($sUrl);
    }

    /**
     * Generic method (but still specialized in Youtube API while remaining open to other APIs)
     * to retrieve the ID of the video. It can be redefined if the recovery of the video ID is more specific.
     */
    public function getVideoId(string $sUrl)
    {
        $aData = parse_url($sUrl);
        $sUrl = str_replace(
            [
                '://',
                'v=',
                'v/',
                'embed/',
                '?',
                '=',
                '//',
                $aData['scheme'],
                $aData['host'],
                'watch',
                'feature',
                'player_embedded'
            ],
            '',
            $sUrl
        );
        $sUrl = preg_replace('#^/#', '', $sUrl);
        $sUrl = preg_replace('#^([^/&=\?]+)(?:.+)?$#i', '$1', $sUrl);
        $sUrl = str_replace(['&', '/'], '', $sUrl); // To finish the cleaning

        return $sUrl;
    }

    /**
     * Retrieve information on the video site where it is hosted.
     *
     * @param string $sUrl
     *
     * @return \stdClass|bool Returns data object on success or FALSE on failure.
     */
    protected function getData(string $sUrl)
    {
        $sData = (new File)->getUrlContents($sUrl);
        return json_decode($sData);
    }
}
