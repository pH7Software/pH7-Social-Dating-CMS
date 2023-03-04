<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.2
 * @link             http://ph7builder.com
 * @history          28/03/2016 - Since pH7Builder 1.3.7, it's now compatible with Youtube API v3. Since Youtube API v3, it requires a Google API key. This is available through pH7Builder's admin panel.
 */

declare(strict_types=1);

namespace PH7\Framework\Video\Api;

use stdClass;

defined('PH7') or exit('Restricted access');

class Youtube extends Api implements Apible
{
    const API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails,statistics,status';
    const PLAYER_URL = 'https://www.youtube.com/embed/';
    const THUMBNAIL_URL = 'https://i%d.ytimg.com/vi/%s.jpg';
    const REGEX_TIME_FORMAT = '/[0-9]+[HMS]/';
    const API_KEY_MIN_LENGTH = 10;

    /** @var \stdClass */
    private $oContentDetails;

    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the embed video URL if found, FALSE otherwise.
     */
    public function getVideo(string $sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl The video URL (e.g., https://www.youtube.com/watch?v=q-1eHnBOg4A).
     *
     * @return self|bool FALSE if unable to open the API URL, otherwise YouTube
     *
     * @throws InvalidApiKeyException If there is a problem with YouTube API service.
     */
    public function getInfo(string $sUrl)
    {
        if ($this->isApiKeySet()) {
            $sDataUrl = sprintf(static::API_URL, $this->getVideoId($sUrl), $this->sApiKey);

            if ($oData = $this->getData($sDataUrl)) {
                $sErrorMessage = $this->retrieveErrorMessage($oData);
                if (isset($sErrorMessage)) {
                    throw new InvalidApiKeyException(
                        sprintf('YouTube API: %s', $sErrorMessage)
                    );
                }

                $this->oData = $oData->items[0]->snippet;
                $this->oContentDetails = $oData->items[0]->contentDetails; // Need only for getting the video duration

                return $this;
            }

            return false;
        } else {
            throw new InvalidApiKeyException(
                t('YouTube requires an API key to be set. Admin Dashboard -> Mod -> Video Youtube API key')
            );
        }
    }

    /**
     * @see Youtube::getInfo();
     *
     * @return float|int The video duration if found, FALSE otherwise.
     */
    public function getDuration()
    {
        return $this->getDurationTime($this->oContentDetails->duration);
    }

    /**
     * @param string $sUrl
     * @param string $sMedia
     * @param int|string $iWidth
     * @param int|string $iHeight
     */
    public function getMeta(string $sUrl, string $sMedia, $iWidth, $iHeight): string
    {
        if ($sMedia === 'preview') {
            $aThumb = ['default', 1, 2, 3];
            shuffle($aThumb);

            return sprintf(self::THUMBNAIL_URL, mt_rand(1, 4), $this->getVideoId($sUrl) . PH7_SH . $aThumb[0]);
        }

        $sParam = $this->bAutoplay ? '?autoplay=1&amp;' : '?';

        return '<iframe width="' . $iWidth . '" height="' . $iHeight . '" src="' . $this->getEmbedUrl($sUrl) . $sParam . 'rel=0" frameborder="0" allowfullscreen></iframe>';
    }

    /**
     * Get the YouTube video's duration time.
     *
     * @author Yahia/Chris Z-S – I've been inspired by Yahia example <http://stackoverflow.com/a/26178914>
     *
     * @param string $sDuration YouTube video's duration format (e.g., PT4M13S).
     *
     * @return int YouTube Duration in seconds.
     */
    protected function getDurationTime(string $sDuration)
    {
        preg_match_all(self::REGEX_TIME_FORMAT, $sDuration, $aMatches);
        $iDuration = 0; // Default value

        foreach ($aMatches as $aMatch) {
            foreach ($aMatch as $iPors) {
                switch (substr($iPors, strlen($iPors) - 1)) {
                    case 'H':
                        $iDuration += substr($iPors, 0, strlen($iPors) - 1) * 60 * 60;
                        break;
                    case 'M':
                        $iDuration += substr($iPors, 0, strlen($iPors) - 1) * 60;
                        break;
                    case 'S':
                        $iDuration += substr($iPors, 0, strlen($iPors) - 1);
                        break;
                }
            }
        }

        return $iDuration;
    }

    private function retrieveErrorMessage(stdClass $oData): ?string
    {
        return $oData->error->message ?? $oData->error->errors[0]->message ?? null;
    }

    public function isApiKeySet(): bool
    {
        return !empty($this->sApiKey) && strlen($this->sApiKey) > self::API_KEY_MIN_LENGTH;
    }
}
