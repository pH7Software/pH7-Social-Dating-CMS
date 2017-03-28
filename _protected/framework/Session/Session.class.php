<?php
/**
 * @title            Session Class
 * @desc             Handler Session
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Session
 */

namespace PH7\Framework\Session;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Server\Server;

class Session
{
    /**
     * @param boolean $bDisableSessCache Disable PHP's session cache. Default FALSE
     */
    public function __construct($bDisableSessCache = false)
    {
        if ($bDisableSessCache)
            session_cache_limiter(false);

        session_name(Config::getInstance()->values['session']['cookie_name']);

        /**
         * In localhost mode, security session_set_cookie_params causing problems in the sessions, so we disable this if we are in localhost mode.
         * Otherwise if we are in production mode, we activate this.
         */
        if (!(new Server)->isLocalHost()) {
            $iTime = (int) Config::getInstance()->values['session']['expiration'];
            session_set_cookie_params($iTime, Config::getInstance()->values['session']['path'], Config::getInstance()->values['session']['domain'], (substr(PH7_URL_PROT, 0, 5) === 'https'), true);
        }

        $this->initializePHPSession();
    }

    /**
     * Set a PHP session.
     *
     * @param array|string $mName Name of the session.
     * @param string $sValue Value of the session, Optional if the session data is in a array.
     * @return void
     */
    public function set($mName, $sValue = null)
    {
        if (is_array($mName)) {
            foreach ($mName as $sName => $sVal) {
                $this->set($sName, $sVal);
            }
        } else {
            $_SESSION[Config::getInstance()->values['session']['prefix'] . $mName] = $sValue;
        }
    }

    /**
     * Get a session value by giving its name.
     *
     * @param string $sName Name of the session.
     * @param boolean $bEscape Default TRUE
     * @return string If the session exists, returns the session with function escape() (htmlspecialchars) if escape is enabled. Empty string value if the session doesn't exist.
     */
    public function get($sName, $bEscape = true)
    {
        $sSessionName = Config::getInstance()->values['session']['prefix'] . $sName;
        return (isset($_SESSION[$sSessionName]) ? ($bEscape ? escape($_SESSION[$sSessionName]) : $_SESSION[$sSessionName]) : '');
    }

    /**
     * Returns a boolean informing if the session exists or not.
     *
     * @param array|string $mName Name of the session.
     * @return boolean
     */
    public function exists($mName)
    {
        $bExists = false; // Default value

        if (is_array($mName)) {
            foreach ($mName as $sName) {
                if (!$bExists = $this->exists($sName)) {
                    return false;
                }
            }
        } else {
            $bExists = isset($_SESSION[Config::getInstance()->values['session']['prefix'] . $mName]);
        }

        return $bExists;
    }

    /**
     * Delete the session(s) if the session exists.
     *
     * @param array|string $mName Name of the session to delete.
     * @return void
     */
    public function remove($mName)
    {
        if (is_array($mName)) {
            foreach ($mName as $sName) {
                $this->remove($sName);
            }
        } else {
            $sSessionName = Config::getInstance()->values['session']['prefix'] . $mName;

            // We put the session in a table so if the session is in the form of multi-dimensional array, it is clear how much is destroyed
            $_SESSION[$sSessionName] = array();
            unset($_SESSION[$sSessionName]);
        }
    }

    /**
     * Session regenerate ID.
     *
     * @return void
     */
    public function regenerateId()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Destroy all PHP's sessions.
     */
    public function destroy()
    {
        if (!empty($_SESSION)) {
            $_SESSION = array();
            session_unset();
            session_destroy();
        }
    }

    /**
     * Check if the session is already initialized and initialize it if it isn't the case.
     *
     * @return void
     */
    protected function initializePHPSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            @session_start();
    }

    protected function close()
    {
        session_write_close();
    }

    public function __destruct()
    {
        // $this->close();
    }

    private function __clone()
    {
    }
}
