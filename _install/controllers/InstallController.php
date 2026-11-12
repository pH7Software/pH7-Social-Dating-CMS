<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Controller
 */

declare(strict_types=1);

namespace PH7;

use Throwable;

defined('PH7') or exit('Restricted access');

// Reset the time limit
@set_time_limit(0);

class InstallController extends Controller
{
    private const CORE_SQL_FILE = 'pH7_Core.sql';
    private const SQL_SCHEMA_VERSION = '1.6.6';
    private const INITIAL_ADMIN_MISMATCH_ERROR_CODE = 180601;

    private const SAMPLE_USERS_MIN_AGE = 18;
    private const SAMPLE_USERS_MAX_AGE = 60;
    private const TOTAL_MEMBERS_SAMPLE = 16;
    private const TOTAL_AFFILIATES_SAMPLE = 1;
    private const TOTAL_SUBSCRIBERS_SAMPLE = 1;

    /**
     * Enable/Disable Modules according to the chosen niche
     */
    private const SOCIAL_MODS = [
        'affiliate' => '0',
        'chat' => '0',
        'picture' => '1',
        'video' => '1',
        'friend' => '1',
        'hotornot' => '0',
        'forum' => '1',
        'note' => '1',
        'blog' => '1',
        'newsletter' => '0',
        'invite' => '1',
        'love-calculator' => '0',
        'mail' => '1',
        'im' => '0',
        'user-dashboard' => '0',
        'cool-profile-page' => '0',
        'related-profile' => '1',
        'birthday' => '1',
        'map' => '1',
        'pwa' => '0',
        'sms-verification' => '0'
    ];

    private const DATING_MODS = [
        'affiliate' => '1',
        'chat' => '1',
        'picture' => '1',
        'video' => '0',
        'friend' => '0',
        'hotornot' => '1',
        'forum' => '0',
        'note' => '0',
        'blog' => '1',
        'newsletter' => '1',
        'invite' => '0',
        'love-calculator' => '1',
        'mail' => '1',
        'im' => '1',
        'user-dashboard' => '1',
        'cool-profile-page' => '1',
        'related-profile' => '1',
        'birthday' => '0',
        'map' => '1',
        'pwa' => '0',
        'sms-verification' => '0'
    ];

    /**
     * Enable/Disable Site Settings according to the chosen niche
     */
    private const SOCIAL_SETTINGS = [
        'navbarType' => 'default',
        'socialMediaWidgets' => '1',
        'requireRegistrationAvatar' => '0',
        'isUserAgeRangeField' => '0'
    ];

    private const DATING_SETTINGS = [
        'navbarType' => 'inverse',
        'socialMediaWidgets' => '0',
        'requireRegistrationAvatar' => '1',
        'isUserAgeRangeField' => '1'
    ];


    /********************* STEP 1 *********************/
    public function index(): void
    {
        global $LANG;

        if (!$this->hasInstallationAccess()) {
            $this->displayInstallationAccessForm();

            return;
        }

        $this->resumeInstallationIfNeeded();

        $aLangs = get_dir_list(PH7_ROOT_INSTALL . Language::LANG_FOLDER_NAME);
        $aLangsList = include PH7_ROOT_INSTALL . 'inc/lang_list.inc.php';
        $sLangSelect = '';

        foreach ($aLangs as $sLang) {
            $sSelectedAttr = (empty($_REQUEST['l']) && $sLang === $this->sCurrentLang || !empty($_REQUEST['l']) && $sLang === $_REQUEST['l']) ? '" selected="selected' : '';
            $sLangSelect .= '<option value="?l=' . $sLang . $sSelectedAttr . '">' . $aLangsList[$sLang] . '</option>';
        }

        $this->oView->assign('lang_select', $sLangSelect);
        $this->oView->assign('sept_number', 1);
        $this->oView->display('index.tpl');
    }

    /********************* STEP 2 *********************/
    public function license(): void
    {
        if ($this->isValidPostRequest() && !empty($_POST['license_agreements_submit'])) {
            if ($this->isAgreementsAgreed()) {
                $this->markStepComplete(2);

                redirect(PH7_URL_SLUG_INSTALL . 'config_path');
            } else {
                $this->oView->assign('failure', 1);
            }
        }

        $this->oView->assign('sept_number', 2);
        $this->oView->display('license.tpl');
    }

    /********************* STEP 3 *********************/
    public function config_path(): void
    {
        global $LANG;

        if (!empty($_SESSION['step3'])) {
            redirect(PH7_URL_SLUG_INSTALL . 'config_system');
        }

        if (!empty($_SESSION['step2'])) {
            $sConstantsPath = PH7_ROOT_PUBLIC . '_constants.php';
            $bReplaceStaleConstants = is_file($sConstantsPath) &&
                (int)get_install_state()['completed_step'] === 2;
            if (empty($_SESSION['val']['path_protected'])) {
                // If not set, set a default value for the field used in Smarty tpl
                $_SESSION['val']['path_protected'] = PH7_ROOT_PUBLIC . '_protected' . PH7_DS;
            }

            if ($this->isValidPostRequest() && !empty($_POST['path_protected']) && is_string($_POST['path_protected'])) {
                $sRealProtectedPath = realpath(rtrim(trim($_POST['path_protected']), '/\\'));
                $_SESSION['val']['path_protected'] = is_string($sRealProtectedPath)
                    ? check_ext_end($sRealProtectedPath)
                    : trim($_POST['path_protected']);

                if (!is_string($sRealProtectedPath) || !is_file($sRealProtectedPath . PH7_DS . 'app/configs/constants.php')) {
                    $aErrors[] = $LANG['no_protected_exist'];
                } elseif (!is_readable($sRealProtectedPath)) {
                    $aErrors[] = $LANG['no_protected_readable'];
                } elseif (($aUnwritablePaths = $this->getUnwritableApplicationPaths($sRealProtectedPath)) !== []) {
                    foreach ($aUnwritablePaths as $sPathDescription) {
                        $aErrors[] = sprintf($LANG['runtime_path_not_writable'], $sPathDescription);
                    }
                } else {
                    $sConstantTemplate = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/constants.php');
                    $aCanonicalUrl = parse_url(PH7_URL_ROOT);
                    $sCanonicalScheme = is_array($aCanonicalUrl) && isset($aCanonicalUrl['scheme'])
                        ? (string)$aCanonicalUrl['scheme'] . '://'
                        : '';
                    $sCanonicalHost = is_array($aCanonicalUrl) && isset($aCanonicalUrl['host'])
                        ? (string)$aCanonicalUrl['host']
                        : '';
                    if (str_contains($sCanonicalHost, ':') && !str_starts_with($sCanonicalHost, '[')) {
                        $sCanonicalHost = '[' . $sCanonicalHost . ']';
                    }
                    if (is_array($aCanonicalUrl) && isset($aCanonicalUrl['port'])) {
                        $sCanonicalHost .= ':' . (int)$aCanonicalUrl['port'];
                    }

                    $sConstantContent = is_string($sConstantTemplate) && $sCanonicalScheme !== '' && $sCanonicalHost !== ''
                        ? strtr(
                            $sConstantTemplate,
                            [
                                "'%path_protected%'" => var_export(check_ext_end($sRealProtectedPath), true),
                                "'%url_protocol%'" => var_export($sCanonicalScheme, true),
                                "'%domain%'" => var_export($sCanonicalHost, true)
                            ]
                        )
                        : false;
                    $sTemporaryConstantsPath = $sConstantsPath . '.installing-' . generate_hash(8);

                    if ($sConstantContent === false || is_file($sConstantsPath) && !$bReplaceStaleConstants ||
                        @file_put_contents($sTemporaryConstantsPath, $sConstantContent, LOCK_EX) === false
                    ) {
                        if (is_file($sTemporaryConstantsPath)) {
                            @unlink($sTemporaryConstantsPath);
                        }
                        $aErrors[] = $LANG['no_public_writable'];
                    } else {
                        @chmod($sTemporaryConstantsPath, 0600);

                        if (!$this->replaceConstantsFile(
                            $sTemporaryConstantsPath,
                            $sConstantsPath,
                            $bReplaceStaleConstants
                        )) {
                            @unlink($sTemporaryConstantsPath);
                            $aErrors[] = $LANG['no_public_writable'];
                        } else {
                            $this->markStepComplete(
                                3,
                                ['protected_path' => check_ext_end($sRealProtectedPath)]
                            );
                            unset($_SESSION['val']);

                            redirect(PH7_URL_SLUG_INSTALL . 'config_system');
                        }
                    }
                }
            }

            if ($bReplaceStaleConstants) {
                $this->oView->assign('stale_constants_recovery', $LANG['stale_constants_recovery']);
            }
        } else {
            redirect(PH7_URL_SLUG_INSTALL . 'license');
        }

        $this->oView->assign('sept_number', 3);

        if (!empty($aErrors) && is_array($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }

        $this->oView->display('config_path.tpl');
    }

    /********************* STEP 4 *********************/
    public function config_system(): void
    {
        global $LANG;

        if (!empty($_SESSION['step4'])) {
            redirect(PH7_URL_SLUG_INSTALL . 'config_site');
        }

        if (!empty($_SESSION['step3']) && is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
            session_regenerate_id(true);

            if (empty($_SESSION['val'])) {
                $_SESSION['db']['type_name'] = Database::DBMS_MYSQL_NAME;
                $_SESSION['db']['type'] = Database::DSN_MYSQL_PREFIX;

                $_SESSION['db']['hostname'] = DbDefaultConfig::HOSTNAME;
                $_SESSION['db']['username'] = DbDefaultConfig::USERNAME;
                $_SESSION['db']['name'] = DbDefaultConfig::NAME;
                $_SESSION['db']['prefix'] = DbDefaultConfig::PREFIX;
                $_SESSION['db']['port'] = DbDefaultConfig::PORT;
                $_SESSION['db']['charset'] = DbDefaultConfig::CHARSET;

                $_SESSION['val']['bug_report_email'] = '';
                $_SESSION['val']['ffmpeg_path'] = ffmpeg_path();
            }

            if ($this->isValidPostRequest() && !empty($_POST['config_system_submit'])) {
                $aRequiredFields = [
                    'db_hostname',
                    'db_username',
                    'db_password',
                    'db_name',
                    'db_prefix',
                    'db_port',
                    'bug_report_email'
                ];

                if (!$this->hasRequiredScalarFields($_POST, $aRequiredFields)) {
                    $aErrors[] = $LANG['all_fields_mandatory'];
                } else {
                    $_SESSION['db']['type_name'] = Database::DBMS_MYSQL_NAME;
                    $_SESSION['db']['type'] = Database::DSN_MYSQL_PREFIX;
                    $_SESSION['db']['hostname'] = trim($_POST['db_hostname']);
                    $_SESSION['db']['username'] = trim($_POST['db_username']);
                    $_SESSION['db']['password'] = $_POST['db_password'];
                    $_SESSION['db']['name'] = trim($_POST['db_name']);
                    $_SESSION['db']['prefix'] = trim($_POST['db_prefix']);
                    $_SESSION['db']['port'] = trim($_POST['db_port']);
                    $_SESSION['db']['charset'] = DbDefaultConfig::CHARSET;
                    $_SESSION['val']['bug_report_email'] = trim($_POST['bug_report_email']);
                    $_SESSION['val']['ffmpeg_path'] = isset($_POST['ffmpeg_path']) && is_string($_POST['ffmpeg_path'])
                        ? trim($_POST['ffmpeg_path'])
                        : '';

                    if (!$this->hasValidDatabaseSettings($_SESSION['db'])) {
                        $aErrors[] = $LANG['invalid_database_settings'];
                    } elseif (!validate_email($_SESSION['val']['bug_report_email'])) {
                        $aErrors[] = $LANG['bad_email'];
                    } elseif (!$this->hasValidFfmpegPath($_SESSION['val']['ffmpeg_path'])) {
                        $aErrors[] = $LANG['invalid_ffmpeg_path'];
                    } else {
                        $sTemporaryConfigPath = '';

                        try {
                            require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';
                            @require_once PH7_ROOT_PUBLIC . '_constants.php';
                            @require_once PH7_PATH_APP . 'configs/constants.php';

                            if (!self::isSupportedMySqlServer(
                                (string)$DB->getAttribute(\PDO::ATTR_DRIVER_NAME),
                                (string)$DB->getAttribute(\PDO::ATTR_SERVER_VERSION)
                            )) {
                                $aErrors[] = $LANG['require_mysql_version'];
                            } elseif (!$this->isDatabasePrefixAvailable($DB)) {
                                if ($this->canResumeCompletedDatabaseStep($DB)) {
                                    $this->finalizeDatabaseStep();
                                }

                                $aErrors[] = $LANG['error_sql_import'];
                            } else {
                                $sConfigContent = $this->buildConfigContent();
                                $sTemporaryConfigPath = PH7_PATH_APP_CONFIG . 'config.ini.installing-' . generate_hash(8);

                                if (!is_string($sConfigContent) ||
                                    @file_put_contents($sTemporaryConfigPath, $sConfigContent, LOCK_EX) === false
                                ) {
                                    $aErrors[] = $LANG['no_app_config_writable'];
                                } else {
                                    @chmod($sTemporaryConfigPath, 0600);
                                    ignore_user_abort(true);

                                    $mImportResult = exec_query_file(
                                        $DB,
                                        PH7_ROOT_INSTALL . 'data/sql/' . Database::DBMS_MYSQL_NAME . '/' . self::CORE_SQL_FILE
                                    );

                                    if ($mImportResult !== true) {
                                        error_log('Installer schema import failed: ' . print_r($mImportResult, true));
                                        $aErrors[] = $LANG['error_sql_import'];
                                    } elseif (!@rename($sTemporaryConfigPath, PH7_PATH_APP_CONFIG . 'config.ini')) {
                                        error_log('Installer could not finalize protected/app/configs/config.ini.');
                                        $aErrors[] = $LANG['no_app_config_writable'];
                                    } else {
                                        $sTemporaryConfigPath = '';
                                        $this->finalizeDatabaseStep();
                                    }
                                }
                            }
                        } catch (Throwable $oE) {
                            error_log((string)$oE);
                            $aErrors[] = $LANG['database_error'];
                        } finally {
                            if ($sTemporaryConfigPath !== '' && is_file($sTemporaryConfigPath)) {
                                @unlink($sTemporaryConfigPath);
                            }
                        }
                    }
                }
            }
        } else {
            redirect(PH7_URL_SLUG_INSTALL . 'config_path');
        }

        // Assign the sample DB config values to the template
        $this->oView->assign('def_db_hostname', DbDefaultConfig::HOSTNAME);
        $this->oView->assign('def_db_username', DbDefaultConfig::USERNAME);
        $this->oView->assign('def_db_name', DbDefaultConfig::NAME);
        $this->oView->assign('def_db_prefix', DbDefaultConfig::PREFIX);
        $this->oView->assign('def_db_port', DbDefaultConfig::PORT);
        $this->oView->assign('def_db_charset', DbDefaultConfig::CHARSET);

        $this->oView->assign('sept_number', 4);

        if (!empty($aErrors) && is_array($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }

        $this->oView->display('config_system.tpl');
    }

    /********************* STEP 5 *********************/
    public function config_site(): void
    {
        global $LANG;

        if (!empty($_SESSION['step5'])) {
            redirect(PH7_URL_SLUG_INSTALL . 'niche');
        }

        if (empty($_SESSION['step4']) || !is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
            redirect(PH7_URL_SLUG_INSTALL . 'config_system');
        }

        session_regenerate_id(true);

        if (empty($_SESSION['val'])) {
            $_SESSION['val'] = [
                'site_name' => self::DEFAULT_SITE_NAME,
                'admin_login_email' => '',
                'admin_email' => '',
                'admin_feedback_email' => '',
                'admin_return_email' => '',
                'admin_username' => self::DEFAULT_ADMIN_USERNAME,
                'admin_first_name' => '',
                'admin_last_name' => ''
            ];
        }

        if ($this->isValidPostRequest() && !empty($_POST['config_site_submit'])) {
            $aRequiredFields = [
                'site_name',
                'admin_login_email',
                'admin_email',
                'admin_feedback_email',
                'admin_return_email',
                'admin_username',
                'admin_password',
                'admin_passwords',
                'admin_first_name',
                'admin_last_name'
            ];

            if (!$this->hasRequiredScalarFields($_POST, $aRequiredFields)) {
                $aErrors[] = $LANG['all_fields_mandatory'];
            } else {
                foreach (array_diff($aRequiredFields, ['admin_password', 'admin_passwords']) as $sField) {
                    $_SESSION['val'][$sField] = trim($_POST[$sField]);
                }

                $sAdminPassword = $_POST['admin_password'];
                $sAdminPasswordConfirmation = $_POST['admin_passwords'];
                $iUsernameStatus = validate_username($_SESSION['val']['admin_username']);
                $iPasswordStatus = validate_password($sAdminPassword);

                if (mb_strlen($_SESSION['val']['site_name']) < 2 || mb_strlen($_SESSION['val']['site_name']) > 50) {
                    $aErrors[] = $LANG['bad_site_name'];
                } elseif (!validate_email($_SESSION['val']['admin_login_email']) ||
                    !validate_email($_SESSION['val']['admin_email']) ||
                    !validate_email($_SESSION['val']['admin_feedback_email']) ||
                    !validate_email($_SESSION['val']['admin_return_email'])
                ) {
                    $aErrors[] = $LANG['bad_email'];
                } elseif ($iUsernameStatus !== 0) {
                    $aErrors[] = $this->getUsernameValidationMessage($iUsernameStatus);
                } elseif ($iPasswordStatus !== 0) {
                    $aErrors[] = $this->getPasswordValidationMessage($iPasswordStatus);
                } elseif (!validate_identical($sAdminPassword, $sAdminPasswordConfirmation)) {
                    $aErrors[] = $LANG['passwords_different'];
                } elseif (find($sAdminPassword, $_SESSION['val']['admin_username']) ||
                    find($sAdminPassword, $_SESSION['val']['admin_first_name']) ||
                    find($sAdminPassword, $_SESSION['val']['admin_last_name'])
                ) {
                    $aErrors[] = $LANG['insecure_password'];
                } elseif (!validate_name($_SESSION['val']['admin_first_name'])) {
                    $aErrors[] = $LANG['bad_first_name'];
                } elseif (!validate_name($_SESSION['val']['admin_last_name'])) {
                    $aErrors[] = $LANG['bad_last_name'];
                } else {
                    $this->initializeClasses();

                    try {
                        ignore_user_abort(true);
                        require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';
                        $DB->beginTransaction();
                        if (!$this->doesInitialAdminMatch($DB, $sAdminPassword)) {
                            $this->persistInitialSite($DB, $sAdminPassword);
                        }
                        $DB->commit();
                        $this->markStepComplete(
                            5,
                            [
                                'database_prefix' => $_SESSION['db']['prefix'],
                                'admin_login_email' => $_SESSION['val']['admin_login_email'],
                                'admin_username' => $_SESSION['val']['admin_username']
                            ]
                        );

                        if (!empty($_POST['sample_data_request'])) {
                            $this->tryToPopulateSampleData();
                        }

                        redirect(PH7_URL_SLUG_INSTALL . 'niche');
                    } catch (Throwable $oE) {
                        if (isset($DB) && $DB->inTransaction()) {
                            $DB->rollBack();
                        }
                        error_log((string)$oE);
                        $aErrors[] = $oE->getCode() === self::INITIAL_ADMIN_MISMATCH_ERROR_CODE
                            ? $LANG['initial_admin_mismatch']
                            : $LANG['database_error'];
                    }
                }
            }
        }

        $this->oView->assign('def_site_name', self::DEFAULT_SITE_NAME);
        $this->oView->assign('def_admin_username', self::DEFAULT_ADMIN_USERNAME);
        $this->oView->assign('sept_number', 5);

        if (!empty($aErrors) && is_array($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }

        $this->oView->display('config_site.tpl');
    }

    /********************* STEP 6 *********************/
    public function niche(): void
    {
        global $LANG;

        if (!empty($_SESSION['step6'])) {
            redirect(PH7_URL_SLUG_INSTALL . 'finish');
        }

        if (empty($_SESSION['step5']) || !is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
            redirect(PH7_URL_SLUG_INSTALL . 'config_site');
        }

        session_regenerate_id(true);
        $aErrors = [];

        if (!empty($_SESSION['sample_data_warning']) && is_string($_SESSION['sample_data_warning'])) {
            $aErrors[] = $_SESSION['sample_data_warning'];
            unset($_SESSION['sample_data_warning']);
        }

        if ($this->isValidPostRequest() && !empty($_POST['niche_submit'])) {
            $sNiche = is_string($_POST['niche_submit']) ? $_POST['niche_submit'] : '';
            $bValidNiche = true;

            if ($sNiche === 'base') {
                $this->markStepComplete(6);
                redirect(PH7_URL_SLUG_INSTALL . 'finish');
            }

            if ($sNiche === 'zendate') {
                $sThemeName = 'zendate';
                $aModUpdate = self::SOCIAL_MODS;
                $aSettingUpdate = self::SOCIAL_SETTINGS;
            } elseif ($sNiche === 'datelove') {
                $sThemeName = 'datelove';
                $aModUpdate = self::DATING_MODS;
                $aSettingUpdate = self::DATING_SETTINGS;
            } else {
                $aErrors[] = $LANG['invalid_niche'];
                $bValidNiche = false;
            }

            if ($bValidNiche) {
                $this->initializeClasses();

                try {
                    require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';
                    $DB->beginTransaction();

                    foreach ($aModUpdate as $sModName => $sStatus) {
                        if (!$this->updateMods($DB, $sModName, $sStatus)) {
                            throw new \RuntimeException('The installer could not update a module status.');
                        }
                    }

                    $this->updateSettings($DB, $aSettingUpdate);

                    if (!$this->updateTheme($DB, $sThemeName)) {
                        throw new \RuntimeException('The installer could not update the selected theme.');
                    }

                    $DB->commit();
                    $this->markStepComplete(6);
                    redirect(PH7_URL_SLUG_INSTALL . 'finish');
                } catch (Throwable $oE) {
                    if (isset($DB) && $DB->inTransaction()) {
                        $DB->rollBack();
                    }
                    error_log((string)$oE);
                    $aErrors[] = $LANG['database_error'];
                }
            }
        }

        $this->oView->assign('sept_number', 6);

        if (!empty($aErrors) && is_array($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }

        $this->oView->display('niche.tpl');
    }

    /********************* STEP 7 *********************/
    public function finish(): void
    {
        global $LANG;

        $sConstantsPath = PH7_ROOT_PUBLIC . '_constants.php';
        if (!is_file($sConstantsPath)) {
            redirect(PH7_URL_SLUG_INSTALL . 'config_path');
        }

        if (empty($_SESSION['step6'])) {
            redirect(PH7_URL_SLUG_INSTALL . 'niche');
        }

        @require_once $sConstantsPath;
        $aErrors = [];

        if ($this->canEmailBeSent()) {
            $this->oView->assign('admin_login_email', $_SESSION['val']['admin_login_email']);
            $this->oView->assign('admin_username', $_SESSION['val']['admin_username']);

            if (empty($_SESSION['welcome_email_attempted'])) {
                $_SESSION['welcome_email_attempted'] = 1;
                try {
                    $bEmailSent = $this->sendWelcomeEmail();
                } catch (Throwable $oE) {
                    error_log((string)$oE);
                    $bEmailSent = false;
                }

                if (!$bEmailSent) {
                    $aErrors[] = $LANG['welcome_email_warning'];
                }
            }
        }

        if ($this->isValidPostRequest() && !empty($_POST['confirm_remove_install'])) {
            if (remove_install_dir()) {
                $this->removeCookies();
                $this->removeSessions();
                clearstatcache();
                redirect(PH7_URL_ROOT);
            }

            $aErrors[] = $LANG['remove_install_failed'];
        }

        $this->oView->assign('sept_number', 7);
        if (!empty($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }
        $this->oView->display('finish.tpl');
    }

    /**
     * Send an email to say the installation is now done, and give some information...
     */
    private function sendWelcomeEmail(): bool
    {
        global $LANG;

        $aParams = [
            'to' => $_SESSION['val']['admin_login_email'],
            'subject' => $LANG['title_email_finish_install'],
            'body' => $LANG['content_email_finish_install']
        ];

        return send_mail($aParams);
    }

    private function displayInstallationAccessForm(): void
    {
        global $LANG;

        $bAccessConfigured = $this->isInstallationAccessConfigured();
        if ($this->isValidPostRequest() && !empty($_POST['install_access_submit'])) {
            $sToken = isset($_POST['install_access_token']) && is_string($_POST['install_access_token'])
                ? $_POST['install_access_token']
                : '';

            if ($this->authenticateInstallationAccess($sToken)) {
                redirect(PH7_URL_SLUG_INSTALL . 'index');
            }

            http_response_code(403);
            $this->oView->assign('install_access_error', $LANG['install_access_invalid']);
        }

        $this->oView->assign('install_access_required', true);
        $this->oView->assign('install_access_configured', $bAccessConfigured);
        $this->oView->assign('sept_number', 1);
        $this->oView->display('index.tpl');
    }

    private function resumeInstallationIfNeeded(): void
    {
        $iCompletedStep = (int)get_install_state()['completed_step'];
        $aNextActions = [
            2 => 'config_path',
            3 => 'config_system',
            4 => 'config_site',
            5 => 'niche',
            6 => 'finish'
        ];

        if (isset($aNextActions[$iCompletedStep])) {
            redirect(PH7_URL_SLUG_INSTALL . $aNextActions[$iCompletedStep]);
        }
    }

    /**
     * Verify if the email can be sent (has all necessary global variables)
     * to assure only one email is send and not multiple ones.
     *
     * @return bool
     */
    private function canEmailBeSent(): bool
    {
        return !empty($_SESSION['val']['admin_login_email']) &&
            !empty($_SESSION['val']['admin_username']);
    }

    /**
     * Update module status (enabled/disabled).
     *
     * @param Database $oDb
     * @param string $sModName Module Name.
     * @param string $sStatus '1' = Enabled | '0' = Disabled (need to be string because in DB it is an "enum").
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    private function updateMods(Database $oDb, string $sModName, string $sStatus)
    {
        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::UPDATE_SYS_MODULE, $_SESSION['db']['prefix'] . DbTableName::SYS_MOD_ENABLED)
        );

        return $rStmt->execute(['modName' => $sModName, 'status' => $sStatus]);
    }

    /**
     * Set the adequate website's theme for the chosen niche.
     *
     * @param Database $oDb
     * @param string $sThemeName
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    private function updateTheme(Database $oDb, string $sThemeName)
    {
        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::UPDATE_THEME, $_SESSION['db']['prefix'] . DbTableName::SETTING)
        );

        return $rStmt->execute(['theme' => $sThemeName, 'setting' => 'defaultTemplate']);
    }

    /**
     * @param Database $oDb
     * @param array $aParams
     */
    private function updateSettings(Database $oDb, array $aParams): void
    {
        foreach ($aParams as $sName => $sValue) {
            $rStmt = $oDb->prepare(
                sprintf(SqlQuery::UPDATE_SETTING, $_SESSION['db']['prefix'] . DbTableName::SETTING)
            );
            if (!$rStmt->execute(['settingValue' => $sValue, 'settingName' => $sName])) {
                throw new \RuntimeException('The installer could not update a site setting.');
            }

            if ($sName === 'socialMediaWidgets') {
                $rStmt = $oDb->prepare(
                    sprintf(
                        SqlQuery::UPDATE_STATIC_FILE_STATUS,
                        $_SESSION['db']['prefix'] . DbTableName::STATIC_FILE
                    )
                );
                if (!$rStmt->execute(['status' => $sValue])) {
                    throw new \RuntimeException('The installer could not update the social widget asset.');
                }
            }
        }
    }

    /**
     * Populates some sample user profiles with Faker library.
     *
     * @param int $iMemberNumber The number of members to generate.
     * @param int $iAffiliateNumber The number of affiliates to generate (usually less than members).
     * @param int $iSubscriberNumber The number of subscribers to generate (for newsletter module).
     *
     * @return void
     *
     * @throws Framework\Translate\Exception
     */
    private function populateSampleUserData(int $iMemberNumber, int $iAffiliateNumber, int $iSubscriberNumber): void
    {
        (new Framework\Translate\Lang)
            ->setDefaultLang('en_US')
            ->init();

        // Initialize the site's database for "UserCoreModel" and "AffiliateCoreModel" classes
        Framework\Mvc\Router\FrontController::getInstance()->_initializeDatabase();

        $oUserModel = new UserCoreModel;
        $oAffModel = new AffiliateCoreModel;
        $oSubscriberModel = new SubscriberCoreModel;
        $oFaker = \Faker\Factory::create();

        for ($iProfile = 1; $iProfile <= $iMemberNumber; $iProfile++) {
            $sSex = $oFaker->randomElement(['male', 'female']);
            $sMatchSex = $oFaker->randomElement(['male', 'female', 'couple']);
            $sBirthDate = $oFaker->dateTimeBetween(sprintf('-%s years', self::SAMPLE_USERS_MAX_AGE), sprintf('-%s years', self::SAMPLE_USERS_MIN_AGE))->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = str_replace(['.', ' '], '-', $oFaker->userName);
            $aUser['email'] = $oFaker->email;
            $aUser['first_name'] = $oFaker->firstName;
            $aUser['last_name'] = $oFaker->lastName;
            $aUser['password'] = $oFaker->password;
            $aUser['sex'] = $sSex;
            $aUser['match_sex'] = [$sMatchSex];
            $aUser['country'] = $oFaker->countryCode;
            $aUser['city'] = $oFaker->city;
            $aUser['address'] = $oFaker->streetAddress;
            $aUser['zip_code'] = $oFaker->postcode;
            $aUser['birth_date'] = $sBirthDate;
            $aUser['description'] = $oFaker->paragraph(2);
            $aUser['lang'] = $oFaker->locale;
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);

            if ($iProfile <= $iAffiliateNumber) {
                // Specific data only for affiliates
                $aUser['website'] = 'https://pierrehenry.be';
                $aUser['phone'] = $oFaker->phoneNumber;
                $aUser['bank_account'] = $oFaker->companyEmail;
                $oAffModel->add($aUser);
            }

            if ($iProfile <= $iSubscriberNumber) {
                // Specific data only for subscribers
                $aUser['name'] = $oFaker->name;
                $aUser['active'] = $iAccountStatus = $oFaker->randomElement(
                    [
                        SubscriberCoreModel::ACTIVE_STATUS,
                        SubscriberCoreModel::INACTIVE_STATUS
                    ]
                );
                $aUser['current_date'] = $oFaker->dateTime()->format('Y-m-d H:i:s');
                $aUser['hash_validation'] = sha1($oFaker->password(20));
                $aUser['affiliated_id'] = 0;
                $oSubscriberModel->add($aUser);
            }
        }
    }

    /**
     * Check if the user has ticked the license agreement.
     */
    private function isAgreementsAgreed(): bool
    {
        return
            !empty($_POST['license_agreed']) &&
            !empty($_POST['conform_laws_agreed']) &&
            !empty($_POST['responsibilities_agreed']);
    }

    private function hasRequiredScalarFields(array $aValues, array $aRequiredFields): bool
    {
        foreach ($aRequiredFields as $sField) {
            if (!isset($aValues[$sField]) || !is_string($aValues[$sField]) || trim($aValues[$sField]) === '') {
                return false;
            }
        }

        return true;
    }

    private function hasValidDatabaseSettings(array $aDatabase): bool
    {
        $sPort = $aDatabase['port'];

        return preg_match('/^[a-z0-9.\-:\[\]]{1,255}$/i', $aDatabase['hostname']) === 1 &&
            preg_match('/^[^\x00-\x1f\x7f]{1,128}$/u', $aDatabase['username']) === 1 &&
            preg_match('/^[^\x00-\x1f\x7f]{1,255}$/u', $aDatabase['password']) === 1 &&
            preg_match('/^[a-z0-9_$-]{1,64}$/i', $aDatabase['name']) === 1 &&
            preg_match('/^[a-z][a-z0-9_]{0,31}$/i', $aDatabase['prefix']) === 1 &&
            ctype_digit($sPort) &&
            (int)$sPort >= 1 &&
            (int)$sPort <= 65535 &&
            $aDatabase['charset'] === DbDefaultConfig::CHARSET;
    }

    private function hasValidFfmpegPath(string $sPath): bool
    {
        return $sPath === '' || is_file($sPath) && is_executable($sPath);
    }

    private function replaceConstantsFile(string $sTemporaryPath, string $sConstantsPath, bool $bReplace): bool
    {
        if (!$bReplace || !is_file($sConstantsPath)) {
            return @rename($sTemporaryPath, $sConstantsPath);
        }

        if (is_link($sConstantsPath)) {
            return false;
        }

        // POSIX rename replaces the destination atomically. Keep a rollback path
        // for platforms that require the old file to be moved first.
        if (@rename($sTemporaryPath, $sConstantsPath)) {
            return true;
        }

        $sBackupPath = PH7_ROOT_PUBLIC . '.ph7builder-stale-constants-' . generate_hash(16) . '.php';
        if (!@rename($sConstantsPath, $sBackupPath)) {
            return false;
        }

        if (@rename($sTemporaryPath, $sConstantsPath)) {
            @unlink($sBackupPath);

            return true;
        }

        @rename($sBackupPath, $sConstantsPath);

        return false;
    }

    private function isDatabasePrefixAvailable(Database $oDb): bool
    {
        $rStmt = $oDb->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database_name AND table_name LIKE :table_prefix ESCAPE \'=\''
        );
        $sEscapedPrefix = str_replace(['=', '%', '_'], ['==', '=%', '=_'], $_SESSION['db']['prefix']);
        $rStmt->execute(
            [
                'database_name' => $_SESSION['db']['name'],
                'table_prefix' => $sEscapedPrefix . '%'
            ]
        );

        return (int)$rStmt->fetchColumn() === 0;
    }

    private function canResumeCompletedDatabaseStep(Database $oDb): bool
    {
        $sConfigPath = PH7_PATH_APP_CONFIG . 'config.ini';
        if (!is_file($sConfigPath) || !is_readable($sConfigPath)) {
            return false;
        }

        $aStoredConfig = parse_ini_file($sConfigPath, true, INI_SCANNER_TYPED);
        $aStoredDatabase = is_array($aStoredConfig) && isset($aStoredConfig['database']) &&
            is_array($aStoredConfig['database'])
            ? $aStoredConfig['database']
            : [];
        $aExpectedDatabase = [
            'type' => $_SESSION['db']['type'],
            'hostname' => $_SESSION['db']['hostname'],
            'username' => $_SESSION['db']['username'],
            'password' => $_SESSION['db']['password'],
            'name' => $_SESSION['db']['name'],
            'prefix' => $_SESSION['db']['prefix'],
            'charset' => $_SESSION['db']['charset'],
            'port' => (int)$_SESSION['db']['port']
        ];

        foreach ($aExpectedDatabase as $sSetting => $mExpectedValue) {
            $mStoredValue = $aStoredDatabase[$sSetting] ?? null;
            if ($sSetting === 'port') {
                $mStoredValue = is_numeric($mStoredValue) ? (int)$mStoredValue : null;
            }
            if ($mStoredValue !== $mExpectedValue) {
                return false;
            }
        }

        try {
            $rStmt = $oDb->prepare(
                'SELECT version FROM ' . $_SESSION['db']['prefix'] .
                "modules WHERE vendorName = 'pH7Builder' AND moduleName = 'SQL System Schema' LIMIT 1"
            );
            $rStmt->execute();

            return $rStmt->fetchColumn() === self::SQL_SCHEMA_VERSION;
        } catch (Throwable $oE) {
            error_log((string)$oE);

            return false;
        }
    }

    private function finalizeDatabaseStep(): void
    {
        $this->chmodConfigFiles();
        $this->markStepComplete(
            4,
            ['database_prefix' => $_SESSION['db']['prefix']]
        );
        unset($_SESSION['db'], $_SESSION['val']);

        redirect(PH7_URL_SLUG_INSTALL . 'config_site');
    }

    private function buildConfigContent(): string|false
    {
        $sConfigContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/config.ini');
        if (!is_string($sConfigContent)) {
            return false;
        }

        return strtr(
            $sConfigContent,
            [
                '%bug_report_email%' => clean_string($_SESSION['val']['bug_report_email']),
                '%ffmpeg_path%' => clean_string($_SESSION['val']['ffmpeg_path']),
                '%db_type_name%' => Database::DBMS_MYSQL_NAME,
                '%db_type%' => Database::DSN_MYSQL_PREFIX,
                '%db_hostname%' => clean_string($_SESSION['db']['hostname']),
                '%db_username%' => clean_string($_SESSION['db']['username']),
                '%db_password%' => clean_string($_SESSION['db']['password']),
                '%db_name%' => clean_string($_SESSION['db']['name']),
                '%db_prefix%' => clean_string($_SESSION['db']['prefix']),
                '%db_charset%' => DbDefaultConfig::CHARSET,
                '%db_port%' => (string)(int)$_SESSION['db']['port'],
                '%private_key%' => generate_hash(40),
                '%rand_id%' => generate_hash(5)
            ]
        );
    }

    private function getUsernameValidationMessage(int $iStatus): string
    {
        global $LANG;

        return match ($iStatus) {
            1 => $LANG['username_too_short'],
            2 => $LANG['username_too_long'],
            default => $LANG['bad_username']
        };
    }

    /**
     * @return list<string>
     */
    private function getUnwritableApplicationPaths(string $sProtectedPath): array
    {
        $aRequiredPaths = [
            $sProtectedPath . PH7_DS . 'app/configs' => 'protected/app/configs',
            $sProtectedPath . PH7_DS . 'data/backup' => 'protected/data/backup',
            $sProtectedPath . PH7_DS . 'data/cache' => 'protected/data/cache',
            $sProtectedPath . PH7_DS . 'data/log' => 'protected/data/log',
            $sProtectedPath . PH7_DS . 'data/tmp' => 'protected/data/tmp',
            PH7_ROOT_PUBLIC . 'data' => 'data',
            PH7_ROOT_PUBLIC . '_repository/module' => '_repository/module'
        ];
        $aUnwritablePaths = [];

        foreach ($aRequiredPaths as $sPath => $sDescription) {
            if (!is_dir($sPath) || !is_writable($sPath)) {
                $aUnwritablePaths[] = $sDescription;
            }
        }

        return $aUnwritablePaths;
    }

    private function getPasswordValidationMessage(int $iStatus): string
    {
        global $LANG;

        return match ($iStatus) {
            1 => $LANG['password_too_short'],
            2 => $LANG['password_too_long'],
            3 => $LANG['password_no_number'],
            default => $LANG['password_no_upper']
        };
    }

    private function persistInitialSite(Database $oDb, string $sAdminPassword): void
    {
        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::ADD_ADMIN, $_SESSION['db']['prefix'] . DbTableName::ADMIN)
        );
        $sCurrentDate = date('Y-m-d H:i:s');
        $rStmt->execute(
            [
                'username' => $_SESSION['val']['admin_username'],
                'password' => Framework\Security\Security::hashPwd($sAdminPassword),
                'email' => $_SESSION['val']['admin_login_email'],
                'firstName' => $_SESSION['val']['admin_first_name'],
                'lastName' => $_SESSION['val']['admin_last_name'],
                'joinDate' => $sCurrentDate,
                'lastActivity' => $sCurrentDate,
                'ip' => client_ip()
            ]
        );

        $aSettingQueries = [
            [SqlQuery::UPDATE_SITE_NAME, 'siteName', $_SESSION['val']['site_name']],
            [SqlQuery::UPDATE_ADMIN_EMAIL, 'adminEmail', $_SESSION['val']['admin_email']],
            [SqlQuery::UPDATE_FEEDBACK_EMAIL, 'feedbackEmail', $_SESSION['val']['admin_feedback_email']],
            [SqlQuery::UPDATE_RETURN_EMAIL, 'returnEmail', $_SESSION['val']['admin_return_email']],
            [SqlQuery::UPDATE_CRON_SECURITY_HASH, 'securityHash', generate_hash(16)]
        ];

        foreach ($aSettingQueries as [$sSql, $sParameter, $sValue]) {
            $rStmt = $oDb->prepare(
                sprintf($sSql, $_SESSION['db']['prefix'] . DbTableName::SETTING)
            );
            $rStmt->execute([$sParameter => $sValue]);
        }

        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::UPDATE_META_OWNER, $_SESSION['db']['prefix'] . DbTableName::META_MAIN)
        );
        $rStmt->execute(['siteName' => $_SESSION['val']['site_name']]);
    }

    private function doesInitialAdminMatch(Database $oDb, string $sAdminPassword): bool
    {
        $rStmt = $oDb->prepare(
            'SELECT username, password, email FROM ' . $_SESSION['db']['prefix'] .
            DbTableName::ADMIN . ' WHERE profileId = 1 LIMIT 1'
        );
        $rStmt->execute();
        $aAdmin = $rStmt->fetch(\PDO::FETCH_ASSOC);

        if ($aAdmin === false) {
            return false;
        }

        if (!isset($aAdmin['username'], $aAdmin['password'], $aAdmin['email']) ||
            !hash_equals($_SESSION['val']['admin_username'], (string)$aAdmin['username']) ||
            !hash_equals($_SESSION['val']['admin_login_email'], (string)$aAdmin['email']) ||
            !Framework\Security\Security::checkPwd($sAdminPassword, (string)$aAdmin['password'])
        ) {
            throw new \RuntimeException(
                'The initial administrator already exists with different credentials. Restore or clear the installation database before retrying.',
                self::INITIAL_ADMIN_MISMATCH_ERROR_CODE
            );
        }

        return true;
    }

    private function tryToPopulateSampleData(): void
    {
        global $LANG;

        $oDb = null;
        $bTransactionStarted = false;

        try {
            Framework\Mvc\Router\FrontController::getInstance()->_initializeDatabase();
            $oDb = Framework\Mvc\Model\Engine\Db::getInstance();
            $oDb->beginTransaction();
            $bTransactionStarted = true;
            $this->populateSampleUserData(
                self::TOTAL_MEMBERS_SAMPLE,
                self::TOTAL_AFFILIATES_SAMPLE,
                self::TOTAL_SUBSCRIBERS_SAMPLE
            );
            $oDb->commit();
            $bTransactionStarted = false;
        } catch (Throwable $oE) {
            if ($bTransactionStarted && $oDb instanceof Framework\Mvc\Model\Engine\Db) {
                try {
                    $oDb->rollBack();
                } catch (Throwable) {
                }
            }
            error_log((string)$oE);
            $_SESSION['sample_data_warning'] = $LANG['sample_data_warning'];
        }
    }

    private static function isSupportedMySqlServer(string $sDriverName, string $sVersion): bool
    {
        return $sDriverName === Database::DSN_MYSQL_PREFIX &&
            stripos($sVersion, 'mariadb') === false &&
            version_compare($sVersion, PH7_REQUIRED_SQL_VERSION, '>=');
    }

    /**
     * Set the correct permission to the config files.
     */
    private function chmodConfigFiles(): void
    {
        @chmod(PH7_PATH_APP_CONFIG . 'config.ini', 0600);
        @chmod(PH7_ROOT_PUBLIC . '_constants.php', 0644);
    }

    /**
     * Include and Initialize the needed PHP classes.
     */
    private function initializeClasses(): void
    {
        @require_once PH7_ROOT_PUBLIC . '_constants.php';
        @require_once PH7_PATH_APP . 'configs/constants.php';

        require PH7_PATH_APP . 'includes/helpers/misc.php';
        require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';
        require PH7_PATH_FRAMEWORK . 'Error/Debug.class.php';

        Framework\Loader\Autoloader::getInstance()->init();

        // Loading Class ~/protected/app/includes/classes/* (for "DbTableName" class)
        require PH7_PATH_APP . 'includes/classes/Loader/Autoloader.php';
        App\Includes\Classes\Loader\Autoloader::getInstance()->init();
    }

    private function removeSessions(): void
    {
        if (ini_get('session.use_cookies')) {
            $aCookieParams = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $aCookieParams['path'],
                    'domain' => $aCookieParams['domain'],
                    'secure' => $aCookieParams['secure'],
                    'httponly' => $aCookieParams['httponly'],
                    'samesite' => $aCookieParams['samesite'] ?? 'Lax'
                ]
            );
        }

        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    private function removeCookies(): void
    {
        $sCookieName = self::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang';

        // We are asking the browser to delete the cookie.
        setcookie(
            $sCookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => parse_url(PH7_URL_INSTALL, PHP_URL_PATH) ?: '/',
                'secure' => str_starts_with(PH7_URL_INSTALL, 'https://'),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        // and then, we delete the cookie value locally to avoid using it by mistake later on in our script
        unset($_COOKIE[$sCookieName]);
    }

    /**
     * Get the loading HTML <img src="" /> gif image.
     *
     * @return string
     */
    private function loadImg(): string
    {
        global $LANG;

        return '<div style="text-align:center"><p>' . $LANG['wait_importing_database'] . '</p>
        <p><img src="data:image/gif;base64,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" alt="' . $LANG['loading'] . '" /></p>
        </div>';
    }
}
