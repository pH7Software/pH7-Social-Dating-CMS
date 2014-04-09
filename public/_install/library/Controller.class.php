<?php
/**
 * @title            Controller Core Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://software.hizup.com
 * @package          PH7 / Install / Library
 * @version          1.0
 */

namespace PH7;
defined('PH7') or die('Restricted access');

abstract class Controller implements IController
{

    const
    SOFTWARE_NAME = 'pH7CMS',
    SOFTWARE_PREFIX_COOKIE_NAME = 'pH7',
    SOFTWARE_WEBSITE = 'http://software.hizup.com',
    SOFTWARE_REGISTRATION_URL = 'http://software.hizup.com/register',
    SOFTWARE_LICENSE_URL = 'http://software.hizup.com/legal/license',
    SOFTWARE_HELP_URL = 'https://hizup.net/clients/support', // Help Desk URL
    SOFTWARE_LICENSE_KEY_URL = 'http://software.hizup.com/web/buysinglelicense',
    SOFTWARE_DOWNLOAD_URL = 'http://download.hizup.com/',
    SOFTWARE_REQUIREMENTS_URL = 'http://software.hizup.com/doc/en/requirements',
    SOFTWARE_HOSTING_LIST_URL = 'http://software.hizup.com/doc/en/hosting',
    SOFTWARE_HOSTING_LIST_FR_URL = 'http://software.hizup.com/doc/fr/h%C3%A9bergement-web',
    SOFTWARE_EMAIL = 'ph7software@gmail.com',
    SOFTWARE_AUTHOR = 'Pierre-Henry Soria',
    SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.',
    SOFTWARE_COPYRIGHT = '© (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.',
    SOFTWARE_VERSION_NAME = 'pOH',
    SOFTWARE_VERSION = '1.1.0',
    SOFTWARE_BUILD = '1',
    DEFAULT_LANG = 'en',
    DEFAULT_THEME = 'base';

    protected $sCurrentLang;

    public function __construct ()
    {
        global $LANG;

        // PHP session initialization
        if (empty($_SESSION))
            @session_start();

        // Verify and correct the time zone if necessary
        if (!ini_get('date.timezone'))
            date_default_timezone_set(PH7_DEFAULT_TIMEZONE);

        // Language initialization
        $this->sCurrentLang = (new Language)->get();
        include_once PH7_ROOT_INSTALL . 'langs/' . $this->sCurrentLang . '/install.lang.php';

        /* Smarty initialization */
        $this->view = new \Smarty;
        $this->view->use_sub_dirs = true;
        $this->view->setTemplateDir(PH7_ROOT_INSTALL . 'views/' . self::DEFAULT_THEME);
        $this->view->setCompileDir(PH7_ROOT_INSTALL . 'data/caches/smarty_compile');
        $this->view->setCacheDir(PH7_ROOT_INSTALL  . 'data/caches/smarty_cache');
        $this->view->setPluginsDir( PH7_ROOT_INSTALL . 'library/Smarty/plugins');
        // Smarty Cache
        $this->view->caching = 0; // 0 = Cache disabled |  1 = Cache never expires | 2 = Set the cache duration at "cache_lifetime" attribute
        $this->view->cache_lifetime = 86400; // 86400 seconds = 24h

        $this->view->assign('content', '');
        $this->view->assign('LANG', $LANG);
        $this->view->assign('software_name', self::SOFTWARE_NAME);
        $this->view->assign('software_version', self::SOFTWARE_VERSION . ' Build ' . self::SOFTWARE_BUILD . ' - ' . self::SOFTWARE_VERSION_NAME);
        $this->view->assign('software_website', self::SOFTWARE_WEBSITE);
        $this->view->assign('software_license_url', self::SOFTWARE_LICENSE_URL);
        $this->view->assign('software_help_url', self::SOFTWARE_HELP_URL);
        $this->view->assign('software_license_key_url', self::SOFTWARE_LICENSE_KEY_URL);
        $this->view->assign('software_author', self::SOFTWARE_AUTHOR);
        $this->view->assign('software_email', self::SOFTWARE_EMAIL);
        $this->view->assign('tpl_name', self::DEFAULT_THEME);
        $this->view->assign('current_lang', $this->sCurrentLang);
    }

}
