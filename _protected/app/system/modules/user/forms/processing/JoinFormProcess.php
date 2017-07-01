<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Security;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Util\Various;
use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Ip\Ip;
use DAT\Tools\Client\Registration as Register;
use DAT\Service\TAC\EveFlirt;
use DAT\Service\Identifier\Affiliate as AffiliateId;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class JoinFormProcess extends Form
{
    const PARTNER_AFF_VAR_NAME = 'partner_register';
    const PARTNER_AFF_ID = 645555;

    /** @var UserModel */
    private $oUserModel;

    /** @var integer */
    private $iActiveType;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserModel;
        $this->iActiveType = DbConfig::getSetting('userActivationType');
    }

    public function step1()
    {
        $iAffId = (int) (new Cookie)->get(AffiliateCore::COOKIE_NAME);

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'first_name' => $this->httpRequest->post('first_name'),
            'reference' => $this->getAffiliateRefence(),
            'ip' => Ip::get(),
            'hash_validation' => Various::genRnd(),
            'current_date' => (new CDateTime)->get()->dateTime('Y-m-d H:i:s'),
            'is_active' => $this->iActiveType,
            'group_id' => (int) DbConfig::getSetting('defaultMembershipGroupId'),
            'affiliated_id' => $iAffId
        ];

        // Need to use Http::NO_CLEAN since password might contains special character like "<" and will otherwise be converted to HTML entities
        $sPassword = $this->httpRequest->post('password', Http::NO_CLEAN);
        $aData += ['password' => Security::hashPwd($sPassword)];

        $iTimeDelay = (int) DbConfig::getSetting('timeDelayUserRegistration');
        if (!$this->oUserModel->checkWaitJoin($aData['ip'], $iTimeDelay, $aData['current_date'])) {
            \PFBC\Form::setError('form_join_user', Form::waitRegistrationMsg($iTimeDelay));
        } elseif (!$iProfileId = $this->oUserModel->join($aData)) {
            \PFBC\Form::setError('form_join_user',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again with new information in the form fields or come back later.')
            );
        } else {
            /**
             * Update the Affiliate Commission
             * Only if the user's account is already activated
             */
            if ($this->iActiveType == 0) {
                AffiliateCore::updateJoinCom($iAffId, $this->config, $this->registry);
            }

            if ($this->httpRequest->postExists(self::PARTNER_AFF_VAR_NAME)) {
                $this->session->set(self::PARTNER_AFF_VAR_NAME, 1);
            }

            // Send email
            (new Registration)->sendMail($aData);

            $aSessData = [
                'mail_step1' => $aData['email'],
                'username' => $aData['username'],
                'first_name' => $aData['first_name'],
                'profile_id' => $iProfileId
            ];
            $this->session->set($aSessData);

            Header::redirect(Uri::get('user','signup','step2'));
        }
    }

    public function step2()
    {
        $iProfileId = $this->oUserModel->getId($this->session->get('mail_step1'));
        $sBirthDate = $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d');

        // WARNING FOT "matchSex" FIELD: Be careful, you should use the Http::NO_CLEAN constant, otherwise Http::post() method removes the special tags
        // and damages the SET function SQL for entry into the database
        $aData1 = [
            'sex' => $this->httpRequest->post('sex'),
            'match_sex' => Form::setVal($this->httpRequest->post('match_sex', Http::NO_CLEAN)),
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
                t('Please try again with new information in the form fields or come back later.')
            );
        } else {
            if ($this->session->exists(self::PARTNER_AFF_VAR_NAME)) {
                // If we got the authorization from the user, we register their to a partner service
                $aData = $aData1 + $aData2;
                $this->addUserToPartnerService($aData);
            }

            $this->session->set('mail_step2', $this->session->get('mail_step1'));
            Header::redirect(Uri::get('user','signup','step3'));
        }
    }

    public function step3()
    {
        $aData = [
            'description' => $this->httpRequest->post('description', Http::ONLY_XSS_CLEAN),
            'profile_id' => $this->oUserModel->getId($this->session->get('mail_step2'))
        ];

        if (!$this->oUserModel->exe($aData, '3')) {
            \PFBC\Form\setError('form_join_user3',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again with new information in the form fields or come back later.')
            );
        } else {
            // Registered successfully in database for step 3!
            $this->session->set('mail_step3', $this->session->get('mail_step1'));
            Header::redirect(Uri::get('user','signup','step4'), t('Your account has just been created!'));
        }
    }

    public function step4()
    {
        // If no photo added from the form, automatically skip this step
        if (empty($_FILES['avatar']['tmp_name'])) {
            Header::redirect(Uri::get('user','signup','done'));
        }

        $iApproved = (DbConfig::getSetting('avatarManualApproval') == 0) ? '1' : '0';
        $bAvatar = (new UserCore)->setAvatar($this->session->get('profile_id'), $this->session->get('username'), $_FILES['avatar']['tmp_name'], $iApproved);

        if (!$bAvatar) {
            \PFBC\Form::setError('form_join_user4', Form::wrongImgFileTypeMsg());
        } else {
            Header::redirect(Uri::get('user', 'signup', 'done'));
        }
    }

    /**
     * @return string
     */
    private function getAffiliateRefence()
    {
        $sVariableName = Registration::REFERENCE_VAR_NAME;
        $sRef = $this->session->exists($sVariableName) ? $this->session->get($sVariableName) : t('No reference');
        $this->session->remove($sVariableName);

        return $sRef;
    }

    /**
     * @param array $aData
     *
     * @return void
     */
    private function addUserToPartnerService(array $aData)
    {
        $aBirthDate = explode('-', $aData['birth_date']); // "Y-m-d" pattern

        $aUserData = [
            EveFlirt::PLATFORM_FIELD => 'desktop',
            EveFlirt::TOS_FIELD => 'on',
            EveFlirt::EMAIL_FIELD => $this->session->get('mail_step1'),
            EveFlirt::USERNAME_FIELD => $this->session->get('username'),
            EveFlirt::FIRSTNAME_FIELD => $this->session->get('first_name'),
            EveFlirt::GENDER_FIELD => ($aData['sex'] === 'male' ? 1 : 2),
            EveFlirt::BIRTH_YEAR_FIELD => $aBirthDate[0],
            EveFlirt::BIRTH_MONTH_FIELD => $aBirthDate[1],
            EveFlirt::BIRTH_DAY_FIELD => $aBirthDate[2],
            EveFlirt::COUNTRY_FIELD => $aData['country'],
            EveFlirt::CITY_FIELD => $aData['city'],
            EveFlirt::POSTALCODE_FIELD => $aData['zip_code'],

            // For security reason, we don't want to send the user's password
            EverFlirt::PASSWORD_FIELD => Various::genRnd(null, EveFlirt::MAX_PASSWORD_LENGTH)
        ];

        $oAffiliateId = new AffiliateId(self::PARTNER_AFF_ID);
        $oEveFlirt = new EveFlirt($oAffiliateId);

        (new Register($oEveFlirt, $aUserData))->send();
    }
}