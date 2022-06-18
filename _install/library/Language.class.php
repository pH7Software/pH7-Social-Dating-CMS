<?php
/**
 * @title            Language Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Library
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

class Language
{
    public const LANG_FILENAME = 'install.lang.php';
    public const LANG_FOLDER_NAME = 'langs/';

    private const ISO_LANG_CODE_LENGTH = 2;
    private const REQUEST_PARAM_NAME = 'l';

    private string $sLang;

    public function __construct()
    {
        if ($this->doesUserLangExist()) {
            $this->sLang = $_GET[self::REQUEST_PARAM_NAME];
            $this->createCookie($this->sLang);
        } elseif ($this->doesCookieLangExist()) {
            $this->sLang = $_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang'];
        } elseif ($this->doesBrowserLangExist()) {
            $this->sLang = $this->getBrowser();
        } else {
            $this->sLang = Controller::DEFAULT_LANG;
        }
    }

    /**
     * Get the language of the client browser.
     *
     * @return string|null First two letters of the languages of the client browser.
     */
    public function getBrowser(): ?string
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        $sLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'])[0];

        return htmlspecialchars(
            strtolower(
                substr(
                    rtrim($sLang),
                    0,
                    self::ISO_LANG_CODE_LENGTH
                )
            ),
            ENT_QUOTES
        );
    }

    /**
     * Gives the correct chosen language (e.g., fr, en, es, ...).
     */
    public function get(): string
    {
        return $this->sLang;
    }

    private function doesUserLangExist(): bool
    {
        return !empty($_GET[self::REQUEST_PARAM_NAME]) &&
            is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $_GET[self::REQUEST_PARAM_NAME] . PH7_DS . self::LANG_FILENAME);
    }

    private function doesCookieLangExist(): bool
    {
        return isset($_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang']) &&
            is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang'] . PH7_DS . self::LANG_FILENAME);
    }

    private function doesBrowserLangExist(): bool
    {
        return is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $this->getBrowser() . PH7_DS . self::LANG_FILENAME);
    }

    private function createCookie(string $sCookieValue): void
    {
        setcookie(
            Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang',
            $sCookieValue,
            time() + 60 * 60 * 24 * 365,
            '',
            '',
            false,
            true
        );
    }
}
