<?php

/**
 * @title            Abstract API class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @see             http://ph7builder.com
 */

declare(strict_types=1);

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\CurlException;
use PH7\Framework\File\File;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Str\Str;

abstract class Api
{
    protected const PLAYER_URL = '';

    protected Str $oStr;

    /** @var \stdClass|\DOMXPath */
    protected $oData;

    protected string $sApiKey;

    protected bool $bAutoplay;

    public function __construct(?bool $bAutoplay = null)
    {
        $this->oStr = new Str();
        $this->bAutoplay = $bAutoplay ?? (bool)DbConfig::getSetting('autoplayVideo');
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
     * @return string|bool the title with escape function if found otherwise returns false
     */
    public function getTitle()
    {
        $mTitle = is_object($this->oData) ? ($this->oData->title ?? null) : null;

        return self::isValidTextValue($mTitle) ? $this->oStr->escape($mTitle, true) : false;
    }

    /**
     * Gets description (it can be redefined if the recovery of the data information is more specific).
     *
     * @see Api::getInfo();
     *
     * @return string|bool the description with escape function if found otherwise returns false
     */
    public function getDescription()
    {
        $mDescription = is_object($this->oData) ? ($this->oData->description ?? null) : null;

        return self::isValidTextValue($mDescription) ? $this->oStr->escape($mDescription, true) : false;
    }

    /**
     * Gets video duration (it can be redefined if the recovery of the data information is more specific).
     *
     * @see Api::getInfo();
     *
     * @return int|bool the video duration if found, FALSE otherwise
     */
    public function getDuration()
    {
        $mDuration = is_object($this->oData) ? ($this->oData->duration ?? null) : null;

        return self::isValidDurationValue($mDuration) ? (int)$mDuration : false;
    }

    /**
     * @return string|bool the embed URL if id is valid, false otherwise
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
     * @return \stdClass|bool returns data object on success or FALSE on failure
     */
    protected function getData(string $sUrl)
    {
        try {
            $mData = (new File())->getUrlContents($sUrl);
        } catch (CurlException $oException) {
            return false;
        }

        return $this->decodeData($mData);
    }

    /**
     * @param string|false $mData
     *
     * @return array|\stdClass|bool
     */
    protected function decodeData($mData)
    {
        if (!is_string($mData)) {
            return false;
        }

        $mDecodedData = json_decode($mData);

        return is_array($mDecodedData) || is_object($mDecodedData) ? $mDecodedData : false;
    }

    protected static function isValidTextValue(mixed $mValue): bool
    {
        return is_string($mValue) && trim($mValue) !== '';
    }

    protected static function isValidDurationValue(mixed $mValue): bool
    {
        return (is_int($mValue) && $mValue > 0)
            || (is_string($mValue) && preg_match('/^[1-9][0-9]*$/D', $mValue) === 1);
    }
}
