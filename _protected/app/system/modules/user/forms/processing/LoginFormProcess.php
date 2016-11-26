<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header,
PH7\Framework\Security\Security,
PH7\Framework\Mvc\Model\Security as SecurityModel;

class LoginFormProcess extends Form
{
   public function __construct()
   {
        parent::__construct();

        $oUserModel = new UserCoreModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sPassword = $this->httpRequest->post('password');

        /** Check if the connection is not locked **/
        $bIsLoginAttempt = (bool) DbConfig::getSetting('isUserLoginAttempt');
        $iMaxAttempts = (int) DbConfig::getSetting('maxUserLoginAttempts');
        $iTimeDelay = (int) DbConfig::getSetting('loginUserAttemptTime');

        if ($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view))
        {
            \PFBC\Form::setError('form_login_user', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        // Check Login
        $sLogin = $oUserModel->login($sEmail, $sPassword);
        if ($sLogin === 'email_does_not_exist' || $sLogin === 'password_does_not_exist')
        {
            sleep(1); // Security against brute-force attack to avoid drowning the server and the database

            if ($sLogin === 'email_does_not_exist')
            {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_login_user', t('Oops! "%0%" is not associated with any %site_name% account.', escape(substr($sEmail,0,PH7_MAX_EMAIL_LENGTH))));
                $oSecurityModel->addLoginLog($sEmail, 'Guest', 'No Password', 'Failed! Incorrect Username');
            }
            elseif ($sLogin === 'password_does_not_exist')
            {
                $oSecurityModel->addLoginLog($sEmail, 'Guest', $sPassword, 'Failed! Incorrect Password');

                if ($bIsLoginAttempt)
                    $oSecurityModel->addLoginAttempt();

                $this->enableCaptcha();
                $sWrongPwdTxt = t('Oops! This password you entered is incorrect.') . '<br />';
                $sWrongPwdTxt .= t('Please try again (make sure your caps lock is off).') . '<br />';
                $sWrongPwdTxt .= t('Forgot your password? <a href="%0%">Request a new one</a>.', Uri::get('lost-password','main','forgot','user'));
                \PFBC\Form::setError('form_login_user', $sWrongPwdTxt);
            }
        }
        else
        {
            $oSecurityModel->clearLoginAttempts();
            $this->session->remove('captcha_user_enabled');
            $iId = $oUserModel->getId($sEmail);
            $oUserData = $oUserModel->readProfile($iId);

            if ($this->httpRequest->postExists('remember'))
            {
                // We hash again the password
                (new Framework\Cookie\Cookie)->set(
                    array('member_remember' => Security::hashCookie($oUserData->password), 'member_id' => $oUserData->profileId)
                );
            }

            $oUser = new UserCore;
            if (true !== ($mStatus = $oUser->checkAccountStatus($oUserData)))
            {
                \PFBC\Form::setError('form_login_user', $mStatus);
            }
            else
            {
                $o2FactorModel = new TwoFactorAuthCoreModel('user');
                if ($o2FactorModel->isEnabled($iId))
                {
                    // Store the user ID for 2FA
                    $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iId);

                    Header::redirect(Uri::get('two-factor-auth', 'main', 'verificationcode', 'user'));
                }
                else
                {
                    $oUser->setAuth($oUserData, $oUserModel, $this->session, $oSecurityModel);

                    Header::redirect(Uri::get('user','account','index'), t('You are successfully logged in!'));
                }
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
        $this->session->set('captcha_user_enabled',1);
    }
}
