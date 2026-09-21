<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class VerificationFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iProfileId = SmsVerificationCore::getChallengeProfileId($this->session);
        if ($iProfileId === null) {
            $this->restartLogin();

            return;
        }
        $mCode = $this->httpRequest->post('verification_code');
        if (!is_string($mCode) || !Verification::consumeCode($this->session, $mCode)) {
            \PFBC\Form::setError(
                'form_sms_verification',
                t('This code is invalid, expired or has too many attempts. <a href="%0%">Request a new code</a>.', Uri::get('sms-verification', 'main', 'send'))
            );

            return;
        }
        $mPhoneNumber = $this->session->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME);
        if (!is_string($mPhoneNumber) || $mPhoneNumber === ''
            || !(new SmsVerificationModel())->activate($iProfileId, $mPhoneNumber)) {
            $this->restartLogin();

            return;
        }

        $oUser = new UserCore();
        $oUser->clearReadProfileCache($iProfileId);
        $oUser->clearInfoFieldCache($iProfileId);
        SmsVerificationCore::clearChallenge($this->session);
        $this->session->remove(RememberMeCore::STAY_LOGGED_IN_REQUESTED);
        Header::redirect(
            Uri::get('user', 'main', 'login'),
            t('Your phone number is verified. Please sign in to continue.')
        );
    }

    private function restartLogin(): void
    {
        SmsVerificationCore::clearChallenge($this->session);
        $this->session->remove(RememberMeCore::STAY_LOGGED_IN_REQUESTED);
        Header::redirect(Uri::get('user', 'main', 'login'), t('Please sign in again to verify your account.'), Design::ERROR_TYPE);
    }
}
