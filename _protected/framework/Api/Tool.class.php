<?php
/**
 * @title            Tool Class
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Api
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
     * @return boolean TRUE is the app has access, FALSE otherwise.
     */
    public static function checkAccess(Config $oConfig, Http $oRequest)
    {
        if (strcmp($oRequest->post('private_api_key'), $oConfig->values['api']['private_key']) === 0)
        {
            return in_array($oRequest->post('url'), $oConfig->values['api']['allow_domains']);
        }
        return false;
    }

}
