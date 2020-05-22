<?php
/**
 * @title            Language Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or die('Restricted access');

class Language
{
    const LANG_FILENAME = 'install.lang.php';
    const LANG_FOLDER_NAME = 'langs/';

    /** @var string */
    private $sLang;

    public function __construct()
    {
        if ($this->doesUserLangExist()) {
            setcookie(
                Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang',
                $_GET['l'],
                time() + 60 * 60 * 24 * 365,
                null,
                null,
                false,
                true
            );
            $this->sLang = $_GET['l'];
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
    public function getBrowser()
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        $sLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        return htmlspecialchars(
            strtolower(
                substr(
                    chop($sLang[0]),
                    0,
                    2
                )
            ),
            ENT_QUOTES
        );
    }

    /**
     * Gives the correct chosen language (e.g., fr, en, es).
     *
     * @return string
     */
    public function get()
    {
        return $this->sLang;
    }

    /**
     * @return bool
     */
    private function doesUserLangExist()
    {
        return !empty($_GET['l']) && is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $_GET['l'] . PH7_DS . self::LANG_FILENAME);
    }

    /**
     * @return bool
     */
    private function doesCookieLangExist()
    {
        return isset($_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang']) &&
            is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang'] . PH7_DS . self::LANG_FILENAME);
    }

    /**
     * @return bool
     */
    private function doesBrowserLangExist()
    {
        return is_file(PH7_ROOT_INSTALL . self::LANG_FOLDER_NAME . $this->getBrowser() . PH7_DS . self::LANG_FILENAME);
    }
}
