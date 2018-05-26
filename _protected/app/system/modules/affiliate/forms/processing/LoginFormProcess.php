<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Security;
use PH7\Framework\Url\Header;

class LoginFormProcess extends Form implements LoginableForm
{
    const BRUTE_FORCE_SLEEP_DELAY = 1;

    /** @var AffiliateModel */
    private $oAffModel;

    public function __construct()
    {
        parent::__construct();

        $this->oAffModel = new AffiliateModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sPassword = $this->httpRequest->post('password', HttpRequest::NO_CLEAN);

        /** Check if the connection is not locked **/
        $bIsLoginAttempt = (bool)DbConfig::getSetting('isAffiliateLoginAttempt');
        $iMaxAttempts = (int)DbConfig::getSetting('maxAffiliateLoginAttempts');
        $iTimeDelay = (int)DbConfig::getSetting('loginAffiliateAttemptTime');

        if ($bIsLoginAttempt &&
            !$oSecurityModel->checkLoginAttempt(
                $iMaxAttempts,
                $iTimeDelay,
                $sEmail,
                $this->view,
                DbTableName::AFFILIATE
            )
        ) {
            \PFBC\Form::setError('form_login_aff', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        // Check Login
        $sLogin = $this->oAffModel->login($sEmail, $sPassword, DbTableName::AFFILIATE);
        if ($sLogin === 'email_does_not_exist' || $sLogin === 'password_does_not_exist') {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if ($sLogin === 'email_does_not_exist') {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_login_aff', t('Oops! "%0%" is not associated with any %site_name% account.', escape(substr($sEmail, 0, PH7_MAX_EMAIL_LENGTH))));
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    'No Password',
                    'Failed! Incorrect Username',
                    DbTableName::AFFILIATE
                );
            } elseif ($sLogin === 'password_does_not_exist') {
                $oSecurityModel->addLoginLog(
                    $sEmail,
                    'Guest',
                    $sPassword,
                    'Failed! Incorrect Password',
                    DbTableName::AFFILIATE
                );

                if ($bIsLoginAttempt) {
                    $oSecurityModel->addLoginAttempt(DbTableName::AFFILIATE);
                }

                $this->enableCaptcha();
                $sWrongPwdTxt = t('Oops! This password you entered is incorrect.') . '<br />';
                $sWrongPwdTxt .= t('Please try again (make sure your caps lock is off).') . '<br />';
                $sWrongPwdTxt .= t('Forgot your password? <a href="%0%">Request a new one</a>.', Uri::get('lost-password', 'main', 'forgot', 'affiliate'));
                \PFBC\Form::setError('form_login_aff', $sWrongPwdTxt);
            }
        } else {
            $oSecurityModel->clearLoginAttempts(DbTableName::AFFILIATE);
            $this->session->remove('captcha_aff_enabled');
            $iId = $this->oAffModel->getId($sEmail, null, DbTableName::AFFILIATE);
            $oAffData = $this->oAffModel->readProfile($iId, DbTableName::AFFILIATE);

            $this->updatePwdHashIfNeeded($sPassword, $oAffData->password, $sEmail);

            $oAff = new AffiliateCore;
            if (true !== ($mStatus = $oAff->checkAccountStatus($oAffData))) {
                \PFBC\Form::setError('form_login_aff', $mStatus);
            } else {
                $o2FactorModel = new TwoFactorAuthCoreModel('affiliate');
                if ($o2FactorModel->isEnabled($iId)) {
                    // Store the affiliate ID for 2FA
                    $this->session->set(TwoFactorAuthCore::PROFILE_ID_SESS_NAME, $iId);

                    Header::redirect(Uri::get('two-factor-auth', 'main', 'verificationcode', 'affiliate'));
                } else {
                    $oAff->setAuth($oAffData, $this->oAffModel, $this->session, $oSecurityModel);

                    Header::redirect(Uri::get('affiliate', 'account', 'index'), t('You are successfully logged in!'));
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
            $this->oAffModel->changePassword($sEmail, $sNewPwdHash, DbTableName::AFFILIATE);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha()
    {
        $this->session->set('captcha_aff_enabled', 1);
    }
}
