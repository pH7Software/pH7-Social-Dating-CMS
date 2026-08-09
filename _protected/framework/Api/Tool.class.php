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

class Tool
{
    const SOFTWARE_API_URL = 'https://api.ph7builder.com/';

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
        $mPrivateApiKey = $oRequest->gets('private_api_key');
        $mUrl = $oRequest->gets('url');

        if (!is_string($mPrivateApiKey) || !is_string($mUrl)) {
            return false;
        }

        if (self::isApiKeyValid($mPrivateApiKey, $oConfig)) {
            return self::isUrlAllowed($mUrl, $oConfig);
        }

        return false;
    }

    private static function isApiKeyValid(string $sPrivateApiKey, Config $oConfig): bool
    {
        $mConfiguredApiKey = $oConfig->values['ph7cms.api']['private_key'] ?? null;

        return is_string($mConfiguredApiKey) &&
            $mConfiguredApiKey !== '' &&
            hash_equals($mConfiguredApiKey, $sPrivateApiKey);
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
