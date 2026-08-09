<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Moderation\Filter;
use PH7\Framework\Security\Security;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class JoinFormProcess extends Form
{
    /** @var UserModel */
    private $oUserModel;

    /** @var int */
    private $iActiveType;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserModel();
        $this->iActiveType = (int)DbConfig::getSetting('userActivationType');
    }

    public function step1()
    {
        $iAffId = (int)(new Cookie())->get(AffiliateCore::COOKIE_NAME);

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'first_name' => $this->httpRequest->post('first_name'),
            'reference' => $this->getAffiliateReference(),
            'ip' => Ip::get(),
            'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
            'current_date' => (new CDateTime())->get()->dateTime('Y-m-d H:i:s'),
            'is_active' => $this->iActiveType,
            'group_id' => (int)DbConfig::getSetting('defaultMembershipGroupId'),
            'affiliated_id' => $iAffId
        ];

        // Need to use Http::NO_CLEAN since password might contains special character like "<" and will otherwise be converted to HTML entities
        $sPassword = $this->httpRequest->post('password', Http::NO_CLEAN);
        $aData += ['password' => Security::hashPwd($sPassword)];

        $iTimeDelay = (int)DbConfig::getSetting('timeDelayUserRegistration');
        if (!$this->oUserModel->checkWaitJoin($aData['ip'], $iTimeDelay, $aData['current_date'])) {
            \PFBC\Form::setError('form_join_user', Form::waitRegistrationMsg($iTimeDelay));
        } elseif (!$iProfileId = $this->registerStep1($aData)) {
            \PFBC\Form::setError('form_join_user',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again or come back later.')
            );
        } else {
            /*
             * Update the Affiliate Commission
             * Only if the user's account is already activated
             */
            if ($this->isUserActivated()) {
                AffiliateCore::updateJoinCom($iAffId, $this->config, $this->registry);
            }

            // Send confirmation email
            (new Registration($this->view))->sendMail($aData);

            $aSessData = [
                'mail_step1' => $aData['email'],
                'username' => $aData['username'],
                'first_name' => $aData['first_name'],
                'profile_id' => $iProfileId
            ];
            $this->session->set($aSessData);

            Header::redirect(
                $this->buildSignupStepUrlWithRecoveryToken(
                    'step2',
                    $iProfileId,
                    $aData['hash_validation']
                )
            );
        }
    }

    public function step2()
    {
        $iProfileId = $this->getCurrentProfileId('mail_step1', 'step2');
        if ($iProfileId <= 0) {
            \PFBC\Form::setError('form_join_user2',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again or come back later.')
            );

            return;
        }

        $sBirthDate = $this->getUserBirthDateValue();

        // WARNING FOT "matchSex" FIELD: Be careful, you should use the Http::NO_CLEAN constant, otherwise Http::post() method removes the special tags
        // and damages the SET function SQL for entry into the database
        $aData1 = [
            'sex' => $this->httpRequest->post('sex'),
            'match_sex' => Form::setVal((array)$this->httpRequest->post('match_sex', Http::NO_CLEAN)),
            'birth_date' => $sBirthDate,
            'profile_id' => $iProfileId
        ];

        $aData2 = [
            'country' => $this->httpRequest->post('country'),
            'city' => $this->httpRequest->post('city'),
            'zip_code' => $this->httpRequest->post('zip_code'),
            'profile_id' => $iProfileId
        ];

        if (!$this->oUserModel->exe($aData1, '2_1') || !$this->oUserModel->exe($aData2, '2_2')) {
            \PFBC\Form::setError('form_join_user2',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again or come back later.')
            );
        } else {
            $this->session->set('mail_step2', $this->session->get('mail_step1'));
            $sHashValidation = $this->getHashValidationByProfileId($iProfileId);
            Header::redirect(
                $this->buildSignupStepUrlWithRecoveryToken(
                    'step3',
                    $iProfileId,
                    $sHashValidation
                )
            );
        }
    }

    public function step3()
    {
        $iProfileId = $this->getCurrentProfileId('mail_step2', 'step3');
        if ($iProfileId <= 0) {
            \PFBC\Form::setError('form_join_user3',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again or come back later.')
            );

            return;
        }

        $aData = [
            'description' => $this->httpRequest->post('description', Http::ONLY_XSS_CLEAN),
            'profile_id' => $iProfileId
        ];

        if (!$this->oUserModel->exe($aData, '3')) {
            \PFBC\Form::setError('form_join_user3',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again or come back later.')
            );
        } else {
            $this->session->set('mail_step3', $this->session->get('mail_step1'));
            $sHashValidation = $this->getHashValidationByProfileId($iProfileId);
            Header::redirect(
                $this->buildSignupStepUrlWithRecoveryToken(
                    'step4',
                    $iProfileId,
                    $sHashValidation
                )
            );
        }
    }

    public function step4()
    {
        if (!$this->isAvatarUploaded()) {
            // Automatically skip the uploading process if no photo was uploaded
            $this->session->set('mail_step4', $this->session->get('mail_step1'));
            $this->redirectUserToDonePage();
        } else {
            $iApproved = (int)DbConfig::getSetting('avatarManualApproval') === 0 ? 1 : 0;

            if ($this->isNudityFilterEligible($iApproved) && $this->hasAvatarNudity()) {
                // Overwrite "$iApproved" if avatar doesn't look suitable for anyone
                $iApproved = 0;
            }

            $bAvatar = (new UserCore())->setAvatar(
                $this->session->get('profile_id'),
                $this->session->get('username'),
                $_FILES['avatar']['tmp_name'],
                $iApproved
            );

            if (!$bAvatar) {
                \PFBC\Form::setError('form_join_user4', Form::wrongImgFileTypeMsg());
            } else {
                $this->session->set('mail_step4', $this->session->get('mail_step1'));
                $this->redirectUserToDonePage();
            }
        }
    }

    private function registerStep1(array $aData): int
    {
        try {
            return (int)$this->oUserModel->join($aData);
        } catch (\Throwable $oException) {
            error_log(sprintf('Member registration failed: %s', $oException->getMessage()));

            return 0;
        }
    }

    private function redirectUserToDonePage()
    {
        Header::redirect(
            Uri::get(
                'user',
                'signup',
                'done'
            )
        );
    }

    /**
     * @param int $iApproved
     *
     * @return bool
     */
    private function isNudityFilterEligible($iApproved)
    {
        return $iApproved === 1 && DbConfig::getSetting('nudityFilter');
    }

    /**
     * @return bool
     */
    private function hasAvatarNudity()
    {
        return Filter::isNudity($_FILES['avatar']['tmp_name']);
    }

    /**
     * @return string returns the birthdate depending of what field type is used in the form
     */
    private function getUserBirthDateValue()
    {
        if (DbConfig::getSetting('isUserAgeRangeField')) {
            $iAge = $this->httpRequest->post('age', 'int');
            $oDate = new \DateTime();
            $oDate->modify(sprintf('- %d year', $iAge));
            $sBirthDate = $oDate->format('Y-m-d');
        } else {
            $sBirthDate = $this->dateTime
                ->get(
                    $this->httpRequest->post('birth_date')
                )->date('Y-m-d');
        }

        return $sBirthDate;
    }

    /**
     * @return string
     */
    private function getAffiliateReference()
    {
        $sVariableName = Registration::REFERENCE_VAR_NAME;
        $sRef = $this->session->exists($sVariableName) ? $this->session->get($sVariableName) : t('No reference');
        $this->session->remove($sVariableName);

        return $sRef;
    }

    /**
     * @return bool
     */
    private function isAvatarUploaded()
    {
        return !empty($_FILES['avatar']['tmp_name']);
    }

    /**
     * @return bool
     */
    private function isUserActivated()
    {
        return $this->iActiveType === RegistrationCore::NO_ACTIVATION;
    }

    private function getCurrentProfileId(string $sEmailSessionKey, string $sExpectedStep): int
    {
        $iProfileId = (int)$this->session->get('profile_id');
        if ($iProfileId > 0) {
            return $iProfileId;
        }

        $sEmail = (string)$this->session->get($sEmailSessionKey);
        if (!empty($sEmail)) {
            return (int)$this->oUserModel->getId($sEmail);
        }

        return $this->recoverProfileIdFromToken($sExpectedStep);
    }

    private function recoverProfileIdFromToken(string $sExpectedStep): int
    {
        $iProfileId = $this->httpRequest->get(Registration::SIGNUP_RECOVERY_PROFILE_ID_PARAM, 'int');
        $sRecoveryToken = $this->httpRequest->get(Registration::SIGNUP_RECOVERY_TOKEN_PARAM);

        if ($iProfileId <= 0 || empty($sRecoveryToken)) {
            return 0;
        }

        $oProfile = $this->oUserModel->readProfile($iProfileId, DbTableName::MEMBER);
        if (empty($oProfile) || empty($oProfile->hashValidation)) {
            return 0;
        }

        $sHashValidation = (string)$oProfile->hashValidation;
        if (!$this->isValidRecoveryToken($iProfileId, $sHashValidation, (string)$sRecoveryToken, $sExpectedStep)) {
            return 0;
        }

        $aSessionData = [
            'mail_step1' => $oProfile->email,
            'username' => $oProfile->username,
            'first_name' => $oProfile->firstName,
            'profile_id' => $iProfileId
        ];

        if ($sExpectedStep === 'step3' || $sExpectedStep === 'step4') {
            $aSessionData['mail_step2'] = $oProfile->email;
        }

        if ($sExpectedStep === 'step4') {
            $aSessionData['mail_step3'] = $oProfile->email;
        }

        $this->session->set($aSessionData);

        return $iProfileId;
    }

    private function buildSignupStepUrlWithRecoveryToken(string $sStep, int $iProfileId, string $sHashValidation): string
    {
        if (empty($sHashValidation)) {
            return Uri::get('user', 'signup', $sStep);
        }

        $sToken = Registration::buildSignupRecoveryToken($iProfileId, $sHashValidation, $sStep);

        return Uri::get('user', 'signup', $sStep) . '?' . http_build_query([
            Registration::SIGNUP_RECOVERY_PROFILE_ID_PARAM => $iProfileId,
            Registration::SIGNUP_RECOVERY_TOKEN_PARAM => $sToken
        ]);
    }

    private function getHashValidationByProfileId(int $iProfileId): string
    {
        $oProfile = $this->oUserModel->readProfile($iProfileId, DbTableName::MEMBER);

        return !empty($oProfile) && !empty($oProfile->hashValidation) ? (string)$oProfile->hashValidation : '';
    }

    private function isValidRecoveryToken(int $iProfileId, string $sHashValidation, string $sRecoveryToken, string $sExpectedStep): bool
    {
        $sExpectedToken = Registration::buildSignupRecoveryToken($iProfileId, $sHashValidation, $sExpectedStep);

        return hash_equals($sExpectedToken, $sRecoveryToken);
    }
}
