<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Api
 * @link             http://pierrehenry.be
 */

namespace PH7\Framework\Api;

class AllowCors
{
    const ALLOW_CORS_ORIGIN_KEY = 'Access-Control-Allow-Origin';
    const ALLOW_CORS_METHOD_KEY = 'Access-Control-Allow-Methods';
    const ALLOW_CORS_HEADER_KEY = 'Access-Control-Allow-Headers';
    const ALLOW_CORS_ORIGIN_VALUE = '*';
    const ALLOW_CORS_METHOD_VALUE = 'GET, POST, PUT, DELETE, PATCH, OPTIONS';
    const ALLOW_CORS_HEADER_VALUE = '';

    /**
     * Initialize the Cross-Origin Resource Sharing (CORS) headers.
     *
     * @link https://en.wikipedia.org/wiki/Cross-origin_resource_sharing More info concerning CORS headers.
     */
    public function init()
    {
        $this->set(self::ALLOW_CORS_ORIGIN_KEY, self::ALLOW_CORS_ORIGIN_VALUE);
        $this->set(self::ALLOW_CORS_METHOD_KEY, self::ALLOW_CORS_METHOD_VALUE);
    }

    /**
     * Set data key to value.
     *
     * @param string $sKey The data key.
     * @param string $sValue The data value.
     */
    private function set($sKey, $sValue)
    {
        header($sKey . ':' . $sValue);
    }
}
