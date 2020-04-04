<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Security;
use PH7\Framework\Url\Header;
use stdClass;

class LoginFormProcess extends Form implements LoginableForm
{
    const BRUTE_FORCE_SLEEP_DELAY = 1;

    /** @var UserCoreModel */
    private $oUserModel;

    public function __construct()
    {
        $sUrlRelocateAfterLogin = Uri::get('user', 'account', 'index');

        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sPassword = $this->httpRequest->post('password', HttpRequest::NO_CLEAN);

        /** Check if the connection is not locked **/
        $bIsLoginAttempt = (bool)DbConfig::getSetting('isUserLoginAttempt');
        $iMaxAttempts = (int)DbConfig::getSetting('maxUserLoginAttempts');
        $iTimeDelay = (int)DbConfig::getSetting('loginUserAttemptTime');

        if ($bIsLoginAttempt &&
            !$oSecurityModel->checkLoginAttempt(
                $iMaxAttempts,
                $iTimeDelay,
                $sEmail,
                $this->view
            )
        ) {
            \PFBC\Form::setError('form_login_user', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        // Check Login
        $sLogin = $this->oUserModel->login($sEmail, $sPassword);
        if ($sLogin === CredentialStatusCore::EMAIL_DOES_NOT_EXIST || $sLogin === CredentialStatusCore::PASSWORD_DOES_NOT_EXIST) {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if ($sLogin === CredentialStatusCore::EMAIL_DOES_NOT_EXIST) {
                $this->enableCaptcha();
                \PFBC\Form::setError(
                    'form_login_user',
                    t('Oops! "%0%" is not associated with any %site_name% account.', escape(substr($sEmail, 0, PH7_MAX_EMAIL_LENGTH)))
                );
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    'No Password',
                    'Failed! Incorrect Username'
                );
            } elseif ($sLogin === CredentialStatusCore::PASSWORD_DOES_NOT_EXIST) {
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    $sPassword,
                    'Failed! Incorrect Password'
                );

                if ($bIsLoginAttempt) {
                    $oSecurityModel->addLoginAttempt();
                }

                $this->enableCaptcha();
                $sWrongPwdTxt = t('Oops! This password you entered is incorrect.') . '<br />';
                $sWrongPwdTxt .= t('Please try again (make sure your caps lock is off).') . '<br />';
                $sWrongPwdTxt .= t('Forgot your password? <a href="%0%">Request a new one</a>.', Uri::get('lost-password', 'main', 'forgot', 'user'));
                \PFBC\Form::setError('form_login_user', $sWrongPwdTxt);
            }
        } else {
            $oSecurityModel->clearLoginAttempts();
            $this->session->remove('captcha_user_enabled');
            $iId = $this->oUserModel->getId($sEmail);
            $oUserData = $this->oUserModel->readProfile($iId);

            $this->updatePwdHashIfNeeded($sPassword, $oUserData->password, $sEmail);

            if ($this->httpRequest->postExists(RememberMeCore::CHECKBOX_FIELD_NAME)) {
                $this->session->set(RememberMeCore::STAY_LOGGED_IN_REQUESTED, 1);
            }

            if ($this->isSmsVerificationEligible($oUserData)) {
                // Store the user ID before redirecting to sms-verification module
                $this->session->set(SmsVerificationCore::PROFILE_ID_SESS_NAME, $iId);

                $this->redirectToSmsVerification();
            }

            $oUser = new UserCore;
            if (true !== ($mStatus = $oUser->checkAccountStatus($oUserData))) {
                \PFBC\Form::setError('form_login_user', $mStatus);
            } else {
                $o2FactorModel = new TwoFactorAuthCoreModel('user');
                if ($o2FactorModel->isEnabled($iId)) {
                    // Store the user ID for 2FA
                    $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iId);

                    $this->redirectToTwoFactorAuth();
                } else {
                    $oRememberMe = new RememberMeCore;
                    if ($oRememberMe->isEligible($this->session)) {
                        $oRememberMe->enableSession($oUserData);
                    }
                    unset($oRememberMe);

                    $oUser->setAuth(
                        $oUserData,
                        $this->oUserModel,
                        $this->session,
                        $oSecurityModel
                    );

                    Header::redirect(
                        $sUrlRelocateAfterLogin,
                        t('You are successfully logged in!')
                    );
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updatePwdHashIfNeeded($sPassword, $sUserPasswordHash, $sEmail)
    {
        if ($sNewPwdHash = Security::pwdNeedsRehash($sPassword, $sUserPasswordHash)) {
            $this->oUserModel->changePassword($sEmail, $sNewPwdHash, DbTableName::MEMBER);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha()
    {
        $iNumberAttempts = (int)$this->session->get('captcha_user_enabled');
        $this->session->set('captcha_user_enabled', $iNumberAttempts++);
    }

    /**
     * @param stdClass $oUserData
     *
     * @return bool
     */
    private function isSmsVerificationEligible(stdClass $oUserData)
    {
        return $oUserData->active == RegistrationCore::SMS_ACTIVATION &&
            SysMod::isEnabled('sms-verification');
    }

    private function redirectToSmsVerification()
    {
        Header::redirect(
            Uri::get('sms-verification', 'main', 'send')
        );
    }

    private function redirectToTwoFactorAuth()
    {
        Header::redirect(
            Uri::get(
                'two-factor-auth',
                'main',
                'verificationcode',
                'user'
            )
        );
    }
}
