<?php
/**
 * @title            API Interface
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video / Api
 * @version          1.0
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video\Api;

// The prototypes of the methods
interface IApi
{

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) Returns the video embed URL if it was found, FALSE otherwise.
     */
    public function getVideo($sUrl);

    /**
     * @param string $sUrl
     * @return mixed (object | boolean) FALSE if unable to open the url, otherwise the this object.
     */
    public function getInfo($sUrl);

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     * @return mixed (string | boolean) The title with escape function if found otherwise returns false.
     */
    public function getTitle();

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     * @return mixed (string | boolean) The description with escape function if found otherwise returns false.
     */
    public function getDescription();

    /**
     * @see \PH7\Framework\Video\Api\Api::getInfo();
     * @return mixed (integer | boolean) The duration video if found otherwise returns false.
     */
    public function getDuration();

    /**
     * @param string $sUrl
     * @param string $sMedia ("preview" or "movie").
     * @param integer $iWidth
     * @param integer $iHeight
     * @return string The HTML code.
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight);

    /**
     * @param string $sUrl
     * @return mixed (string | boolean) The embed URL if id is valid, false otherwise.
     */
    public function getEmbedUrl($sUrl);

    /**
     * @param string $sUrl
     * @return mixed (integer | boolean) Returns the ID of the video if it was found, FALSE otherwise.
     */
    public function getVideoId($sUrl);

}
