<?php
/**
 * @title            Language Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;
defined('PH7') or die('Restricted access');

class Language
{

    private $_sLang;

    public function __construct()
    {
        if (!empty($_GET['l']) && is_file(PH7_ROOT_INSTALL . 'langs/' . $_GET['l'] . '/install.lang.php')) {
            setcookie(Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang', $_GET['l'], time() + 60 * 60 * 24 * 365, null, null, false, true);
            $this->_sLang = $_GET['l'];
        } elseif (isset($_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang']) && is_file(PH7_ROOT_INSTALL . 'langs/' . $_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang'] . '/install.lang.php')) {
            $this->_sLang = $_COOKIE[Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang'];
        } elseif (is_file(PH7_ROOT_INSTALL . 'langs/' . $this->getBrowser() . '/install.lang.php')) {
            $this->_sLang = $this->getBrowser();
        } else {
            $this->_sLang = Controller::DEFAULT_LANG;
        }
    }

    /**
     * Get the language of the client browser.
     *
     * @return string First two letters of the languages ​​of the client browser.
     */
    public function getBrowser()
    {
        $sLang = explode(',', @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        return htmlspecialchars(strtolower(substr(chop($sLang[0]), 0, 2)), ENT_QUOTES);
    }

    public function get()
    {
        return $this->_sLang;
    }

}
