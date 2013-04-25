<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

use
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Date\CDateTime;

class EditForm
{

    public static function display()
    {
        if (isset($_POST['submit_user_edit_account']))
        {
            if (\PFBC\Form::isValid($_POST['submit_user_edit_account']))
                new EditFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $oUserModel = new UserModel;
        $oHR = new HttpRequest;
        $iProfileId = (AdminCore::auth() && !User::auth() && $oHR->getExists('profile_id')) ? $oHR->get('profile_id', 'int') : (new Session)->get('member_id');

        $oUser = $oUserModel->readProfile($iProfileId);


        // Birth Date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oUser->birthDate)->date('m/d/Y');

        $oForm = new \PFBC\Form('form_user_edit_account', 650);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_user_edit_account', 'form_user_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if (AdminCore::auth() && !User::auth() && $oHR->getExists('profile_id'))
        {
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="center"><a class="s_button" href="' . UriRoute::get(PH7_ADMIN_MOD, 'user', 'browse') . '">' . t('Return to back users browse') . '</a></p>'));

            $oGroupId = (new AdminCoreModel)->getMemberships();
            $aGroupName = array();
            foreach ($oGroupId as $iId) $aGroupName[$iId->groupId] = $iId->name;
            $oForm->addElement(new \PFBC\Element\Select(t('Membership Group:'), 'group_id', $aGroupName, array('value'=>$oUser->groupId, 'required'=>1)));
            unset($aGroupName);
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('id'=>'str_first_name','onblur' =>'CValid(this.value,this.id,2,20)','value'=>$oUser->firstName,'required'=>1,'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_first_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('id'=>'str_last_name','onblur' =>'CValid(this.value,this.id,2,20)','value'=>$oUser->lastName,'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_last_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', array('description'=>t('For site security, you cannot change your username.'),'disabled'=>'disabled','value'=>$oUser->username)));
        $oForm->addElement(new \PFBC\Element\Email(t('Email:'), 'mail', array('description'=>t('For site security and to avoid spam, you cannot change your email address.'), 'disabled'=>'disabled','value'=>$oUser->email)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', array('female'=>t('Female'), 'male'=>t('Male'), 'couple'=>t('Couple')), array('value' => $oUser->sex,'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Interested in:'), 'match_sex', array('male'=>t('Male'), 'female'=>t('Female'), 'couple'=>t('Couple')),array('value'=>Form::getVal($oUser->matchSex), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Date(t('Date of birth:'), 'birth_date', array('id'=>'birth_date', 'onblur'=>'CValid(this.value, this.id)', 'value'=>$sBirthDate, 'validation' => new \PFBC\Validation\BirthDate, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));

        // Generate dynamic fields
        $oFields = $oUserModel->getInfoFields($iProfileId);
        foreach ($oFields as $sColumn => $sValue)
            $oForm = (new DynamicFieldCoreForm($oForm, $sColumn, $sValue))->generate();

        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script><script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

}
