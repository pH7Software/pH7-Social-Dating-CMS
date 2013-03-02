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
        if(isset($_POST['submit_user_edit_account'])) {
            if(\PFBC\Form::isValid($_POST['submit_user_edit_account']))
                new EditFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $oHR = new HttpRequest;
        $iProfileId = (AdminCore::auth() && !User::auth() && $oHR->getExists('profile_id')) ? $oHR->get('profile_id', 'int') : (new Session)->get('member_id');

        $oUser = (new UserModel)->readProfile($iProfileId);


        // Birth Date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oUser->birthDate)->date('m/d/Y');

        $oForm = new \PFBC\Form('form_user_edit_account', 650);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_user_edit_account', 'form_user_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if(AdminCore::auth() && !User::auth() && $oHR->getExists('profile_id'))
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
        $oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), 'country', array('id'=>'str_country', 'value'=>$oUser->country, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), 'city', array('id'=>'str_city', 'onblur' =>'CValid(this.value,this.id,2,150)','value'=>$oUser->city, 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_city"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your State or Province:'), 'state', array('id'=>'str_state', 'onblur' =>'CValid(this.value,this.id,2,150)','value'=>$oUser->state, 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_state"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal code (zip):'), 'zip_code', array('id'=>'str_zip_code', 'onblur' =>'CValid(this.value,this.id,2,10)','value'=>$oUser->zipCode, 'validation'=>new \PFBC\Validation\Str(2,10), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_zip_code"></span>'));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Description:'), 'description', array('id'=>'str_description', 'onblur' =>'CValid(this.value,this.id,10,2000)','value'=>$oUser->description, 'validation'=>new \PFBC\Validation\Str(10,2000), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Your Website:'), 'website', array('id'=>'url','onblur'=>'CValid(this.value,this.id)','value'=>$oUser->website)));
        $oForm->addElement(new \PFBC\Element\HtmlExternal('<span class="input_error url"></span>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Social Network Site:'), 'social_network_site', array('id'=>'url2','onblur'=>'CValid(this.value,this.id)','description'=>t('The url of your profile Facebook, Twitter, Google+, etc.'),'value'=>$oUser->socialNetworkSite)));
        $oForm->addElement(new \PFBC\Element\HtmlExternal('<span class="input_error url2"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script><script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

}
