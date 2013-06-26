<?php
/**
 * @title            InstallController Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Controller
 * @version          1.3
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

/**
 * This is usually necessary to import data from remote server.
 */
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class InstallController extends Controller
{

    private $_sRedirectUrlNoLicense, $_sGameDownloadPath, $_sGamePath;

    /***** Constructor (initializing variables) *****/
    public function __construct ()
    {
        parent::__construct();

        $this->_sGamePath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/';
        $this->_sGameDownloadPath = self::SOFTWARE_DOWNLOAD_URL . 'games/pH7_game.zip';
        $this->_sRedirectUrlNoLicense = self::SOFTWARE_LICENSE_URL;
    }

    /********************* STEP 1 *********************/
    public function index ()
    {
        if (empty($_SESSION['step1']))
        {
            $_SESSION['step1'] = 1;
        }

        $aLangs = get_dir_list(PH7_ROOT_INSTALL . '/langs/');
        $sLangSelect = '';

        foreach ($aLangs as $sLang)
        {
            $sSel = (empty($_REQUEST['l']) ? $sLang == $this->sCurrentLang ? '" selected="selected' : '' : ($sLang == $_REQUEST['l']) ? '" selected="selected' : '');
            $sLangSelect .= '<option value="?l=' . $sLang . $sSel . '">' . $sLang . '</option>';
        }

        $this->view->assign('lang_select', $sLangSelect);
        $this->view->assign('sept_number', 1);
        $this->view->display('index.tpl');
    }

    /********************* STEP 2 *********************/
    public function license ()
    {
        global $LANG;

        if (empty($_SESSION['step2']))
        {
            if (isset($_SESSION['step1']))
            {
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['license']))
                {
                    if (check_license($_POST['license']))
                    {
                        $_SESSION['status_license'] = 'success';
                        $_SESSION['step2'] = 1;
                        redirect(PH7_URL_SLUG_INSTALL . 'config_path');
                    }
                    else
                    {
                        $_SESSION['status_license'] = 'failure';
                        $aErrors[] = $LANG['failure_license'];
                    }
                }
            }
            else
            {
                redirect(PH7_URL_SLUG_INSTALL);
            }
        }
        else
        {
            redirect(PH7_URL_SLUG_INSTALL . 'config_path');
        }

        $this->view->assign('sept_number', 2);
        $this->view->assign('errors', @$aErrors);
        unset($aErrors);
        $this->view->display('license.tpl');
    }

    /********************* STEP 3 *********************/
    public function config_path ()
    {
        global $LANG;

        if (empty($_SESSION['step3']))
        {
            if (isset($_SESSION['step2']))
            {
                if ($_SESSION['status_license'] == 'success')
                {
                    if (empty($_SESSION['value']))
                        $_SESSION['value']['path_protected'] = dirname(PH7_ROOT_PUBLIC) . PH7_DS . '_protected' . PH7_DS;

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['path_protected']))
                    {
                        $_SESSION['value']['path_protected'] = check_ext_start(check_ext_end(trim($_POST['path_protected'])));

                        if (is_dir($_SESSION['value']['path_protected']))
                        {
                            if (is_writable($_SESSION['value']['path_protected']))
                            {
                                $sConstantContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/constants.php');

                                $sConstantContent = str_replace('%path_protected%', $_SESSION['value']['path_protected'], $sConstantContent);

                                file_put_contents(PH7_ROOT_PUBLIC . '_constants.php', $sConstantContent);

                                @chmod(PH7_ROOT_PUBLIC . '_constants.php', 0644);
                                $_SESSION['step3'] = 1;
                                unset($_SESSION['value']);

                                redirect(PH7_URL_SLUG_INSTALL . 'config_system');
                            }
                            else
                            {
                                $aErrors[] = $LANG['no_dir_writable'];
                            }
                        }
                        else
                        {
                            $aErrors[] = $LANG['no_dir_exist'];
                        }
                    }
                }
                else
                {
                    redirect($this->_sRedirectUrlNoLicense);
                }
            }
            else
            {
                redirect(PH7_URL_SLUG_INSTALL . 'license');
            }
        }
        else
        {
            redirect(PH7_URL_SLUG_INSTALL . 'config_system');
        }

        $this->view->assign('sept_number', 3);
        $this->view->assign('errors', @$aErrors);
        unset($aErrors);
        $this->view->display('config_path.tpl');
    }

    /********************* STEP 4 *********************/
    public function config_system ()
    {
        global $LANG;

        if (empty($_SESSION['step4']))
        {
            if (isset($_SESSION['step3']) && is_file(PH7_ROOT_PUBLIC . '_constants.php'))
            {
                if ($_SESSION['status_license'] == 'success')
                {

                    if (empty($_SESSION['value']))
                    {
                        $_SESSION['db']['db_type'] = 'mysql';
                        $_SESSION['db']['db_hostname'] = 'localhost';
                        $_SESSION['db']['db_name'] = 'PHS-SOFTWARE';
                        $_SESSION['db']['db_username'] = 'root';
                        $_SESSION['db']['db_prefix'] = 'PH7_';
                        $_SESSION['db']['db_port'] = '3760';
                        $_SESSION['db']['db_charset'] = 'UTF8';

                        $_SESSION['value']['bug_report_email'] = '';
                        $_SESSION['value']['ffmpeg_path'] = (is_windows()) ? 'C:\ffmpeg\ffmpeg.exe' : '/usr/bin/ffmpeg';
                    }

                    if ($this->_checkDownloadServer()) // Check if the link is available
                    {

                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['config_system_submit']))
                        {

                            foreach ($_POST as $sKey => $sValue)
                            {
                                $_SESSION['db'][$sKey] = trim($sValue);
                            }

                            $_SESSION['value']['bug_report_email'] = trim($_POST['bug_report_email']);
                            $_SESSION['value']['ffmpeg_path'] = trim($_POST['ffmpeg_path']);

                            if (filled_out($_POST))
                            {

                                if (filter_input(INPUT_POST, 'bug_report_email', FILTER_VALIDATE_EMAIL) !== false)
                                {
                                    try
                                    {
                                        require_once(PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php');

                                        @require_once(PH7_ROOT_PUBLIC . '_constants.php');
                                        @require_once(PH7_PATH_APP . 'configs/constants.php');

                                        // Config File
                                        @chmod(PH7_PATH_APP_CONFIG, 0777);
                                        $sConfigContent = file_get_contents(PH7_ROOT_INSTALL . 'data/configs/config.ini');

                                        $sConfigContent = str_replace('%bug_report_email%', $_SESSION['value']['bug_report_email'], $sConfigContent);
                                        $sConfigContent = str_replace('%ffmpeg_path%', $_SESSION['value']['ffmpeg_path'], $sConfigContent);

                                        $sConfigContent = str_replace('%db_type%', $_SESSION['db']['db_type'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_hostname%', $_SESSION['db']['db_hostname'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_name%', $_SESSION['db']['db_name'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_username%', $_SESSION['db']['db_username'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_password%', $_SESSION['db']['db_password'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_prefix%', $_SESSION['db']['db_prefix'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_charset%', $_SESSION['db']['db_charset'], $sConfigContent);
                                        $sConfigContent = str_replace('%db_port%', $_SESSION['db']['db_port'], $sConfigContent);

                                        $sConfigContent = str_replace('%rand_id%', generate_hash(5), $sConfigContent);

                                        file_put_contents(PH7_PATH_APP_CONFIG . 'config.ini', $sConfigContent);
                                        @chmod(PH7_PATH_APP_CONFIG . 'config.ini', 0644);

                                        echo '<div style="text-align:center"><p>' . $LANG['wait_importing_database'] . '</p>
                                        <p><img src="data:image/gif;base64,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" alt="' . $LANG['loading'] . '" /></p>
                                        </div>';
                                        sleep(2);

                                        /***** Execute the SQL files *****/

                                        /*** Game ***/
                                        // We need to install the Game before the Core SQL for foreign keys that work are correct.
                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_SchemaGame.sql');
                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_DataGame.sql');

                                        /*** Core ***/
                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_installCore.sql');

                                        // --- GeoIp (exec_query_file() function executes these files only if they existens otherwise it does nothing) --- //
                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCountry.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity2.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity3.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity4.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity5.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity6.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity7.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoCity8.sql');

                                        exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_geoState.sql');

                                        // --- Execute this file if there is something --- //
                                        if (filesize(PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_sampleData.sql') > 12)
                                        {
                                            exec_query_file($DB, PH7_ROOT_INSTALL . 'data/sql/MySQL/pH7_sampleData.sql');
                                        }

                                        unset($DB);

                                        /** Checks if the games are not already installed **/
                                        if (!$this->_isGameInstalled())
                                            $this->_downloadGame();

                                        $_SESSION['step4'] = 1;
                                        unset($_SESSION['value']);

                                        redirect(PH7_URL_SLUG_INSTALL . 'config_site');
                                    }
                                    catch (\PDOException $oE)
                                    {
                                        $aErrors[] = $LANG['database_error'] . $oE->getMessage();
                                    }
                                }
                                else
                                {
                                    $aErrors[] = $LANG['bad_email'];
                                }
                            }
                            else
                            {
                                $aErrors[] = $LANG['all_fields_mandatory'];
                            }
                        }
                    }
                    else
                    {
                        $aErrors[] = $LANG['error_get_server_url'];
                    }
                }
                else
                {
                    redirect($this->_sRedirectUrlNoLicense);
                }
            }
            else
            {
                redirect(PH7_URL_SLUG_INSTALL . 'config_path');
            }
        }
        else
        {
            redirect(PH7_URL_SLUG_INSTALL . 'config_site');
        }

        $this->view->assign('sept_number', 4);
        $this->view->assign('errors', @$aErrors);
        unset($aErrors);
        $this->view->display('config_system.tpl');
    }

    /********************* STEP 5 *********************/
    public function config_site ()
    {
        global $LANG;

        if (empty($_SESSION['step5']))
        {
            if (isset($_SESSION['step4']) && is_file(PH7_ROOT_PUBLIC . '_constants.php'))
            {
                if ($_SESSION['status_license'] == 'success')
                {
                    if (empty($_SESSION['value']))
                    {
                        $_SESSION['value']['admin_login_email'] = '';
                        $_SESSION['value']['admin_email'] = '';
                        $_SESSION['value']['admin_feedback_email'] = '';
                        $_SESSION['value']['admin_return_email'] = '';
                        $_SESSION['value']['admin_username'] = 'administrator';
                        $_SESSION['value']['admin_first_name'] = '';
                        $_SESSION['value']['admin_last_name'] = '';
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['config_site_submit']))
                    {
                        foreach ($_POST as $sKey => $sValue)
                        {
                            $_SESSION['value'][$sKey] = trim($sValue);
                        }

                        if (filled_out($_POST))
                        {
                            if (validate_email($_SESSION['value']['admin_login_email']) == 'ok' && validate_email($_SESSION['value']['admin_email']) == 'ok' && validate_email($_SESSION['value']['admin_feedback_email']) == 'ok' && validate_email($_SESSION['value']['admin_return_email']) == 'ok')
                            {
                                if (validate_username($_SESSION['value']['admin_username']) == 'ok')
                                {
                                    if (validate_password($_SESSION['value']['admin_password']) == 'ok')
                                    {
                                        if (validate_identical($_SESSION['value']['admin_password'], $_SESSION['value']['admin_passwordS']))
                                        {
                                            if (validate_name($_SESSION['value']['admin_first_name']))
                                            {
                                                if (validate_name($_SESSION['value']['admin_last_name']))
                                                {
                                                    @require_once(PH7_ROOT_PUBLIC . '_constants.php');
                                                    @require_once(PH7_PATH_APP . 'configs/constants.php');

                                                    require(PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php');
                                                    // To load "Security" and "Various" class.
                                                    Framework\Loader\Autoloader::getInstance()->init();

                                                    try
                                                    {
                                                        require_once(PH7_ROOT_INSTALL . 'inc/_db_connect.inc.php');

                                                        // SQL EXECUTE
                                                        $oSqlQuery = $DB->prepare('INSERT INTO ' . $_SESSION['db']['db_prefix'] . 'Admins' . ' (profileId , username, password, email, firstName, lastName, joinDate, lastActivity, prefixSalt, suffixSalt) VALUES(1, :username, :password, :email, :firstName, :lastName, :joinDate, :lastActivity, :prefixSalt, :suffixSalt)');

                                                        $sCurrentDate = date('Y-m-d H:i:s');
                                                        $sPrefixSalt = Framework\Util\Various::genRnd();
                                                        $sSuffixSalt = Framework\Util\Various::genRnd();
                                                        $oSqlQuery->execute(array(
                                                            'username' => $_SESSION['value']['admin_username'],
                                                            'password' => Framework\Security\Security::hashPwd($sPrefixSalt, $_SESSION['value']['admin_password'], $sSuffixSalt, Framework\Security\Security::ADMIN),
                                                            'email' => $_SESSION['value']['admin_login_email'],
                                                            'firstName'=> $_SESSION['value']['admin_first_name'],
                                                            'lastName'=> $_SESSION['value']['admin_last_name'],
                                                            'joinDate'=> $sCurrentDate,
                                                            'lastActivity' => $sCurrentDate,
                                                            'prefixSalt' => $sPrefixSalt,
                                                            'suffixSalt' => $sSuffixSalt
                                                        ));

                                                        $oSqlQuery = $DB->prepare('UPDATE ' . $_SESSION['db']['db_prefix'] . 'Settings SET value = :adminEmail WHERE name = \'adminEmail\'');
                                                        $oSqlQuery->execute(array(
                                                            'adminEmail' => $_SESSION['value']['admin_email']
                                                        ));

                                                        $oSqlQuery = $DB->prepare('UPDATE ' . $_SESSION['db']['db_prefix'] . 'Settings SET value = :feedbackEmail WHERE name = \'feedbackEmail\'');
                                                        $oSqlQuery->execute(array(
                                                            'feedbackEmail' => $_SESSION['value']['admin_feedback_email']
                                                        ));

                                                        $oSqlQuery = $DB->prepare('UPDATE ' . $_SESSION['db']['db_prefix'] . 'Settings SET value = :returnEmail WHERE name = \'returnEmail\'');
                                                        $oSqlQuery->execute(array(
                                                            'returnEmail' => $_SESSION['value']['admin_return_email']
                                                        ));


                                                        $_SESSION['step5'] = 1;

                                                        redirect(PH7_URL_SLUG_INSTALL . 'finish');
                                                    }
                                                    catch (\PDOException $oE)
                                                    {
                                                        $aErrors[] = $LANG['database_error'] . $oE->getMessage();
                                                    }
                                                }
                                                else
                                                {
                                                    $aErrors[] = $LANG['bad_last_name'];
                                                }
                                            }
                                            else
                                            {
                                                $aErrors[] = $LANG['bad_first_name'];
                                            }
                                        }
                                        else
                                        {
                                            $aErrors[] = $LANG['passwordS_different'];
                                        }
                                    }
                                    elseif (validate_password($_SESSION['value']['admin_password']) == 'empty')
                                    {
                                        $aErrors[] = $LANG['password_empty'];
                                    }
                                    elseif (validate_password($_SESSION['value']['admin_password']) == 'tooshort')
                                    {
                                        $aErrors[] = $LANG['password_tooshort'];
                                    }
                                    elseif (validate_password($_SESSION['value']['admin_password']) == 'toolong')
                                    {
                                        $aErrors[] = $LANG['password_toolong'];
                                    }
                                    elseif (validate_password($_SESSION['value']['admin_password']) ==  'nonumber')
                                    {
                                        $aErrors[] = $LANG['password_nonumber'];
                                    }
                                    elseif (validate_password($_SESSION['value']['admin_password']) ==  'noupper')
                                    {
                                        $aErrors[] = $LANG['password_noupper'];
                                    }
                                }
                                elseif (validate_username($_SESSION['value']['admin_username']) == 'empty')
                                {
                                    $aErrors[] = $LANG['password_empty'];
                                }
                                elseif (validate_username($_SESSION['value']['admin_username']) == 'tooshort')
                                {
                                    $aErrors[] = $LANG['username_tooshort'];
                                }
                                elseif (validate_username($_SESSION['value']['admin_username']) == 'toolong')
                                {
                                    $aErrors[] = $LANG['username_toolong'];
                                }
                                elseif (validate_username($_SESSION['value']['admin_username']) == 'badusername')
                                {
                                    $aErrors[] = $LANG['username_badusername'];
                                }
                            }
                            else
                            {
                                $aErrors[] = $LANG['bad_email'];
                            }
                        }
                        else
                        {
                            $aErrors[] = $LANG['all_fields_mandatory'];
                        }
                    }
                }
                else
                {
                    redirect($this->_sRedirectUrlNoLicense);
                }
            }
            else
            {
                redirect(PH7_URL_SLUG_INSTALL . 'config_system');
            }
        }
        else
        {
            redirect(PH7_URL_SLUG_INSTALL . 'finish');
        }

        $this->view->assign('sept_number', 5);
        $this->view->assign('errors', @$aErrors);
        unset($aErrors);
        $this->view->display('config_site.tpl');
    }

    /********************* STEP 6 *********************/
    public function finish ()
    {
        global $LANG;

        @require_once(PH7_ROOT_PUBLIC . '_constants.php');

        if (!empty($_SESSION['value']))
        {
            // Send email for finish instalation.
            $aParams = array(
              'to' => $_SESSION['value']['admin_login_email'],
              'subject' => $LANG['title_email_finish_install'],
              'body' => $LANG['content_email_finish_install']
            );
            send_mail($aParams);
        }

        $_SESSION = array();
        /* Remote the sessions */
        session_unset();
        session_destroy();

        /* Remove the cookie */
        $sCookieName = Controller::SOFTWARE_PREFIX_COOKIE_NAME . '_install_lang';

        // We are asking the browser to delete the cookie.
        setcookie($sCookieName);
        // and we delete the cookie value locally to avoid using it by mistake in following our script.
        unset($_COOKIE[$sCookieName]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_remove_install']))
        {
            remove_install_dir();
            header('Location: ' . PH7_URL_ROOT);
        }

        $this->view->assign('sept_number', 6);
        $this->view->display('finish.tpl');
    }

    /***** Checks if the games are installed *****/
    private function _isGameInstalled ()
    {
        return (is_dir($this->_sGamePath . 'file') && is_dir($this->_sGamePath . 'img/thumb'));
    }

    /***** Download and extract the games in his directory *****/
    private function _downloadGame ()
    {
        $sGameTmpFile = 'tmp_game.zip';

        $rHandle = fopen($this->_sGamePath . $sGameTmpFile, 'wb');
        fwrite($rHandle, get_url_contents($this->_sGameDownloadPath));
        fclose($rHandle);
        zip_extract($this->_sGamePath . $sGameTmpFile, $this->_sGamePath);
        unlink($this->_sGamePath . $sGameTmpFile);
    }

    /***** Checks Download Server and checks if the games are not already installed *****/
    private function _checkDownloadServer ()
    {
        if ($this->_isGameInstalled()) return true; // Games are already installed.

        return (check_url($this->_sGameDownloadPath));
    }

}
