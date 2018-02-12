<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
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
     * @param string $sClass
     *
     * @return Api\IApi
     *
     * @throws InvalidApiProviderException
     */
    public static function create($sClass)
    {
        switch ($sClass) {
            case in_array($sClass, self::YOUTUBE_NAMES, true):
                $oYoutube = new Api\Youtube;
                if (self::isVideoModule()) {
                    $sKey = Config::getInstance()->values['module.api']['youtube.key'];
                    $oYoutube->setKey($sKey); // Youtube's API v3+ requires an API key
                }
                return $oYoutube;

            case self::VIMEO_NAME:
                $sClass = Api\Vimeo::class;
                break;

            case in_array($sClass, self::DAILYMOTION_NAMES, true):
                $sClass = Api\Dailymotion::class;
                break;

            case self::METACAFE_NAME:
                $sClass = Api\Metacafe::class;
                break;

            default:
                throw new InvalidApiProviderException(
                    sprintf('Invalid API video type. Wrong specified type: %s', $sClass)
                );
        }

        return new $sClass;
    }

    /**
     * @return bool
     */
    private static function isVideoModule()
    {
        return Registry::getInstance()->module === 'video';
    }
}
