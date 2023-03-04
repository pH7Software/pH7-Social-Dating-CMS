<?php
/**
 * @title            API Tool Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Api
 * @link             http://ph7builder.com
 */

declare(strict_types=1);

namespace PH7\Framework\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Server\Server;

class Tool
{
    const SOFTWARE_API_URL = 'https://api.ph7builder.com/';
    const DEV_APP_API_KEY = 'dev772277';

    /**
     * Check if an external app can have access to the API.
     *
     * @param Config $oConfig
     * @param HttpRequest $oRequest
     *
     * @return bool Returns TRUE if the app has access, FALSE otherwise.
     */
    public static function checkAccess(Config $oConfig, HttpRequest $oRequest): bool
    {
        if (self::isApiKeyValid($oRequest->gets('private_api_key'), $oConfig)) {
            return self::isUrlAllowed($oRequest->gets('url'), $oConfig);
        }

        return false;
    }

    private static function isApiKeyValid(string $sPrivateApiKey, Config $oConfig): bool
    {
        return strcmp($sPrivateApiKey, $oConfig->values['ph7cms.api']['private_key']) === 0 ||
            (Server::isLocalHost() && $sPrivateApiKey === self::DEV_APP_API_KEY);
    }

    private static function isUrlAllowed(string $sUrl, Config $oConfig): bool
    {
        return in_array(
            $sUrl,
            $oConfig->values['ph7cms.api']['allow_domains'],
            true
        );
    }
}
