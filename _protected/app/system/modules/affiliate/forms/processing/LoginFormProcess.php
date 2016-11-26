<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header,
PH7\Framework\Mvc\Model\Security as SecurityModel;

class LoginFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oAffModel = new AffiliateModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sPassword = $this->httpRequest->post('password');

        /** Check if the connection is not locked **/
        $bIsLoginAttempt = (bool) DbConfig::getSetting('isAffiliateLoginAttempt');
        $iMaxAttempts = (int) DbConfig::getSetting('maxAffiliateLoginAttempts');
        $iTimeDelay = (int) DbConfig::getSetting('loginAffiliateAttemptTime');

        if ($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view, 'Affiliates'))
        {
            \PFBC\Form::setError('form_login_aff', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        // Check Login
        $sLogin = $oAffModel->login($sEmail, $sPassword, 'Affiliates');
        if ($sLogin === 'email_does_not_exist' || $sLogin === 'password_does_not_exist')
        {
            sleep(1); // Security against brute-force attack to avoid drowning the server and the database

            if ($sLogin === 'email_does_not_exist')
            {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_login_aff', t('Oops! "%0%" is not associated with any %site_name% account.', escape(substr($sEmail,0,PH7_MAX_EMAIL_LENGTH))));
                $oSecurityModel->addLoginLog($sEmail, 'Guest', 'No Password', 'Failed! Incorrect Username', 'Affiliates');
            }
            elseif ($sLogin === 'password_does_not_exist')
            {
                $oSecurityModel->addLoginLog($sEmail, 'Guest', $sPassword, 'Failed! Incorrect Password', 'Affiliates');

                if ($bIsLoginAttempt)
                    $oSecurityModel->addLoginAttempt('Affiliates');

                $this->enableCaptcha();
                $sWrongPwdTxt = t('Oops! This password you entered is incorrect.') . '<br />';
                $sWrongPwdTxt .= t('Please try again (make sure your caps lock is off).') . '<br />';
                $sWrongPwdTxt .= t('Forgot your password? <a href="%0%">Request a new one</a>.', Uri::get('lost-password','main','forgot','affiliate'));
                \PFBC\Form::setError('form_login_aff', $sWrongPwdTxt);
            }
        }
        else
        {
            $oSecurityModel->clearLoginAttempts('Affiliates');
            $this->session->remove('captcha_aff_enabled');
            $iId = $oAffModel->getId($sEmail, null, 'Affiliates');
            $oAffData = $oAffModel->readProfile($iId, 'Affiliates');
            $oAff = new AffiliateCore;

            if (true !== ($mStatus = $oAff->checkAccountStatus($oAffData)))
            {
                \PFBC\Form::setError('form_login_aff', $mStatus);
            }
            else
            {
                $o2FactorModel = new TwoFactorAuthCoreModel('affiliate');
                if ($o2FactorModel->isEnabled($iId))
                {
                    // Store the affiliate ID for 2FA
                    $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iId);

                    Header::redirect(Uri::get('two-factor-auth', 'main', 'verificationcode', 'affiliate'));
                }
                else
                {
                    $oAff->setAuth($oAffData, $oAffModel, $this->session, $oSecurityModel);

                    Header::redirect(Uri::get('affiliate','account','index'), t('You are successfully logged in!'));
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
        $this->session->set('captcha_aff_enabled',1);
    }
}
