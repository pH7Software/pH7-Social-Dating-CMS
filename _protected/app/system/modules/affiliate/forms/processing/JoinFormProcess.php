<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Util\Various;

class JoinFormProcess extends Form
{
    private int $iActiveType;

    public function __construct()
    {
        parent::__construct();

        $this->iActiveType = (int)DbConfig::getSetting('affActivationType');
    }

    public function step1(): void
    {
        $sBirthDate = $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d');
        $iAffId = (int)(new Cookie)->get(AffiliateCore::COOKIE_NAME);

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'password' => $this->httpRequest->post('password', Http::NO_CLEAN),
            'first_name' => $this->httpRequest->post('first_name'),
            'last_name' => $this->httpRequest->post('last_name'),
            'sex' => $this->httpRequest->post('sex'),
            'birth_date' => $sBirthDate,
            'country' => $this->httpRequest->post('country'),
            'city' => $this->httpRequest->post('city'),
            'state' => $this->httpRequest->post('state'),
            'zip_code' => $this->httpRequest->post('zip_code'),
            'ip' => Ip::get(),
            'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
            'current_date' => (new CDateTime)->get()->dateTime(UserCoreModel::DATETIME_FORMAT),
            'is_active' => $this->iActiveType,
            'affiliated_id' => $iAffId
        ];

        $oAffModel = new AffiliateModel;

        $iTimeDelay = (int)DbConfig::getSetting('timeDelayAffRegistration');
        if (!$oAffModel->checkWaitJoin($aData['ip'], $iTimeDelay, $aData['current_date'], DbTableName::AFFILIATE)) {
            \PFBC\Form::setError('form_join_aff', Form::waitRegistrationMsg($iTimeDelay));
        } elseif (!$oAffModel->join($aData)) {
            \PFBC\Form::setError('form_join_aff',
                t('An error occurred during registration!') . '<br />' .
                t('Please try again with new information in the form fields or come back later.')
            );
        } else {
            $oRegistration = new Registration($this->view);

            /** Update the Affiliate Commission **/
            if ($this->isUserActivated()) {
                AffiliateCore::updateJoinCom($iAffId, $this->config, $this->registry);
            }

            // Send an email confirming the account registration
            $oRegistration->sendMail($aData);

            \PFBC\Form::setSuccess(
                'form_join_aff',
                $oRegistration->getMsg()
            );
        }

        unset($oAffModel);
    }

    private function isUserActivated(): bool
    {
        return $this->iActiveType === RegistrationCore::NO_ACTIVATION;
    }
}
