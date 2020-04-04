<?php
/**
 * @title            API Interface
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Video\Api;

// The prototypes of the methods
interface IApi
{
    /**
     * @param string $sUrl
     *
     * @return string|bool Returns the video embed URL if it was found, FALSE otherwise.
     */
    public function getVideo($sUrl);

    /**
     * @param string $sUrl
     *
     * @return IApi|bool
     */
    public function getInfo($sUrl);

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
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return string The HTML code.
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight);

    /**
     * @param string $sUrl
     *
     * @return string|bool The embed URL if id is valid, FALSE otherwise.
     */
    public function getEmbedUrl($sUrl);

    /**
     * @param string $sUrl
     *
     * @return int|bool Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId($sUrl);
}
