<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Video;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Registry\Registry;

class ProviderFactory
{
    const VIDEO_MODULE_NAME = 'video';

    const INVALID_API_PROVIDER_MESSAGE = 'Invalid API video type. Wrong specified type is: %s';

    const YOUTUBE_NAMES = [
        'youtube',
        'youtu'
    ];
    const DAILYMOTION_NAMES = [
        'dailymotion',
        'dai'
    ];
    const VIMEO_NAME = 'vimeo';
    const METACAFE_NAME = 'metacafe';

    /**
     * @param string $sVideoPlatform
     *
     * @return Api\IApi
     *
     * @throws InvalidApiProviderException
     */
    public static function create($sVideoPlatform)
    {
        switch ($sVideoPlatform) {
            case in_array($sVideoPlatform, self::YOUTUBE_NAMES, true):
                $oYoutube = new Api\Youtube;
                if (self::isVideoModule()) {
                    $sKey = Config::getInstance()->values['module.api']['youtube.key'];
                    $oYoutube->setKey($sKey); // Youtube's API v3+ requires an API key
                }
                return $oYoutube;

            case self::VIMEO_NAME:
                $sClass = Api\Vimeo::class;
                break;

            case in_array($sVideoPlatform, self::DAILYMOTION_NAMES, true):
                $sClass = Api\Dailymotion::class;
                break;

            case self::METACAFE_NAME:
                $sClass = Api\Metacafe::class;
                break;

            default:
                throw new InvalidApiProviderException(
                    sprintf(self::INVALID_API_PROVIDER_MESSAGE, $sVideoPlatform)
                );
        }

        return new $sClass;
    }

    /**
     * Check if the page request is done from the "video" module.
     *
     * @return bool
     */
    private static function isVideoModule()
    {
        return Registry::getInstance()->module === self::VIDEO_MODULE_NAME;
    }
}
