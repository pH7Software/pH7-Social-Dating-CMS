<?php
/**
 * @title            API Interface
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @link             http://ph7builder.com
 */

declare(strict_types=1);

namespace PH7\Framework\Video\Api;

// The prototypes of the methods
interface Apible
{
    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the video embed URL if it was found, FALSE otherwise.
     */
    public function getVideo(string $sUrl);

    /**
     * @param string $sUrl
     *
     * @return Apible|bool
     */
    public function getInfo(string $sUrl);

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     *
     * @return string|bool The title with escape function if found, otherwise returns FALSE.
     */
    public function getTitle();

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     *
     * @return string|bool The description with escape function if found, otherwise returns FALSE.
     */
    public function getDescription();

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     *
     * @return int|bool The duration video if found, otherwise returns FALSE.
     */
    public function getDuration();

    /**
     * @param string $sUrl
     * @param string $sMedia ("preview" or "movie").
     * @param int|string $mWidth Could be `400` or '100%'
     * @param int|string $mHeight Could be `600` or '100%'
     *
     * @return string The HTML code.
     */
    public function getMeta(string $sUrl, string $sMedia, $mWidth, $mHeight): string;

    /**
     * @param string $sUrl
     *
     * @return string|bool The embed URL if id is valid, FALSE otherwise.
     */
    public function getEmbedUrl(string $sUrl);

    /**
     * @param string $sUrl
     *
     * @return int|bool Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId(string $sUrl);
}
