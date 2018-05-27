<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditForm
{
    public static function display()
    {
        if (isset($_POST['submit_user_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_user_edit_account'])) {
                new EditFormProcess;
            }
            Header::redirect();
        }

        $bAdminLogged = (AdminCore::auth() && !User::auth()); // Check if the admin is logged

        $oUserModel = new UserModel;
        $oHR = new HttpRequest;
        $iProfileId = ($bAdminLogged && $oHR->getExists('profile_id')) ? $oHR->get('profile_id', 'int') : (new Session)->get('member_id');

        $oUser = $oUserModel->readProfile($iProfileId);

        // Birth Date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oUser->birthDate)->date('Y-m-d');

        $oForm = new \PFBC\Form('form_user_edit_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_user_edit_account', 'form_user_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if ($bAdminLogged && $oHR->getExists('profile_id')) {
            $oForm->addElement(
                new \PFBC\Element\HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'user', 'browse') . '">' . t('Back to Browse Users') . '</a></p>')
            );

            $oMemberships = (new AdminCoreModel)->getMemberships();
            $aGroupName = [];
            foreach ($oMemberships as $oGroup) {
                // Retrieve only the activated memberships
                if ($oGroup->enable == 1) {
                    $aGroupName[$oGroup->groupId] = $oGroup->name;
                }
            }
            $oForm->addElement(new \PFBC\Element\Select(t('Membership Group:'), 'group_id', $aGroupName, ['value' => $oUser->groupId, 'required' => 1]));
            unset($aGroupName);
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', ['id' => 'name_first', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oUser->firstName, 'required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_first"></span>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oUser->lastName, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_last"></span>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Nickname:'), 'username', ['description' => t('For security reasons, you cannot change your username.'), 'disabled' => 'disabled', 'value' => $oUser->username]));

        $oForm->addElement(new \PFBC\Element\Email(t('Email:'), 'mail', ['description' => t('For security reasons and to avoid spam, you cannot change your email address.'), 'disabled' => 'disabled', 'value' => $oUser->email]));

        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', ['female' => t('Woman'), 'male' => t('Man'), 'couple' => t('Couple')], ['value' => $oUser->sex, 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for a:'), 'match_sex', ['male' => t('Man'), 'female' => t('Woman'), 'couple' => t('Couple')], ['value' => Form::getVal($oUser->matchSex), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Date(t('Date of birth:'), 'birth_date', ['id' => 'birth_date', 'onblur' => 'CValid(this.value, this.id)', 'value' => $sBirthDate, 'validation' => new \PFBC\Validation\BirthDate, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));

        // Generate dynamic fields
        $oFields = $oUserModel->getInfoFields($iProfileId);
        foreach ($oFields as $sColumn => $sValue) {
            $oForm = (new DynamicFieldCoreForm($oForm, $sColumn, $sValue))->generate();
        }

        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }
}
