<?php
/**
 * @title            Cookie Class
 * @desc             Handler Cookie
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cookie
 * @version          1.0
 */

namespace PH7\Framework\Cookie;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;

class Cookie
{

    /**
     * @desc Set a PHP Cookie.
     * @param mixed (array or string) $mName Name of the cookie.
     * @param string $sValue value of the cookie, Optional if the cookie data is in a array.
     * @param int $iTime The time the cookie expires. This is a Unix timestamp.
     * @param bool $bSecure If TRUE cookie will only be sent over a secure HTTPS connection from the client.
     * @return void
     */
    public function set($mName, $sValue = null, $iTime = null, $bSecure = null)
    {
        $iTime = (int) (!empty($iTime)) ? $iTime : Config::getInstance()->values['cookie']['expiration'];
        $bSecure = (!empty($bSecure) && is_bool($bSecure)) ? $bSecure : (substr(PH7_URL_PROT, 0, 5) === 'https') ? true : false;

        if (is_array($mName))
        {
            foreach ($mName as $sN => $sV)
                $this->set($sN, $sV, $iTime, $bSecure); // Recursive method
        }
        else
        {
            $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $mName;

            /* Check if we are not in localhost mode, otherwise may not work. */
            if (!(new \PH7\Framework\Server\Server)->isLocalHost())
                setcookie($sCookieName, $sValue, time() + $iTime, Config::getInstance()->values['cookie']['path'], Config::getInstance()->values['cookie']['domain'], $bSecure, true);
            else
                setcookie($sCookieName, $sValue, time() + $iTime, PH7_SH);
        }
    }

    /**
     * @desc Get Cookie.
     * @param string $sName Name of the cookie.
     * @param boolean $bEscape Default TRUE
     * @return string If the cookie exists, returns the cookie with function escape() (htmlspecialchars) if escape is enabled. Empty string value if the cookie does not exist.
     */
    public function get($sName, $bEscape = true)
    {
        $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $sName;
        return (!empty($_COOKIE[$sCookieName]) ? ($bEscape ? escape($_COOKIE[$sCookieName]) : $_COOKIE[$sCookieName]) : '');
    }

    /**
     * @desc Returns a boolean informing of whether or not the requested cookie variable.
     * @param mixed (array or string) $mName Name of the cookie.
     * @return boolean
     */
    public function exists($mName)
    {
        $bExists = false; // Default value

        if (is_array($mName))
        {
            foreach ($mName as $sName)
                if (!$bExists = $this->exists($sName)) break; // Recursive method
        }
        else
        {
            $bExists = (!empty($_COOKIE[Config::getInstance()->values['cookie']['prefix'] . $mName])) ? true : false;
        }

        return $bExists;
    }

    /**
     * @desc Delete the cookie(s) key if the cookie exists.
     * @param mixed (array or string) $mName Name of the cookie to delete.
     * @return void
     */
    public function remove($mName)
    {
        if (is_array($mName))
        {
            foreach ($mName as $sN)
                $this->remove($sN); // Recursive method
        }
        else
        {
            $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $mName;

            // We put the cookie in a table so if the cookie is in the form of multi-dimensional array, it is clear how much is destroyed
            $_COOKIE[$sCookieName] = array();
            // We are asking the browser to delete the cookie
            setcookie($sCookieName);
            // and we delete the cookie value locally to avoid using it by mistake in following our script
            unset($_COOKIE[$sCookieName]);
        }
    }

    /*
     * @__clone
     * @access private
     */
    private function __clone()
    {
    }

}
