<?php
/**
 * @title            Ip Class
 * @desc             Helper for the IP Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ip
 * @version          1.1
 */

namespace PH7\Framework\Ip;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Server\Server, PH7\Framework\Mvc\Model\DbConfig;

class Ip
{

    /**
     * Get IP address.
     *
     * @static
     * @return string IP address.
     */
    public static function get()
    {
        $sIp = ''; // Default IP address value.
        $aVars = [Server::HTTP_CLIENT_IP, Server::HTTP_X_FORWARDED_FOR, Server::REMOTE_ADDR];

        foreach($aVars as $sVar) {
            if (null !== Server::getVar($sVar))
            {
                $sIp = Server::getVar($sVar);
                break;
            }
        }
        unset($aVars);
        return preg_match('/^[a-z0-9:.]{7,}$/', $sIp) ? $sIp : '0.0.0.0';
    }

    /**
     * Returns the API IP with the IP address.
     *
     * @static
     * @param string $sIp IP address.
     * @return string API URL with the IP address.
     */
    public static function api($sIp = null)
    {
        $sIp = (empty($sIp)) ? static::get() : $sIp;
        return DbConfig::getSetting('ipApi') . $sIp;
    }

}
