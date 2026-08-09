<?php

/**
 * @desc             Handler Session
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Framework\Session;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Server\Server;

class Session
{
    private const DEFAULT_PREFIX = '';
    private const DEFAULT_COOKIE_NAME = 'PHPSESSID';
    private const DEFAULT_EXPIRATION = 0;
    private const DEFAULT_PATH = PH7_SH;

    /**
     * @param bool|null $bDisableSessionCache disable PHP's session cache
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

    public function __destruct()
    {
        // $this->close();
    }

    private function __clone()
    {
    }

    /**
     * Set a PHP session.
     *
     * @param array|string $mName  name of the session
     * @param string|null  $sValue value of the session, Optional if the session data is in an array
     */
    public function set($mName, $sValue = null): void
    {
        $sPrefix = $this->getPrefix();

        if (is_array($mName)) {
            foreach ($mName as $sName => $sVal) {
                $this->set($sName, $sVal);
            }
        } else {
            $_SESSION[$sPrefix . $mName] = $sValue;
        }
    }

    /**
     * Get a session value by giving its name.
     *
     * @param string $sName name of the session
     *
     * @return mixed If the session exists, returns the session with function escape() (htmlspecialchars) if escape is enabled. Empty string value if the session doesn't exist.
     */
    public function get(string $sName, ?bool $bEscape = true)
    {
        $sSessionName = $this->getPrefix() . $sName;

        return isset($_SESSION[$sSessionName]) ? ($bEscape && is_string($_SESSION[$sSessionName]) ? escape($_SESSION[$sSessionName]) : $_SESSION[$sSessionName]) : '';
    }

    /**
     * Returns a boolean informing if the session exists or not.
     *
     * @param array|string $mName name of the session
     */
    public function exists($mName): bool
    {
        $bExists = false; // Default value
        $sPrefix = $this->getPrefix();

        if (is_array($mName)) {
            foreach ($mName as $sName) {
                if (!$bExists = $this->exists($sName)) {
                    return false;
                }
            }
        } else {
            $bExists = isset($_SESSION[$sPrefix . $mName]);
        }

        return $bExists;
    }

    /**
     * Delete the session(s) if the session exists.
     *
     * @param array|string $mName name of the session to delete
     */
    public function remove($mName): void
    {
        $sPrefix = $this->getPrefix();

        if (is_array($mName)) {
            foreach ($mName as $sName) {
                $this->remove($sName);
            }
        } else {
            $sSessionName = $sPrefix . $mName;

            // We put the session in a table so if the session is in the form of multi-dimensional array, it is clear how much is destroyed
            $_SESSION[$sSessionName] = [];
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
        $aConfig = $this->getConfig();
        ini_set('session.use_strict_mode', '1');
        session_name((string)($aConfig['cookie_name'] ?? self::DEFAULT_COOKIE_NAME));

        $iTime = (int)($aConfig['expiration'] ?? self::DEFAULT_EXPIRATION);
        session_set_cookie_params([
            'lifetime' => $iTime,
            'path' => (string)($aConfig['path'] ?? self::DEFAULT_PATH),
            'domain' => Server::getCookieDomain(),
            'secure' => Server::isHttps(),
            'httponly' => true,
            // Lax (not Strict) so links from emails/social networks keep users logged in.
            'samesite' => 'Lax'
        ]);

        @session_start();
        if (!$this->isSessionActivated()) {
            throw new \RuntimeException('The application could not start a PHP session. Check session.save_path permissions.');
        }
    }

    private function isSessionActivated(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    private function getPrefix(): string
    {
        return (string)($this->getConfig()['prefix'] ?? self::DEFAULT_PREFIX);
    }

    private function getConfig(): array
    {
        return Config::getInstance()->values['session'] ?? [];
    }

    private function close(): void
    {
        session_write_close();
    }
}
