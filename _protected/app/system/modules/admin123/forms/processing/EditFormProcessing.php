<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class EditFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oAdminModel = new AdminModel;
        // Prohibit other administrators to edit the Root Administrator (ID 1)
        $iProfileId = ($this->httpRequest->getExists('profile_id') && $this->httpRequest->get('profile_id', 'int') !== 1) ? $this->httpRequest->get('profile_id', 'int') : $this->session->get('admin_id');
        $oAdmin = $oAdminModel->readProfile($iProfileId, 'Admins');

        if (!$this->str->equals($this->httpRequest->post('username'), $oAdmin->username)) {
            $oAdminModel->updateProfile('username', $this->httpRequest->post('username'), $iProfileId, 'Admins');
            $this->session->set('admin_username', $this->httpRequest->post('username'));

            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'username' . $iProfileId . 'Admins', null)->clear();
        }
        if (!$this->str->equals($this->httpRequest->post('mail'), $oAdmin->email)) {
            $oAdminModel->updateProfile('email', $this->httpRequest->post('mail'), $iProfileId, 'Admins');
            $this->session->set('admin_email', $this->httpRequest->post('mail'));
        }
        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAdmin->firstName)) {
            $oAdminModel->updateProfile('firstName', $this->httpRequest->post('first_name'), $iProfileId, 'Admins');
            $this->session->set('admin_first_name', $this->httpRequest->post('first_name'));

            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'firstName' . $iProfileId . 'Admins', null)->clear();
        }
        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAdmin->lastName)) {
            $oAdminModel->updateProfile('lastName', $this->httpRequest->post('last_name'), $iProfileId, 'Admins');
        }
        if (!$this->str->equals($this->httpRequest->post('sex'), $oAdmin->sex)) {
            $oAdminModel->updateProfile('sex', $this->httpRequest->post('sex'), $iProfileId, 'Admins');

            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'sex' . $iProfileId . 'Admins', null)->clear();
        }

        $oAdminModel->setLastEdit($iProfileId, 'Admins');

        unset($oAdminModel, $oAdmin);

        (new Admin)->clearReadProfileCache($iProfileId, 'Admins');

        \PFBC\Form::setSuccess('form_edit_account', t('Your profile has been saved successfully!'));
    }

}
