<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Service
 */

namespace PH7\Framework\Service;

defined('PH7') or exit('Restricted access');

/**
 * @class Abstract Class
 */
abstract class Emoticon
{
    private const DIR = 'smile/';
    private const EXT = '.gif';

    /**
     * Get the list of emoticons.
     */
    protected static function get(): array
    {
        return include PH7_PATH_APP_CONFIG . 'emoticon.php';
    }

    /**
     * Get the emoticon's path.
     */
    protected static function getPath(string $sName): string
    {
        return PH7_PATH_STATIC . PH7_IMG . self::DIR . $sName . self::EXT;
    }

    /**
     * Get the emoticon's URL.
     */
    protected static function getUrl(string $sName): string
    {
        return PH7_URL_STATIC . PH7_IMG . self::DIR . $sName . self::EXT;
    }

    /**
     * Gets the emoticon's name.
     */
    protected static function getName(array $aVal): string
    {
        return $aVal[1];
    }

    /**
     * Get the emoticon's code.
     */
    protected static function getCode(array $aVal): array|string
    {
        return $aVal[0];
    }
}
