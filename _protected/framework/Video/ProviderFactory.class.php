<?php
/**
 * @title            Video API Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video
 * @link             http://ph7builder.com
 */

declare(strict_types=1);

namespace PH7\Framework\Video;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Video\Api\Apible;

class ProviderFactory
{
    public const VIDEO_MODULE_NAME = 'video';

    private const INVALID_API_PROVIDER_MESSAGE = 'Invalid API video type. Wrong specified type is: %s';

    private const VIMEO_NAME = 'vimeo';
    private const METACAFE_NAME = 'metacafe';

    private const YOUTUBE_NAMES = [
        'youtube',
        'youtu'
    ];
    private const DAILYMOTION_NAMES = [
        'dailymotion',
        'dai'
    ];

    /**
     * @throws InvalidApiProviderException
     */
    public static function create(string $sVideoPlatform): Apible
    {
        switch ($sVideoPlatform) {
            case in_array($sVideoPlatform, self::YOUTUBE_NAMES, true):
                $oYoutube = new Api\Youtube;
                if (self::isVideoModule()) {
                    $sKey = Config::getInstance()->values['module.api']['youtube.key'];
                    $oYoutube->setKey($sKey); // YouTube's API v3+ requires an API key
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
     */
    private static function isVideoModule(): bool
    {
        return Registry::getInstance()->module === self::VIDEO_MODULE_NAME;
    }
}
