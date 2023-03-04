<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Security;
use PH7\Framework\Url\Header;

class LoginFormProcess extends Form implements LoginableForm
{
    private const BRUTE_FORCE_SLEEP_DELAY = 2;

    private AdminModel $oAdminModel;

    public function __construct()
    {
        parent::__construct();

        $this->oAdminModel = new AdminModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sUsername = $this->httpRequest->post('username');
        $sPassword = $this->httpRequest->post('password', HttpRequest::NO_CLEAN);

        /*** Check if the connection is not locked ***/
        $bIsLoginAttempt = (bool)DbConfig::getSetting('isAdminLoginAttempt');
        $iMaxAttempts = (int)DbConfig::getSetting('maxAdminLoginAttempts');
        $iTimeDelay = (int)DbConfig::getSetting('loginAdminAttemptTime');

        if ($bIsLoginAttempt &&
            !$oSecurityModel->checkLoginAttempt(
                $iMaxAttempts,
                $iTimeDelay,
                $sEmail,
                $this->view,
                DbTableName::ADMIN_ATTEMPT_LOGIN
            )
        ) {
            \PFBC\Form::setError('form_admin_login', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        /*** Check Login ***/
        $bIsLogged = $this->oAdminModel->adminLogin($sEmail, $sUsername, $sPassword);
        $bIpAllowed = $this->isIpAllowed();

        // If the login failed or if the IP address isn't allowed
        if (!$bIsLogged || !$bIpAllowed) {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if (!$bIsLogged) {
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    $sUsername,
                    '*****',
                    'Failed! Incorrect Email, Username or Password',
                    DbTableName::ADMIN_LOG_LOGIN
                );

                if ($bIsLoginAttempt) {
                    $oSecurityModel->addLoginAttempt(DbTableName::ADMIN_ATTEMPT_LOGIN);
                }

                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('"Email", "Username" or "Password" is incorrect'));
            } elseif (!$bIpAllowed) {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('Incorrect Login!'));
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    $sUsername,
                    '*****',
                    'Failed! Wrong IP address',
                    DbTableName::ADMIN_LOG_LOGIN
                );
            }
        } else {
            $oSecurityModel->clearLoginAttempts(DbTableName::ADMIN_ATTEMPT_LOGIN);
            $this->session->remove('captcha_admin_enabled');
            $iProfileId = $this->oAdminModel->getId($sEmail, null, DbTableName::ADMIN);
            $oAdminData = $this->oAdminModel->readProfile($iProfileId, DbTableName::ADMIN);

            $this->updatePwdHashIfNeeded($sPassword, $oAdminData->password, $sEmail);

            $o2FactorModel = new TwoFactorAuthCoreModel(PH7_ADMIN_MOD);
            if ($o2FactorModel->isEnabled($iProfileId)) {
                // Store the admin ID for 2FA
                $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iProfileId);

                $this->redirectToTwoFactorAuth();
            } else {
                (new AdminCore)->setAuth(
                    $oAdminData,
                    $this->oAdminModel,
                    $this->session,
                    $oSecurityModel
                );

                Header::redirect(
                    Uri::get(PH7_ADMIN_MOD, 'main', 'index'),
                    t('You are successfully logged in!')
                );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updatePwdHashIfNeeded(string $sPassword, string $sUserPasswordHash, string $sEmail): void
    {
        if ($sNewPwdHash = Security::pwdNeedsRehash($sPassword, $sUserPasswordHash)) {
            $this->oAdminModel->changePassword($sEmail, $sNewPwdHash, DbTableName::ADMIN);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha(): void
    {
        $this->session->set('captcha_admin_enabled', 1);
    }

    /**
     * Checks if the IP is whitelisted to login on the admin panel.
     */
    private function isIpAllowed(): bool
    {
        $sAllowedIpLogin = (string)DbConfig::getSetting('ipLogin');
        $sAllowedIpLogin = trim($sAllowedIpLogin);

        return empty($sAllowedIpLogin) || $sAllowedIpLogin === Ip::get();
    }

    private function redirectToTwoFactorAuth(): void
    {
        Header::redirect(
            Uri::get(
                'two-factor-auth',
                'main',
                'verificationcode',
                PH7_ADMIN_MOD
            )
        );
    }
}
