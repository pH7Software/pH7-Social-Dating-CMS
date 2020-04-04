<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Form / Processing
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class VerificationFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iProfileId = $this->session->get(SmsVerificationCore::PROFILE_ID_SESS_NAME);
        if ($this->isVerificationCodeValid($iProfileId)) {
            $oUser = new UserCore;
            $oUserModel = new UserCoreModel;
            $this->setPhoneNumberToDb($iProfileId, $oUserModel);
            $this->approveUser($iProfileId, $oUserModel);

            // Clear "active" DB field from the cache
            $oUser->clearReadProfileCache($iProfileId);

            $oUserData = $oUserModel->readProfile($iProfileId);

            $oRememberMe = new RememberMeCore;
            if ($oRememberMe->isEligible($this->session)) {
                $oRememberMe->enableSession($oUserData);
            }
            unset($oRememberMe);

            $oUser->setAuth(
                $oUserData,
                $oUserModel,
                $this->session,
                new SecurityModel
            );
            unset($oUserModel);

            Header::redirect(
                Uri::get('user', 'account', 'index'),
                t('Congratulations! Your phone number has been successfully verified.')
            );

        } else {
            \PFBC\Form::setError(
                'form_sms_verification',
                t('The SMS verification code is invalid. <a href="%0%">Try to resend a new one?</a>', Uri::get('sms-verification', 'main', 'send'))
            );
        }
    }

    /**
     * @param int $iProfileId
     * @param UserCoreModel $oUserModel
     */
    private function setPhoneNumberToDb($iProfileId, UserCoreModel $oUserModel)
    {
        $sPhoneNumber = $this->session->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME);

        $oUserModel->updateProfile(
            'phone',
            $sPhoneNumber,
            $iProfileId,
            DbTableName::MEMBER_INFO
        );
    }

    /**
     * Validate the user (once its phone number has been verified).
     *
     * @param int $iProfileId
     * @param UserCoreModel $oUserModel
     */
    private function approveUser($iProfileId, UserCoreModel $oUserModel)
    {
        $oUserModel->approve(
            $iProfileId,
            1
        );
    }

    /**
     * @param int $iProfileId
     *
     * @return bool
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function isVerificationCodeValid($iProfileId)
    {
        return $this->httpRequest->post('verification_code') === Verification::getVerificationCode($iProfileId);
    }
}
