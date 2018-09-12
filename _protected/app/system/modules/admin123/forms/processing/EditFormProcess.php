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
    /** @var int */
    private $iProfileId;

    /** @var bool */
    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        $oAdminModel = new AdminModel;

        $this->iProfileId = $this->getProfileId();
        $oAdmin = $oAdminModel->readProfile($this->iProfileId, DbTableName::ADMIN);

        if (!$this->str->equals($this->httpRequest->post('username'), $oAdmin->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!(new Validate)->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength, DbTableName::ADMIN)) {
                \PFBC\Form::setError('form_admin_edit_account', t('Username has to be from %0% to %1% characters long, or it is not available, or already taken by another admin.', $iMinUsernameLength, $iMaxUsernameLength));
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile('username', $this->httpRequest->post('username'), $this->iProfileId, DbTableName::ADMIN);
                $this->session->set('admin_username', $this->httpRequest->post('username'));

                $this->clearFieldCache('username');
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oAdmin->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'), DbTableName::ADMIN)) {
                \PFBC\Form::setError('form_admin_edit_account', t('Invalid email or already used by another admin.'));
                $this->bIsErr = true;
            } else {
                $oAdminModel->updateProfile('email', $this->httpRequest->post('mail'), $this->iProfileId, DbTableName::ADMIN);
                $this->session->set('admin_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAdmin->firstName)) {
            $oAdminModel->updateProfile('firstName', $this->httpRequest->post('first_name'), $this->iProfileId, DbTableName::ADMIN);
            $this->session->set('admin_first_name', $this->httpRequest->post('first_name'));

            $this->clearFieldCache('firstName');
        }

        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAdmin->lastName)) {
            $oAdminModel->updateProfile('lastName', $this->httpRequest->post('last_name'), $this->iProfileId, DbTableName::ADMIN);
        }

        if (!$this->str->equals($this->httpRequest->post('sex'), $oAdmin->sex)) {
            $oAdminModel->updateProfile('sex', $this->httpRequest->post('sex'), $this->iProfileId, DbTableName::ADMIN);

            $this->clearFieldCache('sex');
        }

        if (!$this->str->equals($this->httpRequest->post('time_zone'), $oAdmin->timeZone)) {
            $oAdminModel->updateProfile('timeZone', $this->httpRequest->post('time_zone'), $this->iProfileId, DbTableName::ADMIN);
        }

        $oAdminModel->setLastEdit($this->iProfileId, DbTableName::ADMIN);

        unset($oAdminModel, $oAdmin);

        (new Admin)->clearReadProfileCache($this->iProfileId, DbTableName::ADMIN);

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess('form_admin_edit_account', t('Profile successfully updated!'));
        }
    }

    /**
     * @return string
     */
    private function getProfileId()
    {
        if ($this->isNotRootAdmin()) { // Prohibit other admins to edit Root Admin (ID 1)
            return $this->httpRequest->get('profile_id', 'int');
        }

        return $this->session->get('admin_id');
    }

    /**
     * @return bool
     */
    private function isNotRootAdmin()
    {
        return $this->httpRequest->getExists('profile_id') &&
            !AdminCore::isRootProfileId($this->httpRequest->get('profile_id', 'int'));
    }

    /**
     * @param string $sCacheId
     */
    private function clearFieldCache($sCacheId)
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            $sCacheId . $this->iProfileId . DbTableName::ADMIN,
            null
        )->clear();
    }
}
