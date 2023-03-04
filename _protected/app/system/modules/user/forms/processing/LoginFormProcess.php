<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Security;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Url\Header;
use stdClass;

class LoginFormProcess extends Form implements LoginableForm
{
    private const BRUTE_FORCE_SLEEP_DELAY = 1;

    private UserCoreModel $oUserModel;

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
        if ($sLogin === CredentialStatusCore::INCORRECT_EMAIL_IN_DB || $sLogin === CredentialStatusCore::INCORRECT_PASSWORD_IN_DB) {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if ($sLogin === CredentialStatusCore::INCORRECT_EMAIL_IN_DB) {
                $this->enableCaptcha();
                \PFBC\Form::setError(
                    'form_login_user',
                    t('Oops! "%0%" is not associated with any %site_name% accounts.', escape(substr($sEmail, 0, PH7_MAX_EMAIL_LENGTH)))
                );
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    'No Password',
                    'Failed! Incorrect Username'
                );
            } elseif ($sLogin === CredentialStatusCore::INCORRECT_PASSWORD_IN_DB) {
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    '*****',
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
            $iProfileId = $this->oUserModel->getId($sEmail);
            $oUserData = $this->oUserModel->readProfile($iProfileId);

            $this->updatePwdHashIfNeeded($sPassword, $oUserData->password, $sEmail);

            // Check if the country the user logged in is different from the last time.
            $sLocationName = Geo::getCountry();
            if ($this->isForeignLocation($iProfileId, $sLocationName)) {
                SecurityCore::sendSuspiciousLocationAlert(
                    $sLocationName,
                    $oUserData,
                    $this->browser,
                    $this->view
                );
            }

            if ($this->httpRequest->postExists(RememberMeCore::CHECKBOX_FIELD_NAME)) {
                $this->session->set(RememberMeCore::STAY_LOGGED_IN_REQUESTED, 1);
            }

            if ($this->isSmsVerificationEligible($oUserData)) {
                // Store the user ID before redirecting to sms-verification module
                $this->session->set(SmsVerificationCore::PROFILE_ID_SESS_NAME, $iProfileId);

                $this->redirectToSmsVerification();
            }

            $oUser = new UserCore;
            if (true !== ($mStatus = $oUser->checkAccountStatus($oUserData))) {
                \PFBC\Form::setError('form_login_user', $mStatus);
            } else {
                $o2FactorModel = new TwoFactorAuthCoreModel('user');
                if ($o2FactorModel->isEnabled($iProfileId)) {
                    // Store the user ID for 2FA
                    $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iProfileId);

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
    public function updatePwdHashIfNeeded(string $sPassword, string $sUserPasswordHash, string $sEmail): void
    {
        if ($sNewPwdHash = Security::pwdNeedsRehash($sPassword, $sUserPasswordHash)) {
            $this->oUserModel->changePassword($sEmail, $sNewPwdHash, DbTableName::MEMBER);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha(): void
    {
        $iNumberAttempts = (int)$this->session->get('captcha_user_enabled');
        $this->session->set('captcha_user_enabled', $iNumberAttempts++);
    }

    /**
     * @param int $iProfileId
     * @param string $sLocationName
     */
    public function isForeignLocation($iProfileId, $sLocationName): bool
    {
        $sLatestUsedIp = $this->oUserModel->getLastUsedIp($iProfileId);

        if (!empty($sLatestUsedIp) && (new Validate)->ip($sLatestUsedIp)) {
            return Geo::getCountry($sLatestUsedIp) !== $sLocationName;
        }

        return false;
    }

    private function isSmsVerificationEligible(stdClass $oUserData): bool
    {
        return $oUserData->active == RegistrationCore::SMS_ACTIVATION &&
            SysMod::isEnabled('sms-verification');
    }

    private function redirectToSmsVerification(): void
    {
        Header::redirect(
            Uri::get('sms-verification', 'main', 'send')
        );
    }

    private function redirectToTwoFactorAuth(): void
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
