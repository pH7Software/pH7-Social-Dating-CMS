<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @version          1.2
 *
 * @see             http://ph7builder.com
 *
 * @history          28/03/2016 - Since pH7Builder 1.3.7, it's now compatible with Youtube API v3. Since Youtube API v3, it requires a Google API key. This is available through pH7Builder's admin panel.
 */

declare(strict_types=1);

namespace PH7\Framework\Video\Api;

defined('PH7') or exit('Restricted access');

class Youtube extends Api implements Apible
{
    public const API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet,contentDetails,statistics,status';
    public const PLAYER_URL = 'https://www.youtube.com/embed/';
    public const THUMBNAIL_URL = 'https://i%d.ytimg.com/vi/%s.jpg';
    public const REGEX_TIME_FORMAT = '/[0-9]+[HMS]/';
    public const API_KEY_MIN_LENGTH = 10;

    /** @var \stdClass */
    private $oContentDetails;

    /**
     * @return string|bool returns the embed video URL if found, FALSE otherwise
     */
    public function getVideo(string $sUrl)
    {
        return $this->getEmbedUrl($sUrl);
    }

    /**
     * @param string $sUrl The video URL (e.g., https://www.youtube.com/watch?v=q-1eHnBOg4A).
     *
     * @throws InvalidApiKeyException if there is a problem with YouTube API service
     *
     * @return self|bool FALSE if unable to open the API URL, otherwise YouTube
     */
    public function getInfo(string $sUrl)
    {
        if ($this->isApiKeySet()) {
            $sDataUrl = sprintf(static::API_URL, $this->getVideoId($sUrl), $this->sApiKey);

            if ($oData = $this->getData($sDataUrl)) {
                if (!is_object($oData)) {
                    return false;
                }

                $sErrorMessage = $this->retrieveErrorMessage($oData);
                if (isset($sErrorMessage)) {
                    throw new InvalidApiKeyException(sprintf('YouTube API: %s', $sErrorMessage));
                }

                if (
                    !isset($oData->items)
                    || !is_array($oData->items)
                    || !isset($oData->items[0])
                    || !is_object($oData->items[0])
                    || !isset($oData->items[0]->snippet, $oData->items[0]->contentDetails)
                    || !is_object($oData->items[0]->snippet)
                    || !is_object($oData->items[0]->contentDetails)
                    || !self::isValidTextValue($oData->items[0]->snippet->title ?? null)
                    || !self::isValidYouTubeDuration($oData->items[0]->contentDetails->duration ?? null)
                ) {
                    return false;
                }

                $this->oData = $oData->items[0]->snippet;
                $this->oContentDetails = $oData->items[0]->contentDetails; // Need only for getting the video duration

                return $this;
            }

            return false;
        }
        throw new InvalidApiKeyException(t('YouTube requires an API key to be set. Admin Dashboard -> Mod -> Video Youtube API key'));
    }

    /**
     * @see Youtube::getInfo();
     *
     * @return float|int the video duration if found, FALSE otherwise
     */
    public function getDuration()
    {
        return $this->getDurationTime($this->oContentDetails->duration);
    }

    /**
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

    public function isApiKeySet(): bool
    {
        return !empty($this->sApiKey) && strlen($this->sApiKey) > self::API_KEY_MIN_LENGTH;
    }

    /**
     * Get the YouTube video's duration time.
     *
     * @author Yahia/Chris Z-S – I've been inspired by Yahia example <http://stackoverflow.com/a/26178914>
     *
     * @param string $sDuration YouTube video's duration format (e.g., PT4M13S).
     *
     * @return int youTube Duration in seconds
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

    private static function isValidYouTubeDuration(mixed $mDuration): bool
    {
        return is_string($mDuration)
            && preg_match('/^PT(?:(?:[0-9]+H)(?:[0-9]+M)?(?:[0-9]+S)?|(?:[0-9]+M)(?:[0-9]+S)?|[0-9]+S)$/D', $mDuration) === 1;
    }

    private function retrieveErrorMessage(\stdClass $oData): ?string
    {
        if (!isset($oData->error) || !is_object($oData->error)) {
            return null;
        }

        $mMessage = $oData->error->message ?? $oData->error->errors[0]->message ?? null;

        return is_scalar($mMessage) ? (string)$mMessage : null;
    }
}
