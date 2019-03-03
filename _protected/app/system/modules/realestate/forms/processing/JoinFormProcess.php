<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
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

        $this->oUserModel = new UserModel;
        $this->iActiveType = DbConfig::getSetting('userActivationType');
    }

    public function step1()
    {
        $iAffId = (int)(new Cookie)->get(AffiliateCore::COOKIE_NAME);

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'sex' => $this->httpRequest->post('sex'),
            'first_name' => $this->httpRequest->post('first_name'),
            'city' => $this->httpRequest->post('city'),
            'zipCode' => $this->httpRequest->post('zipCode'),
            'reference' => $this->getAffiliateReference(),
            'ip' => Ip::get(),
            'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
            'current_date' => (new CDateTime)->get()->dateTime('Y-m-d H:i:s'),
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
            if ($this->isUserActivated()) {
                AffiliateCore::updateJoinCom($iAffId, $this->config, $this->registry);
            }

            // Send email
            (new Registration($this->view))->sendMail($aData);

            $aSessData = [
                'mail_step1' => $aData['email'],
                'username' => $aData['username'],
                'first_name' => $aData['first_name'],
                'profile_id' => $iProfileId
            ];
            $this->session->set($aSessData);

            // Don't show avatar field for Buyers
            if ($aData['sex'] === 'buyer') {
                $this->session->set('mail_step2', $this->session->get('mail_step1'));
                $this->redirectUserToDonePage();
            } else {
                Header::redirect(
                    Uri::get('realestate', 'signup', 'step2')
                );
            }
        }
    }

    public function step2()
    {
        $iProfileId = $this->oUserModel->getId($this->session->get('mail_step1'));

        $aData = [
            'profile_id' => $iProfileId,
            'price' => $this->httpRequest->post('price', 'int'),
            'bedrooms' => $this->httpRequest->post('bedrooms', 'int'),
            'bathrooms' => $this->httpRequest->post('bathrooms', 'int'),
            'house_size' => $this->httpRequest->post('size', 'int'),
            'year_built' => $this->httpRequest->post('year_built', 'int'),
            'home_type' => $this->httpRequest->post('home_type'),
            'home_style' => $this->httpRequest->post('home_style'),
            'square_ft' => $this->httpRequest->post('square_ft', 'int'),
            'lot_size' => $this->httpRequest->post('lot_size', 'int'),
            'garage_spaces' => $this->httpRequest->post('garage_spaces', 'int'),
            'carport_spaces' => $this->httpRequest->post('carport_spaces', 'int'),
        ];

        if (!$this->oUserModel->exe($aData, '2')) {
            \PFBC\Form::setError('form_join_user2',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again with new information in the form fields or come back later.')
            );
        } else {
            $this->session->set('mail_step2', $this->session->get('mail_step1'));
            Header::redirect(
                Uri::get('realestate', 'signup', 'step3')
            );
        }
    }

    public function step3()
    {
        if (!$this->isAvatarUploaded()) {
            // Automatically skip the uploading process if no photo was uploaded
            $this->session->set('mail_step3', $this->session->get('mail_step2'));
            $this->redirectUserToDonePage();
        } else {
            $iApproved = DbConfig::getSetting('avatarManualApproval') == 0 ? 1 : 0;

            if ($this->isNudityFilterEligible($iApproved) && $this->hasAvatarNudity()) {
                // Overwrite "$iApproved" if avatar doesn't look suitable for anyone
                $iApproved = 0;
            }

            $bAvatar = (new UserCore)->setAvatar(
                $this->session->get('profile_id'),
                $this->session->get('username'),
                $_FILES['avatar']['tmp_name'],
                $iApproved
            );

            if (!$bAvatar) {
                \PFBC\Form::setError('form_join_user3', Form::wrongImgFileTypeMsg());
            } else {
                $this->session->set('mail_step3', $this->session->get('mail_step2'));
                $this->redirectUserToDonePage();
            }
        }
    }

    private function redirectUserToDonePage()
    {
        Header::redirect(
            Uri::get(
                'realestate',
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
        return $this->iActiveType == 1;
    }
}
