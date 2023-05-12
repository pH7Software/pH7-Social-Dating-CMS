<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link             https://ph7builder.com
 * @package          PH7 / Install / Library
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use Smarty;

abstract class Controller implements Controllable
{
    public const SOFTWARE_NAME = 'pH7Builder';
    public const DEFAULT_SITE_NAME = 'My Dating WebApp';
    public const DEFAULT_ADMIN_USERNAME = 'administrator';
    public const SOFTWARE_PREFIX_COOKIE_NAME = 'pH7';
    public const SOFTWARE_WEBSITE = 'https://ph7builder.com';
    public const SOFTWARE_REQUIREMENTS_URL = 'https://ph7builder.com/doc/en/requirements';
    public const PAYPAL_DONATE_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X457W3L7DAPC6';
    public const PATREON_URL = 'https://www.patreon.com/bePatron?u=3534366';
    public const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    public const AUTHOR_URL = 'https://github.com/pH-7';
    public const SOFTWARE_GIT_REPO_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    public const SOFTWARE_TWITTER = '@pH7Soft';
    public const SOFTWARE_COPYRIGHT = '© (c) 2012-%s, Pierre-Henry Soria. All Rights Reserved.';

    public const SOFTWARE_VERSION_NAME = 'SENSATION';
    public const SOFTWARE_VERSION = '18.0.0';

    public const SOFTWARE_BUILD = '1';

    public const DEFAULT_LANG = 'en';
    public const DEFAULT_THEME = 'base';

    private const PHP_TIMEZONE_DIRECTIVE = 'date.timezone';
    private const VIEW_CACHE_LIFETIME = 24 * 3600; //thanks PHP5.6 for scalar expr in consts
    private const TOTAL_INSTALL_STEPS = 7;

    protected Smarty $oView;

    protected string $sCurrentLang;

    public function __construct()
    {
        global $LANG;

        // Initialize PHP session
        $this->initializePHPSession();

        // Verify and correct the time zone if necessary
        $this->checkTimezone();

        // Language initialization
        $this->sCurrentLang = (new Language)->get();
        include_once PH7_ROOT_INSTALL . Language::LANG_FOLDER_NAME . $this->sCurrentLang . PH7_DS . Language::LANG_FILENAME;

        /* Smarty initialization */
        $this->oView = new Smarty;
        $this->oView->setUseSubDirs(true);
        $this->oView->setTemplateDir(PH7_ROOT_INSTALL . 'views/' . self::DEFAULT_THEME);
        $this->oView->setCompileDir(PH7_ROOT_INSTALL . 'data/caches/smarty_compile');
        $this->oView->setCacheDir(PH7_ROOT_INSTALL . 'data/caches/smarty_cache');

        // Smarty Cache
        $this->oView->setCaching(Smarty::CACHING_OFF);
        $this->oView->setCacheLifetime(self::VIEW_CACHE_LIFETIME);

        $this->oView->assign('LANG', $LANG);
        $this->oView->assign('software_name', self::SOFTWARE_NAME);
        $this->oView->assign('software_version', self::SOFTWARE_VERSION . ' ' . self::SOFTWARE_VERSION_NAME . ' - Build ' . self::SOFTWARE_BUILD);
        $this->oView->assign('software_website', self::SOFTWARE_WEBSITE);
        $this->oView->assign('paypal_donate_url', self::PAYPAL_DONATE_URL);
        $this->oView->assign('patreon_url', self::PATREON_URL);
        $this->oView->assign('software_author', self::SOFTWARE_AUTHOR);
        $this->oView->assign('software_copyright', sprintf(self::SOFTWARE_COPYRIGHT, date('Y')));
        $this->oView->assign('tpl_name', self::DEFAULT_THEME);
        $this->oView->assign('current_lang', $this->sCurrentLang);
        $this->oView->assign('total_install_steps', self::TOTAL_INSTALL_STEPS);
    }

    /**
     * Check if the session is already initialized (thanks to "session_status()" PHP >= 5.4).
     * And initialize it if it isn't the case.
     */
    protected function initializePHPSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    /**
     * Set a default timezone if it is not already configured.
     */
    protected function checkTimezone(): void
    {
        if (!ini_get(self::PHP_TIMEZONE_DIRECTIVE)) {
            date_default_timezone_set(PH7_DEFAULT_TIMEZONE);
        }
    }
}
