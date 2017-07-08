<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

/** For "user", "affiliate" and "admin" modules **/
class ChangePasswordCoreFormProcess extends Form
{
    /**
     * @internal Need to use Http::NO_CLEAN arg in Http::post() since password might contains special character like "<" and will otherwise be converted to HTML entities
     */
    public function __construct()
    {
        parent::__construct();

        // PH7\UserCoreModel::login() method of the UserCoreModel Class works only for "user" and "affiliate" module.
        $sClassName = ($this->registry->module == PH7_ADMIN_MOD) ? AdminModel::class : UserCoreModel::class;
        $oPasswordModel = new $sClassName;

        $sEmail = ($this->registry->module == PH7_ADMIN_MOD ? $this->session->get('admin_email') : ($this->registry->module == 'user' ? $this->session->get('member_email') : $this->session->get('affiliate_email')));
        $sTable = ($this->registry->module == PH7_ADMIN_MOD ? 'Admins' : ($this->registry->module == 'user' ? 'Members' : 'Affiliates'));
        $sMod = ($this->registry->module == PH7_ADMIN_MOD ? PH7_ADMIN_MOD : ($this->registry->module == 'user' ? 'user' : 'affiliate'));
        $sAction = ($this->registry->module == 'affiliate') ? 'home' : 'main';

        // Login
        if ($this->registry->module == PH7_ADMIN_MOD) {
            $mLogin = $oPasswordModel->adminLogin(
                $sEmail,
                $this->session->get('admin_username'),
                $this->httpRequest->post('old_password', Http::NO_CLEAN)
            );
        } else {
            $mLogin = $oPasswordModel->login(
                $sEmail,
                $this->httpRequest->post('old_password', Http::NO_CLEAN),
                $sTable
            );
        }

        // Check
        if ($this->httpRequest->post('new_password', Http::NO_CLEAN) !== $this->httpRequest->post('new_password2', Http::NO_CLEAN)) {
            \PFBC\Form::setError('form_change_password', t('The passwords do not match.'));
        } elseif ($this->httpRequest->post('old_password', Http::NO_CLEAN) === $this->httpRequest->post('new_password', Http::NO_CLEAN)) {
            \PFBC\Form::setError('form_change_password', t('The old and new passwords are identical. So why do you change your password?'));
        } elseif ($mLogin !== true) {
            \PFBC\Form::setError('form_change_password', t('The old password is not correct.'));
        } else {
            // Regenerate the session ID to prevent session fixation attack
            $this->session->regenerateId();

            // Update the password
            $oPasswordModel->changePassword($sEmail, $this->httpRequest->post('new_password', Http::NO_CLEAN), $sTable);
            \PFBC\Form::setSuccess('form_change_password', t('Your password has been successfully changed.'));
        }
    }
}
