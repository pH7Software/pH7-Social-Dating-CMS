<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
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

        $oAffModel = new AffiliateModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sPassword = $this->httpRequest->post('password');

        /** Check if the connection is not locked **/
        $bIsLoginAttempt = (bool) DbConfig::getSetting('isAffiliateLoginAttempt');
        $iMaxAttempts = (int) DbConfig::getSetting('maxAffiliateLoginAttempts');
        $iTimeDelay = (int) DbConfig::getSetting('loginAffiliateAttemptTime');

        if($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view, 'Affiliates'))
        {
            \PFBC\Form::setError('form_login_aff', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        // Check Login
        $sLogin = $oAffModel->login($sEmail, $sPassword, 'Affiliates');
        if($sLogin === 'email_does_not_exist' || $sLogin === 'password_does_not_exist')
        {
            sleep(1); // Security against brute-force attack to avoid drowning the server and the database

            if($sLogin === 'email_does_not_exist')
            {
                $this->session->set('captcha_enabled',1); // Enable Captcha
                \PFBC\Form::setError('form_login_aff', t('Oops! "%0%" is not associated with any %site_name% account.', escape(substr($sEmail,0,PH7_MAX_EMAIL_LENGTH))));
                $oSecurityModel->addLoginLog($sEmail, 'Guest', 'No Password', 'Failed! Incorrect Username', 'Affiliates');
            }
            elseif($sLogin === 'password_does_not_exist')
            {
                $oSecurityModel->addLoginLog($sEmail, 'Guest', $sPassword, 'Failed! Incorrect Password', 'Affiliates');

                if($bIsLoginAttempt)
                    $oSecurityModel->addLoginAttempt('Affiliates');

                $this->session->set('captcha_enabled',1); // Enable Captcha
                \PFBC\Form::setError('form_login_aff', t('Oops! This password you entered is incorrect.<br /> Please try again (make sure your caps lock is off).<br /> Forgot your password? <a href="%0%">Request a new one</a>.', Uri::get('affiliate','home','forgot')));
            }
        }
        else
        {
            $oSecurityModel->clearLoginAttempts('Affiliates');
            $this->session->remove('captcha_enabled');
            $iId = $oAffModel->getId($sEmail, null, 'Affiliates');
            $oAffData = $oAffModel->readProfile($iId, 'Affiliates');

            if(true !== ($mStatus = (new AffiliateCore)->checkAccountStatus($oAffData)))
            {
                \PFBC\Form::setError('form_login_aff', $mStatus);
            }
            else
            {
                // Is disconnected if the user is logged on as "user" or "administrator".
                if(UserCore::auth() || AdminCore::auth()) $this->session->destroy();

                // Regenerate the session ID to prevent the session fixation
                $this->session->regenerateId();

                $aSessionData = [
                    'affiliate_id' => $oAffData->profileId,
                    'affiliate_email' => $oAffData->email,
                    'affiliate_username' => $oAffData->username,
                    'affiliate_first_name' => $oAffData->firstName,
                    'affiliate_sex' => $oAffData->sex,
                    'affiliate_ip' => Ip::get(),
                    'affiliate_http_user_agent' => $this->browser->getUserAgent(),
                    'affiliate_token' => Various::genRnd($oAffData->email)
                ];

                $this->session->set($aSessionData);
                $oSecurityModel->addLoginLog($oAffData->email, $oAffData->username, '*****', 'Logged in!', 'Affiliates');
                $oAffModel->setLastActivity($oAffData->profileId, 'Affiliates');

                Header::redirect(Uri::get('affiliate','account','index'), t('You are successfully login!'));
            }
        }
    }

}
