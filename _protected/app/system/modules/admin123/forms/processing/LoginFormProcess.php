<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Ip\Ip,
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

        if ($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view, 'Admins'))
        {
            \PFBC\Form::setError('form_admin_login', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        /*** Check Login ***/
        $bIsLogged = $oAdminModel->adminLogin($sEmail, $sUsername, $sPassword);
        $bIpNotAllowed = !empty($sIpLogin) && $sIpLogin !== $sIp;

        if (!$bIsLogged || $bIpNotAllowed) // If the login is failed or if the IP address is not allowed
        {
            sleep(2); // Security against brute-force attack to avoid drowning the server and the database

            if (!$bIsLogged)
            {
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Incorrect Email, Username or Password', 'Admins');

                if ($bIsLoginAttempt)
                    $oSecurityModel->addLoginAttempt('Admins');

                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('"Email", "Username" or "Password" is Incorrect'));
            }
            elseif ($bIpNotAllowed)
            {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('Incorrect Login!'));
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Wrong IP address', 'Admins');
            }
        }
        else
        {
            $oSecurityModel->clearLoginAttempts('Admins');
            $this->session->remove('captcha_admin_enabled');
            $iId = $oAdminModel->getId($sEmail, null, 'Admins');
            $oAdminData = $oAdminModel->readProfile($iId, 'Admins');

            $o2FactorModel = new TwoFactorAuthCoreModel(PH7_ADMIN_MOD);
            if ($o2FactorModel->isEnabled($iId))
            {
                // Store the admin ID for 2FA
                $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iId);

                Header::redirect(Uri::get('two-factor-auth', 'main', 'verificationcode', PH7_ADMIN_MOD));
            }
            else
            {
                (new AdminCore)->setAuth($oAdminData, $oAdminModel, $this->session, $oSecurityModel);

                Header::redirect(Uri::get(PH7_ADMIN_MOD, 'main', 'index'), t('You are successfully logged in!'));
            }
        }
    }

    /**
     * Enable the Captcha on the login form.
     *
     * @return void
     */
    protected function enableCaptcha()
    {
        $this->session->set('captcha_admin_enabled',1);
    }
}
