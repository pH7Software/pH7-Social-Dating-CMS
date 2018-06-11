<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
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
    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        $oValidate = new Validate;
        $oAdminModel = new AdminModel;

        $iProfileId = $this->getProfileId();
        $oAdmin = $oAdminModel->readProfile($iProfileId, DbTableName::ADMIN);

        if (!$this->str->equals($this->httpRequest->post('username'), $oAdmin->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!$oValidate->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength)) {
                \PFBC\Form::setError('form_admin_edit_account', t('Your username has to contain from %0% to %1% characters, your username is not available or your username already used by other admin.', $iMinUsernameLength, $iMaxUsernameLength));
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile('username', $this->httpRequest->post('username'), $iProfileId, DbTableName::ADMIN);
                $this->session->set('admin_username', $this->httpRequest->post('username'));

                (new Cache)->start(UserCoreModel::CACHE_GROUP, 'username' . $iProfileId . DbTableName::ADMIN, null)->clear();
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oAdmin->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'))) {
                \PFBC\Form::setError('form_admin_edit_account', t('Invalid email address or this email is already used by another admin.'));
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile('email', $this->httpRequest->post('mail'), $iProfileId, DbTableName::ADMIN);
                $this->session->set('admin_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAdmin->firstName)) {
            $oAdminModel->updateProfile('firstName', $this->httpRequest->post('first_name'), $iProfileId, DbTableName::ADMIN);
            $this->session->set('admin_first_name', $this->httpRequest->post('first_name'));

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'firstName' . $iProfileId . DbTableName::ADMIN, null)->clear();
        }

        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAdmin->lastName)) {
            $oAdminModel->updateProfile('lastName', $this->httpRequest->post('last_name'), $iProfileId, DbTableName::ADMIN);
        }

        if (!$this->str->equals($this->httpRequest->post('sex'), $oAdmin->sex)) {
            $oAdminModel->updateProfile('sex', $this->httpRequest->post('sex'), $iProfileId, DbTableName::ADMIN);

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'sex' . $iProfileId . DbTableName::ADMIN, null)->clear();
        }

        if (!$this->str->equals($this->httpRequest->post('time_zone'), $oAdmin->timeZone)) {
            $oAdminModel->updateProfile('timeZone', $this->httpRequest->post('time_zone'), $iProfileId, DbTableName::ADMIN);
        }

        $oAdminModel->setLastEdit($iProfileId, DbTableName::ADMIN);

        unset($oValidate, $oAdminModel, $oAdmin);

        (new Admin)->clearReadProfileCache($iProfileId, DbTableName::ADMIN);

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess('form_admin_edit_account', t('Profile successfully updated!'));
        }
    }

    /**
     * @return string
     */
    private function getProfileId()
    {
        // Prohibit other admins to edit the Root Administrator (ID 1)
        if ($this->httpRequest->getExists('profile_id') &&
            !AdminCore::isRootProfileId($this->httpRequest->get('profile_id', 'int'))
        ) {
            return $this->httpRequest->get('profile_id', 'int');
        }

        return $this->session->get('admin_id');
    }
}
