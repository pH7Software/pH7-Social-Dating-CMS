<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Form / Processing
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class PhoneNumberFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sPhoneNumber = $this->httpRequest->post('phone_number');
        $iProfileId = $this->session->get(SmsVerificationCore::PROFILE_ID_SESS_NAME);
        $oSmsApi = SmsGatewayFactory::create($this->config->values['module.setting']['default_sms_gateway']);
        $bResult = $oSmsApi->send(
            $sPhoneNumber,
            t('%0% is your verification code. Do not share it with anyone. Thank you, %site_name%', Verification::getVerificationCode($iProfileId))
        );

        if ($bResult) {
            $this->session->set(SmsVerificationCore::PHONE_NUMBER_SESS_NAME, $sPhoneNumber);

            Header::redirect(
                Uri::get(
                    'sms-verification',
                    'main',
                    'verification'
                )
            );
        } else {
            \PFBC\Form::setError(
                'form_phone_number_verification',
                t('An error occurred while sending the verification text. Please try again.')
            );
        }
    }
}
