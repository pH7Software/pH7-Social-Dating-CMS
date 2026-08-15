<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria and pH7Builder contributors.
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
    public const DEFAULT_SITE_NAME = 'My Dating Community';
    public const DEFAULT_ADMIN_USERNAME = 'administrator';
    public const SOFTWARE_PREFIX_COOKIE_NAME = 'pH7';
    public const SOFTWARE_WEBSITE = 'https://ph7builder.com';
    public const SOFTWARE_REQUIREMENTS_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/README.md#requirements';
    public const SOFTWARE_LOCKDOWN_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/docs/QUICK_START.md#6-lock-the-installation-down';
    public const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    public const AUTHOR_URL = 'https://github.com/pH-7';
    public const SOFTWARE_GIT_REPO_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    public const SOFTWARE_COPYRIGHT = 'Copyright © 2012-%s Pierre-Henry Soria and pH7Builder contributors.';

    public const SOFTWARE_VERSION_NAME = 'REVOLUTIONARY™';
    public const SOFTWARE_VERSION = '18.6.1';

    public const SOFTWARE_BUILD = '1';

    public const DEFAULT_LANG = 'en';
    public const DEFAULT_THEME = 'base';

    private const PHP_TIMEZONE_DIRECTIVE = 'date.timezone';
    private const VIEW_CACHE_LIFETIME = 24 * 3600; //thanks PHP5.6 for scalar expr in consts
    private const TOTAL_INSTALL_STEPS = 7;
    private const ACTION_TOKEN_SESSION_KEY = 'install_action_token';
    private const ACCESS_SESSION_KEY = 'installer_authenticated';
    private const ACCESS_TOKEN_FILE = 'data/caches/install-token.hash';

    protected Smarty $oView;

    protected string $sCurrentLang;

    public function __construct()
    {
        global $LANG;

        // Initialize PHP session
        $this->initializePHPSession();
        $this->restoreInstallProgress();

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
        $this->oView->assign('software_author', self::SOFTWARE_AUTHOR);
        $this->oView->assign('software_copyright', sprintf(self::SOFTWARE_COPYRIGHT, date('Y')));
        $this->oView->assign('tpl_name', self::DEFAULT_THEME);
        $this->oView->assign('current_lang', $this->sCurrentLang);
        $this->oView->assign('total_install_steps', self::TOTAL_INSTALL_STEPS);
        $this->oView->assign('action_token', $this->getActionToken());
    }

    /**
     * Check if the session is already initialized (thanks to "session_status()" PHP >= 5.4).
     * And initialize it if it isn't the case.
     */
    protected function initializePHPSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', '1');
            session_set_cookie_params(
                [
                    'lifetime' => 0,
                    'path' => parse_url(PH7_URL_INSTALL, PHP_URL_PATH) ?: '/',
                    'secure' => str_starts_with(PH7_URL_INSTALL, 'https://'),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
            @session_start();
            if (session_status() !== PHP_SESSION_ACTIVE) {
                throw new \RuntimeException(
                    'The installer could not start a PHP session. Check session.save_path permissions.'
                );
            }
        }
    }

    protected function isValidPostRequest(): bool
    {
        global $LANG;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return false;
        }

        $sSubmittedToken = $_POST['action_token'] ?? '';
        if (is_string($sSubmittedToken) && hash_equals($this->getActionToken(), $sSubmittedToken)) {
            return true;
        }

        http_response_code(400);
        $this->oView->assign('csrf_error', $LANG['invalid_action']);

        return false;
    }

    public function hasInstallationAccess(): bool
    {
        $sExpectedHash = $this->getInstallationAccessHash();
        $sAuthenticatedHash = $_SESSION[self::ACCESS_SESSION_KEY] ?? null;

        return $sExpectedHash !== null && is_string($sAuthenticatedHash) &&
            hash_equals($sExpectedHash, $sAuthenticatedHash);
    }

    protected function isInstallationAccessConfigured(): bool
    {
        return $this->getInstallationAccessHash() !== null;
    }

    protected function authenticateInstallationAccess(string $sToken): bool
    {
        $sExpectedHash = $this->getInstallationAccessHash();
        if ($sExpectedHash === null || strlen($sToken) < 32 ||
            !hash_equals($sExpectedHash, hash('sha256', $sToken))
        ) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION[self::ACCESS_SESSION_KEY] = $sExpectedHash;

        return true;
    }

    protected function markStepComplete(int $iStep, array $aContext = []): void
    {
        if (!save_install_state($iStep, $aContext)) {
            throw new \RuntimeException(
                'The installer could not save its progress. Check _install/data/caches permissions and try again.'
            );
        }

        $_SESSION['step' . $iStep] = 1;
    }

    private function getActionToken(): string
    {
        if (empty($_SESSION[self::ACTION_TOKEN_SESSION_KEY]) ||
            !is_string($_SESSION[self::ACTION_TOKEN_SESSION_KEY])
        ) {
            $_SESSION[self::ACTION_TOKEN_SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::ACTION_TOKEN_SESSION_KEY];
    }

    private function getInstallationAccessHash(): ?string
    {
        $mEnvironmentToken = getenv('PH7_INSTALL_TOKEN');
        if (is_string($mEnvironmentToken) && strlen($mEnvironmentToken) >= 32) {
            return hash('sha256', $mEnvironmentToken);
        }

        $sTokenPath = PH7_ROOT_INSTALL . self::ACCESS_TOKEN_FILE;
        $sTokenHash = is_readable($sTokenPath) ? trim((string)file_get_contents($sTokenPath)) : '';

        return preg_match('/^[a-f0-9]{64}$/D', $sTokenHash) === 1 ? $sTokenHash : null;
    }

    private function restoreInstallProgress(): void
    {
        $aState = get_install_state();
        $iCompletedStep = (int)$aState['completed_step'];
        for ($iStep = 2; $iStep <= 6; $iStep++) {
            unset($_SESSION['step' . $iStep]);
        }
        if ($iCompletedStep === 0 || isset($aState['recovered_from_step'])) {
            unset($_SESSION['db'], $_SESSION['val'], $_SESSION['sample_data_warning']);
        }

        for ($iStep = 2; $iStep <= $iCompletedStep; $iStep++) {
            $_SESSION['step' . $iStep] = 1;
        }

        $aContext = isset($aState['context']) && is_array($aState['context']) ? $aState['context'] : [];
        if ($iCompletedStep >= 4 && isset($aContext['database_prefix'])) {
            $_SESSION['db']['prefix'] = $aContext['database_prefix'];
        }
        if ($iCompletedStep >= 5 && isset($aContext['admin_login_email'])) {
            $_SESSION['val']['admin_login_email'] = $aContext['admin_login_email'];
        }
        if ($iCompletedStep >= 5 && isset($aContext['admin_username'])) {
            $_SESSION['val']['admin_username'] = $aContext['admin_username'];
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
