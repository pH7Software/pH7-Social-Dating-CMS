<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Video;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Http\Http;

class Api
{
    const URL_PATTERN = '(^https?://|www\.|\.[a-z]{2,4}/?(.+)?$)';

    /** @var int */
    protected $iWidth;

    /** @var int */
    protected $iHeight;

    public function __construct()
    {
        $this->iWidth = 480;
        $this->iHeight = 295;
    }

    /**
     * @param string $sUrl
     *
     * @return string Returns the video embed URL.
     */
    public function getVideo($sUrl)
    {
        $sClass = $this->clear($sUrl);

        switch ($sClass) {
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
     * @param string $sUrl The URL video.
     *
     * @return IApi The Video API class (e.g. Api\Youtube, Api\Vimeo, Api\Dailymotion).
     *
     * @throws PH7InvalidArgumentException If the Api Video is invalid.
     */
    public function getInfo($sUrl)
    {
        $sClass = $this->clear($sUrl);

        switch ($sClass) {
            case 'youtube':
            case 'youtu':
                $sKey = Config::getInstance()->values['module.api']['youtube.key'];
                $oYoutube = new Api\Youtube;
                $oYoutube->setKey($sKey); // Youtube's API v3+ requires an API key
                $oClass = $oYoutube->getInfo($sUrl);
                unset($oYoutube);
                break;

            case 'vimeo':
                $oClass = (new Api\Vimeo)->getInfo($sUrl);
                break;

            case 'dailymotion':
                $oClass = (new Api\Dailymotion)->getInfo($sUrl);
                break;

            case 'metacafe':
                $oClass = (new Api\Metacafe)->getInfo($sUrl);
                break;

            default:
                throw new PH7InvalidArgumentException('Invalid Api Video Type! Bad Type is: \'' . $sClass . '\'');
        }

        return $oClass;
    }

    /**
     * @param string $sUrl
     * @param string $sMedia (preview or movie)
     * @param integer $iWidth
     * @param integer $iHeight
     *
     * @throws PH7InvalidArgumentException If the Video Api is invalid.
     *
     * @return string The HTML video integration code.
     */
    public function getMeta($sUrl, $sMedia, $iWidth, $iHeight)
    {
        $sClass = $this->clear($sUrl);

        $sMedia = ( isset($sMedia) ? $sMedia : 'movie' );
        $iWidth = ( isset($iWidth) ? $iWidth : $this->iWidth );
        $iHeight = (isset($iHeight) ? $iHeight : $this->iHeight );

        switch ($sClass) {
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
                throw new PH7InvalidArgumentException('Invalid Api Video Type! Bad Type is: \''  . $sClass . '\'');
        }

        return $sClass;
    }

    /**
     * @param string $sUrl
     *
     * @return string
     */
    protected function clear($sUrl)
    {
        $oHttp = new Http;
        if ($oHttp->detectSubdomain($sUrl)) {
            // Removes the subdomain with its dot (e.g. mysub.domain.com becomes domain.com).
            $sUrl = str_replace($oHttp->getSubdomain($sUrl) . PH7_DOT, '', $sUrl);
        }
        unset($oHttp);

        return preg_replace('#' . static::URL_PATTERN . '#i', '', $sUrl);
    }
}
