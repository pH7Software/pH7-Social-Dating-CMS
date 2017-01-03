<?php
/**
 * @title            API Tool Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Api
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Api;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config, PH7\Framework\Mvc\Request\Http;

class Tool
{

    const SOFTWARE_API_URL = 'http://api.hizup.com/';

    /**
     * Check if an external app can have access to the API.
     *
     * @param \PH7\Framework\Config\Config $oConfig
     * @param \PH7\Framework\Mvc\Request\Http $oRequest
     * @return boolean Returns TRUE if the app has access, FALSE otherwise.
     */
    public static function checkAccess(Config $oConfig, Http $oRequest)
    {
        if (strcmp($oRequest->gets('private_api_key'), $oConfig->values['ph7cms.api']['private_key']) === 0)
        {
            return in_array($oRequest->gets('url'), $oConfig->values['ph7cms.api']['allow_domains']);
        }
        return false;
    }

}
