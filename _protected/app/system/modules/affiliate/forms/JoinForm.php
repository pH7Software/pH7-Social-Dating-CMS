<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */
namespace PH7;

use
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class JoinForm
{

    public static function step1()
    {
        if (isset($_POST['submit_join_aff']))
        {
            if (\PFBC\Form::isValid($_POST['submit_join_aff']))
                (new JoinFormProcess)->step1();

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_join_aff', 400);
        $oForm->configure(array('action'=> ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_join_aff', 'form_join_aff'));
        $oForm->addElement(new \PFBC\Element\Token('join'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', array('id'=>'str_first_name', 'onblur'=>'CValid(this.value,this.id,2,20)', 'title'=>t('Enter your first name.'), 'required'=> 1,'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_first_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Last Name:'), 'last_name', array('id'=>'str_last_name', 'onblur'=>'CValid(this.value, this.id,2,20)', 'title'=>t('Enter your last name.'), 'required'=> 1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_last_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('id'=>'username', 'onkeyup'=>'CValid(this.value, this.id,\'Affiliates\')', 'title'=>t('Your username will be your unique ID reference for advertisements.'), 'required'=>1, 'validation'=>new \PFBC\Validation\Username('Affiliates'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error username"></span>'));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', array('id'=>'email', 'onblur'=>'CValid(this.value, this.id,\'guest\',\'Affiliates\')', 'title'=>t('Enter your valid email address.'), 'required'=>1, 'validation'=> new \PFBC\Validation\CEmail('guest', 'Affiliates'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new \PFBC\Element\Password(t('Your Password:'), 'password', array('id'=>'password', 'onkeyup'=>'checkPassword(this.value)', 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('Your password. It will be used for logging in to the site. This storage is secure, because we are using an encrypted format.'), 'required'=> 1, 'validation'=> new \PFBC\Validation\Password)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error password"></span>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Your Sex:'), 'sex', array('male'=>t('Male'), 'female'=>t('Female')), array('value'=>'male', 'title'=>t('Please specify your gender.'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Date(t('Your Date of birth:'), 'birth_date', array('placeholder'=>t('Month/Day/Year'), 'id'=>'birth_date', 'title'=>t('Please specify your birth date using the calendar or with this format: Month/Day/Year.'), 'onblur'=>'CValid(this.value, this.id)', 'required'=>1, 'validation'=>new \PFBC\Validation\BirthDate)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));
        $oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), 'country', array('id'=>'str_country', 'value'=>Geo::getCountryCode(), 'title'=>t('Select the country where you live.'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), 'city', array('id'=>'str_city', 'value'=>Geo::getCity(), 'onblur'=>'CValid(this.value,this.id,2,150)', 'title'=>t('Specify the city where you live.'), 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_city"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your State:'), 'state', array('id'=>'str_state', 'value'=>Geo::getState(), 'onblur'=>'CValid(this.value,this.id,2,150)', 'title'=>t('Specify your state.'), 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_state"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your ZIP/Postal Code:'), 'zip_code', array('id'=>'str_zip_code', 'value'=>Geo::getZipCode(), 'onblur'=>'CValid(this.value,this.id,2,15)', 'title'=>t('Enter your post code (Zip).'), 'validation'=>new \PFBC\Validation\Str(2,15), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_zip_code"></span>'));

        if (DbConfig::getSetting('isCaptchaAffiliateSignup'))
        {
          $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha:'), 'captcha', array('id'=>'ccaptcha', 'onkeyup'=>'CValid(this.value, this.id)', 'description'=>t('Enter the code above:'))));
          $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Checkbox(t('Terms of Service'), 'terms', array(1=>'<em>' . t('I have read and accept to the %0%.', '<a href="' . Uri::get('page','main','affiliateterms') . '" rel="nofollow" target="_blank">' . t('Terms of Service') . '</a>') . '</em>'), array('id'=>'terms', 'onblur'=>'CValid(this.checked, this.id)', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error terms-0"></span>'));
        $oForm->addElement(new \PFBC\Element\Button(t('I become an affiliate!'),'submit', array('icon'=>'cart')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script><script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

}
