<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Ip\Ip,
PH7\Framework\Util\Various,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header,
PH7\Framework\Mvc\Model\Security as SecurityModel;

class LoginFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $sIp = Ip::get();
        $oAdminModel = new AdminModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sUsername = $this->httpRequest->post('username');
        $sPassword = $this->httpRequest->post('password');


        /*** Security IP Login ***/
        $sIpLogin = DbConfig::getSetting('ipLogin');

        /*** Check if the connection is not locked ***/
        $bIsLoginAttempt = (bool) DbConfig::getSetting('isAdminLoginAttempt');
        $iMaxAttempts = (int) DbConfig::getSetting('maxAdminLoginAttempts');
        $iTimeDelay = (int) DbConfig::getSetting('loginAdminAttemptTime');

        if($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view, 'Admins'))
        {
            \PFBC\Form::setError('form_admin_login', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        /*** Check Login ***/
        $bIsLogged = $oAdminModel->adminLogin($sEmail, $sUsername, $sPassword);
        $bIsIpBanned = !empty($sIpLogin) && $sIpLogin !== $sIp;

        if(!$bIsLogged || $bIsIpBanned) // If the login is failed or if the IP address is banned
        {
            sleep(2); // Security against brute-force attack to avoid drowning the server and the database

            if(!$bIsLogged)
            {
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Incorrect Email, Username or Password', 'Admins');

                if($bIsLoginAttempt)
                    $oSecurityModel->addLoginAttempt('Admins');

                $this->session->set('captcha_admin_enabled',1); // Enable Captcha
                \PFBC\Form::setError('form_admin_login', t('"Email", "Username" or "Password" is Incorrect'));
            }
            elseif($bIsIpBanned)
            {
                $this->session->set('captcha_admin_enabled',1); // Enable Captcha
                \PFBC\Form::setError('form_admin_login', t('Incorrect Login!'));
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Bad Ip adress', 'Admins');
            }
        }
        else
        {
            $oSecurityModel->clearLoginAttempts('Admins');
            $this->session->remove('captcha_admin_enabled');

            // Is disconnected if the user is logged on as "user" or "affiliate".
            if(UserCore::auth() || AffiliateCore::auth()) $this->session->destroy();

            $iId = $oAdminModel->getId($sEmail, null, 'Admins');
            $oAdminData = $oAdminModel->readProfile($iId, 'Admins');

            // Regenerate the session ID to prevent the session fixation
            $this->session->regenerateId();

            $aSessionData = array(
               'admin_id' => $oAdminData->profileId,
               'admin_email' => $oAdminData->email,
               'admin_username' => $oAdminData->username,
               'admin_first_name' => $oAdminData->firstName,
               'admin_ip' => $sIp,
               'admin_http_user_agent' => $this->browser->getUserAgent(),
               'admin_token' => Various::genRnd($oAdminData->email),
            );

            $this->session->set($aSessionData);
            $oSecurityModel->addLoginLog($sEmail, $sUsername, '*****', 'Logged in!', 'Admins');
            $oAdminModel->setLastActivity($oAdminData->profileId, 'Admins');

            Header::redirect(Uri::get(PH7_ADMIN_MOD,'main','index'), t('You are successfully login!'));
        }
    }

}
