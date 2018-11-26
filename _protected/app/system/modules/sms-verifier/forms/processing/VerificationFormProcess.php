<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Form / Processing
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class VerificationFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iProfileId = $this->session->get(SmsVerificationCore::PROFILE_ID_SESS_NAME);
        if ($this->isVerificationCodeValid($iProfileId)) {
            $oUserModel = new UserCoreModel;
            $sPhoneNumber = $this->session->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME);

            $oUserModel->approve(
                $iProfileId,
                1
            );

            // Add phone number to DB field
            $oUserModel->updateProfile(
                'phone',
                $sPhoneNumber,
                $iProfileId,
                DbTableName::MEMBER_INFO
            );

            // Clear "active" DB field from the cache
            (new UserCore)->clearReadProfileCache($iProfileId);

            unset($oUserModel);

            Header::redirect(
                Uri::get('realestate', 'main', 'login'),
                t('Congratulations! Your phone number has been successfully verified. You can now login.')
            );
        } else {
            \PFBC\Form::setError(
                'form_sms_verification',
                t('The SMS verification code is invalid. <a href="%0%">Try to resend a new one?</a>', Uri::get('sms-verifier', 'main', 'send'))
            );
        }
    }

    /**
     * @param int $iProfileId
     *
     * @return bool
     */
    private function isVerificationCodeValid($iProfileId)
    {
        return $this->httpRequest->post('verification_code') === Verification::getVerificationCode($iProfileId);
    }
}
