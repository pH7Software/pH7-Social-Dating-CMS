<?php
/**
 * @title            InstallController Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Controller
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

// Reset the time limit
@set_time_limit(0);

class InstallController extends Controller
{
    const TOTAL_MEMBERS_SAMPLE = 16;
    const TOTAL_AFFILIATES_SAMPLE = 1;
    const TOTAL_SUBSCRIBERS_SAMPLE = 1;

    /**
     * Enable/Disable Modules according to the chosen niche
     */
    const SOCIAL_MODS = [
        'connect' => '0',
        'affiliate' => '0',
        'game' => '0',
        'chat' => '0',
        'chatroulette' => '0',
        'picture' => '1',
        'video' => '1',
        'friend' => '1',
        'hotornot' => '0',
        'forum' => '1',
        'note' => '1',
        'blog' => '1',
        'newsletter' => '0',
        'invite' => '1',
        'webcam' => '1',
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

    const DATING_MODS = [
        'connect' => '0',
        'affiliate' => '1',
        'game' => '0',
        'chat' => '1',
        'chatroulette' => '1',
        'picture' => '1',
        'video' => '0',
        'friend' => '0',
        'hotornot' => '1',
        'forum' => '0',
        'note' => '0',
        'blog' => '1',
        'newsletter' => '1',
        'invite' => '0',
        'webcam' => '0',
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
    const SOCIAL_SETTINGS = [
        'navbarType' => 'default',
        'socialMediaWidgets' => '1',
        'requireRegistrationAvatar' => '0',
        'isUserAgeRangeField' => '0'
    ];

    const DATING_SETTINGS = [
        'navbarType' => 'inverse',
        'socialMediaWidgets' => '0',
        'requireRegistrationAvatar' => '1',
        'isUserAgeRangeField' => '1'
    ];


    /********************* STEP 1 *********************/
    public function index()
    {
        $aLangs = get_dir_list(PH7_ROOT_INSTALL . Language::LANG_FOLDER_NAME);
        $aLangsList = include PH7_ROOT_INSTALL . 'inc/lang_list.inc.php';
        $sLangSelect = '';

        foreach ($aLangs as $sLang) {
            $sSel = (empty($_REQUEST['l']) ? $sLang === $this->sCurrentLang ? '" selected="selected' : '' : ($sLang === $_REQUEST['l']) ? '" selected="selected' : '');
            $sLangSelect .= '<option value="?l=' . $sLang . $sSel . '">' . $aLangsList[$sLang] . '</option>';
        }

        $this->oView->assign('lang_select', $sLangSelect);
        $this->oView->assign('sept_number', 1);
        $this->oView->display('index.tpl');
    }

    /********************* STEP 2 *********************/
    public function license()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['license_agreements_submit'])) {
            if ($this->isAgreementsAgreed()) {
                $_SESSION['step2'] = 1;

                redirect(PH7_URL_SLUG_INSTALL . 'config_path');
            } else {
                $this->oView->assign('failure', 1);
            }
        }

        $this->oView->assign('sept_number', 2);
        $this->oView->display('license.tpl');
    }

    /********************* STEP 3 *********************/
    public function config_path()
    {
        global $LANG;

        if (!empty($_SESSION['step2'])) {
            if (empty($_SESSION['val']['path_protected'])) {
                // If not set, set a default value for the field used in Smarty tpl
                $_SESSION['val']['path_protected'] = PH7_ROOT_PUBLIC . '_protected' . PH7_DS;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['path_protected'])) {
                $_SESSION['val']['path_protected'] = check_ext_start(check_ext_end(trim($_POST['path_protected'])));

                if (is_file($_SESSION['val']['path_protected'] . 'app/configs/constants.php')) {
                    if (is_readable($_SESSION['val']['path_protected'])) {
                        $sConstantContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/constants.php');

                        $sConstantContent = str_replace('%path_protected%', addslashes($_SESSION['val']['path_protected']), $sConstantContent);

                        if (!@file_put_contents(PH7_ROOT_PUBLIC . '_constants.php', $sConstantContent)) {
                            $aErrors[] = $LANG['no_public_writable'];
                        } else {
                            $_SESSION['step3'] = 1;
                            unset($_SESSION['val']);

                            redirect(PH7_URL_SLUG_INSTALL . 'config_system');
                        }
                    } else {
                        $aErrors[] = $LANG['no_protected_readable'];
                    }
                } else {
                    $aErrors[] = $LANG['no_protected_exist'];
                }
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
    public function config_system()
    {
        global $LANG;

        if (!empty($_SESSION['step3']) && is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
            session_regenerate_id(true);

            if (empty($_SESSION['val'])) {
                $_SESSION['db']['type_name'] = Db::DBMS_MYSQL_NAME;
                $_SESSION['db']['type'] = Db::DSN_MYSQL_PREFIX;

                $_SESSION['db']['hostname'] = DbDefaultConfig::HOSTNAME;
                $_SESSION['db']['username'] = DbDefaultConfig::USERNAME;
                $_SESSION['db']['name'] = DbDefaultConfig::NAME;
                $_SESSION['db']['prefix'] = DbDefaultConfig::PREFIX;
                $_SESSION['db']['port'] = DbDefaultConfig::PORT;
                $_SESSION['db']['charset'] = DbDefaultConfig::CHARSET;

                $_SESSION['val']['bug_report_email'] = '';
                $_SESSION['val']['ffmpeg_path'] = ffmpeg_path();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['config_system_submit'])) {
                if (filled_out($_POST)) {
                    foreach ($_POST as $sKey => $sVal) {
                        $_SESSION['db'][str_replace('db_', '', $sKey)] = trim($sVal);
                    }

                    $_SESSION['val']['bug_report_email'] = trim($_POST['bug_report_email']);
                    $_SESSION['val']['ffmpeg_path'] = trim($_POST['ffmpeg_path']);

                    if (validate_email($_SESSION['val']['bug_report_email'])) {
                        try {
                            require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';
                            @require_once PH7_ROOT_PUBLIC . '_constants.php';
                            @require_once PH7_PATH_APP . 'configs/constants.php';

                            // Config File
                            @chmod(PH7_PATH_APP_CONFIG, 0777);
                            $sConfigContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/config.ini');

                            $sConfigContent = str_replace('%bug_report_email%', $_SESSION['val']['bug_report_email'], $sConfigContent);
                            $sConfigContent = str_replace('%ffmpeg_path%', clean_string($_SESSION['val']['ffmpeg_path']), $sConfigContent);

                            $sConfigContent = str_replace('%db_type_name%', $_SESSION['db']['type_name'], $sConfigContent);
                            $sConfigContent = str_replace('%db_type%', $_SESSION['db']['type'], $sConfigContent);
                            $sConfigContent = str_replace('%db_hostname%', $_SESSION['db']['hostname'], $sConfigContent);
                            $sConfigContent = str_replace('%db_username%', clean_string($_SESSION['db']['username']), $sConfigContent);
                            $sConfigContent = str_replace('%db_password%', clean_string($_SESSION['db']['password']), $sConfigContent);
                            $sConfigContent = str_replace('%db_name%', clean_string($_SESSION['db']['name']), $sConfigContent);
                            $sConfigContent = str_replace('%db_prefix%', clean_string($_SESSION['db']['prefix']), $sConfigContent);
                            $sConfigContent = str_replace('%db_charset%', $_SESSION['db']['charset'], $sConfigContent);
                            $sConfigContent = str_replace('%db_port%', $_SESSION['db']['port'], $sConfigContent);

                            $sConfigContent = str_replace('%private_key%', generate_hash(40), $sConfigContent);
                            $sConfigContent = str_replace('%rand_id%', generate_hash(5), $sConfigContent);

                            if (!@file_put_contents(PH7_PATH_APP_CONFIG . 'config.ini', $sConfigContent)) {
                                $aErrors[] = $LANG['no_app_config_writable'];
                            } else {
                                if (!(
                                    $DB->getAttribute(\PDO::ATTR_DRIVER_NAME) === Db::DSN_MYSQL_PREFIX &&
                                    version_compare($DB->getAttribute(\PDO::ATTR_SERVER_VERSION), PH7_REQUIRED_SQL_VERSION, '>='))
                                ) {
                                    $aErrors[] = $LANG['require_mysql_version'];
                                } else {
                                    ignore_user_abort(true);

                                    $aDumps = [
                                        /** Game **/
                                        // We need to install the Game before the "Core SQL" for foreign key reasons
                                        'pH7_SchemaGame',
                                        'pH7_DataGame',
                                        /** Core (main SQL schema/data) **/
                                        'pH7_Core'
                                    ];

                                    for ($iFileKey = 0, $iCount = count($aDumps); $iFileKey < $iCount; $iFileKey++) {
                                        exec_query_file(
                                            $DB,
                                            PH7_ROOT_INSTALL . 'data/sql/' . $_SESSION['db']['type_name'] . '/' . $aDumps[$iFileKey] . '.sql'
                                        );
                                    }

                                    // We finalise it by setting the correct permission to the config files
                                    $this->chmodConfigFiles();

                                    $_SESSION['step4'] = 1;
                                    unset($_SESSION['val']);

                                    redirect(PH7_URL_SLUG_INSTALL . 'config_site');
                                }
                            }
                        } catch (\PDOException $oE) {
                            $aErrors[] = $LANG['database_error'] . escape($oE->getMessage());
                        }
                    } else {
                        $aErrors[] = $LANG['bad_email'];
                    }
                } else {
                    $aErrors[] = $LANG['all_fields_mandatory'];
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
    public function config_site()
    {
        global $LANG;

        if (empty($_SESSION['step5'])) {
            if (!empty($_SESSION['step4']) && is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
                session_regenerate_id(true);

                if (empty($_SESSION['val'])) {
                    $_SESSION['val']['site_name'] = self::DEFAULT_SITE_NAME;
                    $_SESSION['val']['admin_login_email'] = '';
                    $_SESSION['val']['admin_email'] = '';
                    $_SESSION['val']['admin_feedback_email'] = '';
                    $_SESSION['val']['admin_return_email'] = '';
                    $_SESSION['val']['admin_username'] = self::DEFAULT_ADMIN_USERNAME;
                    $_SESSION['val']['admin_first_name'] = '';
                    $_SESSION['val']['admin_last_name'] = '';
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['config_site_submit'])) {
                    if (filled_out($_POST)) {
                        foreach ($_POST as $sKey => $sVal) {
                            $_SESSION['val'][$sKey] = trim($sVal);
                        }

                        if (validate_email($_SESSION['val']['admin_login_email']) && validate_email($_SESSION['val']['admin_email']) && validate_email($_SESSION['val']['admin_feedback_email']) && validate_email($_SESSION['val']['admin_return_email'])) {
                            if (validate_username($_SESSION['val']['admin_username']) === 0) {
                                if (validate_password($_SESSION['val']['admin_password']) === 0) {
                                    if (validate_identical($_SESSION['val']['admin_password'], $_SESSION['val']['admin_passwords'])) {
                                        if (!find($_SESSION['val']['admin_password'], $_SESSION['val']['admin_username']) && !find($_SESSION['val']['admin_password'], $_SESSION['val']['admin_first_name']) && !find($_SESSION['val']['admin_password'], $_SESSION['val']['admin_last_name'])) {
                                            if (validate_name($_SESSION['val']['admin_first_name'])) {
                                                if (validate_name($_SESSION['val']['admin_last_name'])) {
                                                    $this->initializeClasses();

                                                    try {
                                                        ignore_user_abort(true);
                                                        require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';

                                                        $rStmt = $DB->prepare(
                                                            sprintf(SqlQuery::ADD_ADMIN, $_SESSION['db']['prefix'] . DbTableName::ADMIN)
                                                        );

                                                        $sCurrentDate = date('Y-m-d H:i:s');
                                                        $rStmt->execute([
                                                            'username' => $_SESSION['val']['admin_username'],
                                                            'password' => Framework\Security\Security::hashPwd($_SESSION['val']['admin_password']),
                                                            'email' => $_SESSION['val']['admin_login_email'],
                                                            'firstName' => $_SESSION['val']['admin_first_name'],
                                                            'lastName' => $_SESSION['val']['admin_last_name'],
                                                            'joinDate' => $sCurrentDate,
                                                            'lastActivity' => $sCurrentDate,
                                                            'ip' => client_ip()
                                                        ]);

                                                        $rStmt = $DB->prepare(
                                                            sprintf(SqlQuery::UPDATE_SITE_NAME, $_SESSION['db']['prefix'] . DbTableName::SETTING)
                                                        );
                                                        $rStmt->execute(['siteName' => $_SESSION['val']['site_name']]);

                                                        $rStmt = $DB->prepare(
                                                            sprintf(SqlQuery::UPDATE_ADMIN_EMAIL, $_SESSION['db']['prefix'] . DbTableName::SETTING)
                                                        );
                                                        $rStmt->execute(['adminEmail' => $_SESSION['val']['admin_email']]);

                                                        $rStmt = $DB->prepare(
                                                            sprintf(SqlQuery::UPDATE_FEEDBACK_EMAIL, $_SESSION['db']['prefix'] . DbTableName::SETTING)
                                                        );
                                                        $rStmt->execute(['feedbackEmail' => $_SESSION['val']['admin_feedback_email']]);

                                                        $rStmt = $DB->prepare(
                                                            sprintf(SqlQuery::UPDATE_RETURN_EMAIL, $_SESSION['db']['prefix'] . DbTableName::SETTING)
                                                        );
                                                        $rStmt->execute(['returnEmail' => $_SESSION['val']['admin_return_email']]);

                                                        if (!empty($_POST['sample_data_request'])) {
                                                            $this->populateSampleUserData(
                                                                self::TOTAL_MEMBERS_SAMPLE,
                                                                self::TOTAL_AFFILIATES_SAMPLE,
                                                                self::TOTAL_SUBSCRIBERS_SAMPLE
                                                            );
                                                        }

                                                        $_SESSION['step5'] = 1;

                                                        redirect(PH7_URL_SLUG_INSTALL . 'niche');
                                                    } catch (\PDOException $oE) {
                                                        $aErrors[] = $LANG['database_error'] . escape($oE->getMessage());
                                                    }
                                                } else {
                                                    $aErrors[] = $LANG['bad_last_name'];
                                                }
                                            } else {
                                                $aErrors[] = $LANG['bad_first_name'];
                                            }
                                        } else {
                                            $aErrors[] = $LANG['insecure_password'];
                                        }
                                    } else {
                                        $aErrors[] = $LANG['passwords_different'];
                                    }
                                } elseif (validate_password($_SESSION['val']['admin_password']) === 1) {
                                    $aErrors[] = $LANG['password_too_short'];
                                } elseif (validate_password($_SESSION['val']['admin_password']) === 2) {
                                    $aErrors[] = $LANG['password_too_long'];
                                } elseif (validate_password($_SESSION['val']['admin_password']) === 3) {
                                    $aErrors[] = $LANG['password_no_number'];
                                } elseif (validate_password($_SESSION['val']['admin_password']) === 4) {
                                    $aErrors[] = $LANG['password_no_upper'];
                                }
                            } elseif (validate_username($_SESSION['val']['admin_username']) === 1) {
                                $aErrors[] = $LANG['username_too_short'];
                            } elseif (validate_username($_SESSION['val']['admin_username']) === 2) {
                                $aErrors[] = $LANG['username_too_long'];
                            } elseif (validate_username($_SESSION['val']['admin_username']) === 3) {
                                $aErrors[] = $LANG['bad_username'];
                            }
                        } else {
                            $aErrors[] = $LANG['bad_email'];
                        }
                    } else {
                        $aErrors[] = $LANG['all_fields_mandatory'];
                    }
                }
            } else {
                redirect(PH7_URL_SLUG_INSTALL . 'config_system');
            }
        } else {
            redirect(PH7_URL_SLUG_INSTALL . 'niche');
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
    public function niche()
    {
        global $LANG;

        if (empty($_SESSION['step6'])) {
            if (!empty($_SESSION['step5']) && is_file(PH7_ROOT_PUBLIC . '_constants.php')) {
                session_regenerate_id(true);

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['niche_submit'])) {
                    $bUpdateNeeded = false; // Value by default. Don't need to update the DB for the Social-Dating Niche

                    switch ($_POST['niche_submit']) {
                        case 'zendate':
                            $bUpdateNeeded = true;
                            $sTheme = 'zendate';
                            $aModUpdate = self::SOCIAL_MODS;
                            $aSettingUpdate = self::SOCIAL_SETTINGS;
                            break;

                        case 'datelove':
                            $bUpdateNeeded = true;
                            $sTheme = 'datelove';
                            $aModUpdate = self::DATING_MODS;
                            $aSettingUpdate = self::DATING_SETTINGS;
                            break;

                        // For 'base' niche (template), don't do anything. Just use the default settings already setup in the database
                    }

                    if ($bUpdateNeeded) {
                        $this->initializeClasses();

                        try {
                            require_once PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php';

                            // Enable/Disable the modules according to the chosen niche
                            foreach ($aModUpdate as $sModName => $sStatus) {
                                $this->updateMods($DB, $sModName, $sStatus);
                            }

                            $this->updateSettings($aSettingUpdate);

                            $this->updateTheme($DB, $sTheme);
                        } catch (\PDOException $oE) {
                            $aErrors[] = $LANG['database_error'] . escape($oE->getMessage());
                        }
                    }
                    $_SESSION['step6'] = 1;

                    redirect(PH7_URL_SLUG_INSTALL . 'finish');
                }
            } else {
                redirect(PH7_URL_SLUG_INSTALL . 'config_site');
            }
        } else {
            redirect(PH7_URL_SLUG_INSTALL . 'finish');
        }

        $this->oView->assign('sept_number', 6);

        if (!empty($aErrors) && is_array($aErrors)) {
            $this->oView->assign('errors', $aErrors);
        }

        $this->oView->display('niche.tpl');
    }

    /********************* STEP 7 *********************/
    public function finish()
    {
        $sConstantsPath = PH7_ROOT_PUBLIC . '_constants.php';
        if (is_file($sConstantsPath)) {
            @require_once $sConstantsPath;

            if ($this->canEmailBeSent()) {
                $this->sendWelcomeEmail();

                $this->oView->assign('admin_login_email', $_SESSION['val']['admin_login_email']);
                $this->oView->assign('admin_username', $_SESSION['val']['admin_username']);
            }

            $this->removeSessions();
            $this->removeCookies();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['confirm_remove_install'])) {
                remove_install_dir();
                clearstatcache(); // We remove the files status cache as the "_install" folder doesn't exist anymore by now.
                exit(header('Location: ' . PH7_URL_ROOT));
            }

            $this->oView->assign('sept_number', 7);
            $this->oView->display('finish.tpl');
        } else {
            redirect(PH7_URL_SLUG_INSTALL . 'config_path');
        }
    }

    /**
     * Send an email to say the installation is now done, and give some information...
     */
    private function sendWelcomeEmail()
    {
        global $LANG;

        $aParams = [
            'to' => $_SESSION['val']['admin_login_email'],
            'subject' => $LANG['title_email_finish_install'],
            'body' => $LANG['content_email_finish_install']
        ];

        send_mail($aParams);
    }

    /**
     * Verify if the email can be sent (has all necessary global variables).
     *
     * @return bool
     */
    private function canEmailBeSent()
    {
        return !empty($_SESSION['val']['admin_login_email']) &&
            !empty($_SESSION['val']['admin_username']);
    }

    /**
     * Update module status (enabled/disabled).
     *
     * @param Db $oDb
     * @param string $sModName Module Name.
     * @param string $sStatus '1' = Enabled | '0' = Disabled (need to be string because in DB it is an "enum").
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    private function updateMods(Db $oDb, $sModName, $sStatus)
    {
        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::UPDATE_SYS_MODULE, $_SESSION['db']['prefix'] . DbTableName::SYS_MOD_ENABLED)
        );

        return $rStmt->execute(['modName' => $sModName, 'status' => $sStatus]);
    }

    /**
     * Set the adequate website's theme for the chosen niche.
     *
     * @param Db $oDb
     * @param string $sThemeName
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    private function updateTheme(Db $oDb, $sThemeName)
    {
        $rStmt = $oDb->prepare(
            sprintf(SqlQuery::UPDATE_THEME, $_SESSION['db']['prefix'] . DbTableName::SETTING)
        );

        return $rStmt->execute(['theme' => $sThemeName, 'setting' => 'defaultTemplate']);
    }

    /**
     * @param array $aParams
     *
     * @return void
     */
    private function updateSettings(array $aParams)
    {
        // Initialize the site's database to get "\PH7\Framework\Mvc\Model\Engine\Db" class working (as it uses that DB and not the installer one)
        Framework\Mvc\Router\FrontController::getInstance()->_initializeDatabase();

        foreach ($aParams as $sName => $sValue) {
            $sMethodName = ($sName === 'socialMediaWidgets' ? 'setSocialWidgets' : 'setSetting');
            Framework\Mvc\Model\DbConfig::$sMethodName($sValue, $sName);
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
    private function populateSampleUserData($iMemberNumber, $iAffiliateNumber, $iSubscriberNumber)
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
            $sBirthDate = $oFaker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d');

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
                $aUser['bank_account'] = $oFaker->bankAccountNumber;
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
     * @return bool
     */
    private function isAgreementsAgreed()
    {
        return
            !empty($_POST['license_agreed']) &&
            !empty($_POST['conform_laws_agreed']) &&
            !empty($_POST['responsibilities_agreed']);
    }

    /**
     * Set the correct permission to the config files.
     *
     * @return void
     */
    private function chmodConfigFiles()
    {
        @chmod(PH7_PATH_APP_CONFIG . 'config.ini', 0644);
        @chmod(PH7_ROOT_PUBLIC . '_constants.php', 0644);
    }

    /**
     * Require & Initialize the classes.
     *
     * @return void
     */
    private function initializeClasses()
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

    private function removeSessions()
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
    }

    private function removeCookies()
    {
        $sCookieName = self::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang';

        // We are asking the browser to delete the cookie.
        setcookie(
            $sCookieName,
            0,
            0,
            null,
            null,
            false,
            true
        );

        // and then, we delete the cookie value locally to avoid using it by mistake in following our script.
        unset($_COOKIE[$sCookieName]);
    }

    /**
     * Get the loading HTML <img src="" /> gif image.
     *
     * @return string
     */
    private function loadImg()
    {
        global $LANG;

        return '<div style="text-align:center"><p>' . $LANG['wait_importing_database'] . '</p>
        <p><img src="data:image/gif;base64,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" alt="' . $LANG['loading'] . '" /></p>
        </div>';
    }
}
