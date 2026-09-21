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

class PhoneNumberFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iProfileId = SmsVerificationCore::getChallengeProfileId($this->session);
        if ($iProfileId === null || !(new SmsVerificationModel())->isPending($iProfileId)) {
            SmsVerificationCore::clearChallenge($this->session);
            $this->session->remove(RememberMeCore::STAY_LOGGED_IN_REQUESTED);
            Header::redirect(Uri::get('user', 'main', 'login'), t('Please sign in again to verify your account.'), Design::ERROR_TYPE);

            return;
        }

        $mPhoneNumber = $this->httpRequest->post('phone_number');
        if (!is_string($mPhoneNumber) || $mPhoneNumber === '') {
            \PFBC\Form::setError('form_phone_number_verification', t('Please enter a valid phone number.'));

            return;
        }

        try {
            $sCode = Verification::issueCode($this->session, $mPhoneNumber);
            if ($sCode === null) {
                \PFBC\Form::setError('form_phone_number_verification', t('Please wait one minute before requesting another code. Maximum five requests per 15 minutes.'));

                return;
            }
            $oSmsApi = SmsGatewayFactory::create($this->config->values['module.setting']['default_sms_gateway']);
            $sTextMessage = t('%0% is your verification code. Do not share it with anyone. Thank you, %site_name%', $sCode);
            $bResponse = $oSmsApi->send($mPhoneNumber, $sTextMessage);
        } catch (\Exception $oException) {
            error_log('SMS verification delivery failed (' . get_class($oException) . ').');
            $bResponse = false;
        }

        if ($bResponse) {
            Header::redirect(
                Uri::get(
                    'sms-verification',
                    'main',
                    'verification'
                )
            );
        } else {
            $this->session->remove([SmsVerificationCore::CODE_SESS_NAME, SmsVerificationCore::PHONE_NUMBER_SESS_NAME]);
            \PFBC\Form::setError(
                'form_phone_number_verification',
                t('Could not send a code. Please wait one minute and try again. If this continues, contact the site administrator.')
            );
        }
    }
}
