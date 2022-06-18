<?php
/**
 * @desc             Handler Session
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Session
 */

declare(strict_types=1);

namespace PH7\Framework\Session;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Server\Server;

class Session
{
    /**
     * @param bool|null $bDisableSessionCache Disable PHP's session cache.
     */
    public function __construct(bool $bDisableSessionCache = false)
    {
        if (!$this->isSessionActivated()) {
            if ($bDisableSessionCache) {
                session_cache_limiter();
            }

            $this->initializePHPSession();
        }
    }

    /**
     * Set a PHP session.
     *
     * @param array|string $mName Name of the session.
     * @param string|null $sValue Value of the session, Optional if the session data is in an array.
     */
    public function set($mName, $sValue = null): void
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
     * @param bool|null $bEscape
     *
     * @return mixed If the session exists, returns the session with function escape() (htmlspecialchars) if escape is enabled. Empty string value if the session doesn't exist.
     */
    public function get(string $sName, ?bool $bEscape = true)
    {
        $sSessionName = Config::getInstance()->values['session']['prefix'] . $sName;

        return (isset($_SESSION[$sSessionName]) ? ($bEscape && is_string($_SESSION[$sSessionName]) ? escape($_SESSION[$sSessionName]) : $_SESSION[$sSessionName]) : '');
    }

    /**
     * Returns a boolean informing if the session exists or not.
     *
     * @param array|string $mName Name of the session.
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
            $bExists = isset($_SESSION[Config::getInstance()->values['session']['prefix'] . $mName]);
        }

        return $bExists;
    }

    /**
     * Delete the session(s) if the session exists.
     *
     * @param array|string $mName Name of the session to delete.
     */
    public function remove($mName): void
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
     */
    public function regenerateId(): void
    {
        if ($this->isSessionActivated()) {
            session_regenerate_id(true);
        }
    }

    /**
     * Destroy all PHP's sessions.
     */
    public function destroy(): void
    {
        if (!empty($_SESSION)) {
            $_SESSION = [];
            session_unset();
            session_destroy();
        }
    }

    /**
     * Check if the session is already initialized and initialize it if it isn't the case.
     */
    private function initializePHPSession(): void
    {
        session_name(Config::getInstance()->values['session']['cookie_name']);

        /**
         * In localhost mode, security session_set_cookie_params causing problems in the sessions, so we disable this if we are in localhost mode.
         * Otherwise, if we are in production mode, we activate it.
         */
        if (!Server::isLocalHost()) {
            $iTime = (int)Config::getInstance()->values['session']['expiration'];
            session_set_cookie_params(
                $iTime,
                Config::getInstance()->values['session']['path'],
                Config::getInstance()->values['session']['domain'],
                Server::isHttps(),
                true
            );
        }

        @session_start();
    }

    private function isSessionActivated(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function close(): void
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
