<?php
/**
 * @desc             Handler Cookie
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Cookie
 */

declare(strict_types=1);

namespace PH7\Framework\Cookie;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Server\Server;

class Cookie
{
    /**
     * Set a PHP cookie.
     *
     * @param array|string $mName Name of the cookie.
     * @param string|null $sValue value of the cookie, Optional if the cookie data is in an array.
     * @param int|null $iTime The time the cookie expires. This is a Unix timestamp.
     * @param bool|null $bSecure If TRUE cookie will only be sent over a secure HTTPS connection from the client.
     */
    public function set($mName, ?string $sValue = null, ?int $iTime = null, ?bool $bSecure = null): void
    {
        $iTime = time() + ((int)!empty($iTime) ? $iTime : Config::getInstance()->values['cookie']['expiration']);
        $bSecure = !empty($bSecure) && is_bool($bSecure) ? $bSecure : Server::isHttps();

        if (is_array($mName)) {
            foreach ($mName as $sName => $sVal) {
                $this->set($sName, $sVal, $iTime, $bSecure);
            }
        } else {
            $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $mName;

            /* Check if we are not in localhost mode, otherwise may not work */
            if (!Server::isLocalHost()) {
                setcookie(
                    $sCookieName,
                    $sValue,
                    $iTime,
                    Config::getInstance()->values['cookie']['path'],
                    Config::getInstance()->values['cookie']['domain'],
                    $bSecure,
                    true
                );
            } else {
                setcookie(
                    $sCookieName,
                    $sValue,
                    $iTime,
                    PH7_SH
                );
            }
        }
    }

    /**
     * Get the cookie value by giving its name.
     *
     * @param string $sName Name of the cookie.
     * @param bool|null $bEscape
     *
     * @return mixed If the cookie exists, returns the cookie with function escape() (htmlspecialchars) if escape is enabled. Empty string value if the cookie doesn't exist.
     */
    public function get(string $sName, ?bool $bEscape = true)
    {
        $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $sName;

        return (isset($_COOKIE[$sCookieName]) ? ($bEscape && is_string($_COOKIE[$sCookieName]) ? escape($_COOKIE[$sCookieName]) : $_COOKIE[$sCookieName]) : '');
    }

    /**
     * Returns a boolean informing if the cookie exists or not.
     *
     * @param array|string $mName Name of the cookie.
     *
     * @return bool
     */
    public function exists($mName): bool
    {
        $bExists = false; // Default value

        if (is_array($mName)) {
            foreach ($mName as $sName) {
                if (!$bExists = $this->exists($sName)) {
                    return false;
                }
            }
        } else {
            $bExists = isset($_COOKIE[Config::getInstance()->values['cookie']['prefix'] . $mName]);
        }

        return $bExists;
    }

    /**
     * Delete the cookie(s) key if the cookie exists.
     *
     * @param array|string $mName Name of the cookie to delete.
     */
    public function remove($mName): void
    {
        if (is_array($mName)) {
            foreach ($mName as $sName) {
                $this->remove($sName);
            }
        } else {
            $sCookieName = Config::getInstance()->values['cookie']['prefix'] . $mName;

            // We put the cookie into an array. So, if the cookie is in a multi-dimensional arrays, it is clear how much is destroyed
            $_COOKIE[$sCookieName] = array();

            // We ask the browser to delete the cookie
            if (!Server::isLocalHost()) {
                setcookie(
                    $sCookieName,
                    '',
                    0,
                    Config::getInstance()->values['cookie']['path'],
                    Config::getInstance()->values['cookie']['domain'],
                    Server::isHttps(),
                    true
                );
            } else {
                setcookie($sCookieName, '', 0, PH7_SH);
            }

            // then, we delete the cookie value locally to avoid using it by mistake later on in the script
            unset($_COOKIE[$sCookieName]);
        }
    }

    private function __clone()
    {
    }
}
