<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;

class EditFormProcess extends Form
{
    /** @var bool */
    private $bIsErr = false;

    /**
     * @param int $iProfileId
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($iProfileId)
    {
        parent::__construct();

        $oAdminModel = new AdminModel;

        $oAdmin = $oAdminModel->readProfile($iProfileId, DbTableName::ADMIN);

        if (!$this->str->equals($this->httpRequest->post('username'), $oAdmin->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!(new Validate)->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength, DbTableName::ADMIN)) {
                \PFBC\Form::setError(
                    'form_admin_edit_account',
                    t('Username has to be from %0% to %1% characters long, or it is not available, or already taken by another admin.', $iMinUsernameLength, $iMaxUsernameLength)
                );
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile(
                    'username',
                    $this->httpRequest->post('username'),
                    $iProfileId,
                    DbTableName::ADMIN
                );
                $this->session->set('admin_username', $this->httpRequest->post('username'));

                $this->clearFieldCache('username', $iProfileId);
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oAdmin->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'), DbTableName::ADMIN)) {
                \PFBC\Form::setError(
                    'form_admin_edit_account',
                    t('Invalid email or already used by another admin.')
                );
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile(
                    'email',
                    $this->httpRequest->post('mail'),
                    $iProfileId,
                    DbTableName::ADMIN
                );
                $this->session->set('admin_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAdmin->firstName)) {
            $oAdminModel->updateProfile(
                'firstName',
                $this->httpRequest->post('first_name'),
                $iProfileId,
                DbTableName::ADMIN
            );
            $this->session->set('admin_first_name', $this->httpRequest->post('first_name'));

            $this->clearFieldCache('firstName', $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAdmin->lastName)) {
            $oAdminModel->updateProfile(
                'lastName',
                $this->httpRequest->post('last_name'),
                $iProfileId,
                DbTableName::ADMIN
            );
        }

        if (!$this->str->equals($this->httpRequest->post('sex'), $oAdmin->sex)) {
            $oAdminModel->updateProfile(
                'sex',
                $this->httpRequest->post('sex'),
                $iProfileId,
                DbTableName::ADMIN
            );

            $this->clearFieldCache('sex', $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('time_zone'), $oAdmin->timeZone)) {
            $oAdminModel->updateProfile(
                'timeZone',
                $this->httpRequest->post('time_zone'),
                $iProfileId,
                DbTableName::ADMIN
            );
        }

        $oAdminModel->setLastEdit($iProfileId, DbTableName::ADMIN);

        unset($oAdminModel, $oAdmin);

        (new Admin)->clearReadProfileCache($iProfileId, DbTableName::ADMIN);

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess(
                'form_admin_edit_account',
                t('Profile successfully updated!')
            );
        }
    }

    /**
     * @param string $sCacheId
     * @param int $iProfileId
     *
     * @return void
     */
    private function clearFieldCache($sCacheId, $iProfileId)
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            $sCacheId . $iProfileId . DbTableName::ADMIN,
            null
        )->clear();
    }
}
