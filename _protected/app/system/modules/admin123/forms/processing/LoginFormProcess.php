<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

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
    const BRUTE_FORCE_SLEEP_DELAY = 2;

    /** @var AdminModel */
    private $oAdminModel;

    public function __construct()
    {
        parent::__construct();

        $sIp = Ip::get();
        $this->oAdminModel = new AdminModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sUsername = $this->httpRequest->post('username');
        $sPassword = $this->httpRequest->post('password', HttpRequest::NO_CLEAN);


        /*** Security IP Login ***/
        $sIpLogin = DbConfig::getSetting('ipLogin');

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
        $bIpNotAllowed = !empty(trim($sIpLogin)) && $sIpLogin !== $sIp;

        // If the login is failed or if the IP address is not allowed
        if (!$bIsLogged || $bIpNotAllowed) {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if (!$bIsLogged) {
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    $sUsername,
                    $sPassword,
                    'Failed! Incorrect Email, Username or Password',
                    DbTableName::ADMIN_LOG_LOGIN
                );

                if ($bIsLoginAttempt) {
                    $oSecurityModel->addLoginAttempt(DbTableName::ADMIN_ATTEMPT_LOGIN);
                }

                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('"Email", "Username" or "Password" is Incorrect'));
            } elseif ($bIpNotAllowed) {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('Incorrect Login!'));
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    $sUsername,
                    $sPassword,
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
    public function updatePwdHashIfNeeded($sPassword, $sUserPasswordHash, $sEmail)
    {
        if ($sNewPwdHash = Security::pwdNeedsRehash($sPassword, $sUserPasswordHash)) {
            $this->oAdminModel->changePassword($sEmail, $sNewPwdHash, DbTableName::ADMIN);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha()
    {
        $this->session->set('captcha_admin_enabled', 1);
    }

    private function redirectToTwoFactorAuth()
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
