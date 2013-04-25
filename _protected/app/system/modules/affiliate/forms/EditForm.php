<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
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
        if (isset($_POST['submit_aff_edit_account']))
        {
            if (\PFBC\Form::isValid($_POST['submit_aff_edit_account']))
                new EditFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $oHR = new HttpRequest;
        $iProfileId = (AdminCore::auth() && !Affiliate::auth() && $oHR->getExists('profile_id')) ? $oHR->get('profile_id', 'int') : (new Session)->get('affiliate_id');

        $oAff = (new AffiliateModel)->readProfile($iProfileId, 'Affiliate');


        // Birth date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oAff->birthDate)->date('m/d/Y');

        $oForm = new \PFBC\Form('form_aff_edit_account', 500);
        $oForm->configure(array('action'=> '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_aff_edit_account', 'form_aff_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if (AdminCore::auth() && !Affiliate::auth() && $oHR->getExists('profile_id'))
        {
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="center"><a class="s_button" href="' . UriRoute::get('affiliate', 'admin', 'userlist') . '">' . t('Return to back affiliates browse') . '</a></p>'));
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h2 class="underline">'.t('Global Information:').'</h2>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="error">' . t('Attention all your information must be complete, candid and valid.') . '</p>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', array('id'=>'str_first_name', 'onblur'=>'CValid(this.value,this.id,2,20)', 'value'=>$oAff->firstName, 'required'=>1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_first_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Last Name:'), 'last_name', array('id'=>'str_last_name', 'onblur'=>'CValid(this.value,this.id,2,20)', 'value'=>$oAff->lastName, 'required'=>1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_last_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', array('description'=>t('For site security, you cannot change your username.'), 'disabled'=>'disabled', 'value'=>$oAff->username)));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', array('description'=>t('For site security and to avoid spam, you cannot change your email address.'), 'disabled'=>'disabled', 'value'=>$oAff->email)));
        $oForm->addElement(new \PFBC\Element\Phone(t('Your Phone Number:'), 'phone', array('id'=>'phone', 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('Enter full phone number with area code.'), 'value'=>$oAff->phone, 'required'=>1,'validation'=>new \PFBC\Validation\Phone)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error phone"></span>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Your Sex:'), 'sex', array('male'=>t('Male'), 'female'=>t('Female')), array('value'=> $oAff->sex,'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Date(t('Your Date of birth:'), 'birth_date', array('id'=>'birth_date', 'onblur'=>'CValid(this.value, this.id)', 'value'=>$sBirthDate, 'validation'=> new \PFBC\Validation\BirthDate, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));
        $oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), 'country', array('id'=>'str_country', 'value'=>$oAff->country, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Address:'), 'address', array('id'=>'str_address', 'onblur'=>'CValid(this.value,this.id,4,255)', 'value'=>$oAff->address, 'validation'=>new \PFBC\Validation\Str(4,255), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_address"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), 'city', array('id'=>'str_city', 'onblur'=>'CValid(this.value,this.id,2,150)', 'value'=>$oAff->city, 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_city"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your State or Province:'), 'state', array('id'=>'str_state', 'onblur'=>'CValid(this.value,this.id,2,150)', 'value'=>$oAff->state, 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_state"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Postal code (zip):'), 'zip_code', array('id'=>'str_zip_code', 'onblur'=>'CValid(this.value,this.id,2,10)', 'value'=>$oAff->zipCode, 'validation'=>new \PFBC\Validation\Str(2,10), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_zip_code"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), 'description', array('id'=>'str_description', 'onblur'=>'CValid(this.value,this.id,10,1000)', 'description'=>t('Description of your site(s).'), 'title'=>t('Tell us about your site(s).'), 'value'=>$oAff->description, 'validation'=>new \PFBC\Validation\Str(20,2000), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Your Website:'), 'website', array('id'=>'url', 'onblur'=>'CValid(this.value,this.id)', 'description'=>t('Your main website where you are the owner.'), 'title'=>t('You can advertise on other sites that you specify, but the site that you specify in this field must be your site.'), 'value'=>$oAff->website, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HtmlExternal('<span class="input_error url"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script>'));
        $oForm->render();
    }

}
