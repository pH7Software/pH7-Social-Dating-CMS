<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

use
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Config\Config,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class JoinForm
{

   public static function step1($iWidth = 300)
   {
        if ((new Session)->exists('mail_step1'))
         Header::redirect(Uri::get('user', 'signup', 'step2'));

        if (isset($_POST['submit_join_user']))
        {
            if (\PFBC\Form::isValid($_POST['submit_join_user']))
                (new JoinFormProcess)->step1();

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_join_user', $iWidth);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_join_user', 'form_join_user'));
        $oForm->addElement(new \PFBC\Element\Token('join'));

        // Load the Connect config file
        Config::getInstance()->load(PH7_PATH_SYS_MOD . 'connect' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
        if (Config::getInstance()->values['module.setting']['enable'])
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="center"><a href="' . Uri::get('connect', 'main', 'index') . '" target="_blank" class="m_button">' . t('Universal Login') . '</a></div>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', array('id'=>'str_first_name', 'onblur' =>'CValid(this.value,this.id,2,20)', 'title'=>t('Enter your first name.'), 'required' => 1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_first_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('description'=>PH7_URL_ROOT.'<strong><span class="your-user-name">'.t('your-user-name').'</span><span class="username"></span></strong>'.PH7_PAGE_EXT, 'id'=>'username', 'title'=>t('This username will be used for your site url.'), 'required' => 1, 'validation'=>new \PFBC\Validation\Username)));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', array('id'=>'email', 'onblur' =>'CValid(this.value, this.id,\'guest\')', 'title'=>t('Enter your valid email address.'), 'required'=> 1, 'validation' => new \PFBC\Validation\CEmail('guest'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new \PFBC\Element\Password(t('Your Password:'), 'password', array('id'=>'password', 'onkeyup'=>'checkPassword(this.value)', 'onblur' =>'CValid(this.value, this.id)', 'title'=>t('Your password. It will be used for logging in to the site. This storage is secure, because we are using an encrypted format.'), 'required' => 1, 'validation' => new \PFBC\Validation\Password)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error password"></span>'));

        if (DbConfig::getSetting('isCaptchaUserSignup'))
        {
          $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha:'), 'captcha', array('id'=>'ccaptcha', 'onkeyup'=>'CValid(this.value, this.id)', 'description'=>t('Enter the code above:'))));
          $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Checkbox(t('Terms of Service'), 'terms', array(1=>'<em>' . t('I have read and accept to the %0%.', '<a href="' . Uri::get('page', 'main', 'terms') . '" rel="nofollow" target="_blank">' . t('Terms of Service') . '</a>') . '</em>'), array('id'=>'terms', 'onblur'=>'CValid(this.checked, this.id)', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error terms-0"></span>'));
        $oForm->addElement(new \PFBC\Element\Button(t('I sign up for free!'), 'submit', array('icon'=>'heart')));
        // JavaScript Files
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'signup.js"></script><script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script>'));
        $oForm->render();
    }

    public static function step2()
    {
        $oSession = new Session;
        if (!$oSession->exists('mail_step1'))
            Framework\Url\Header::redirect(Uri::get('user', 'signup', 'step1'));
        elseif ($oSession->exists('mail_step2'))
            Header::redirect(Uri::get('user', 'signup', 'step3'));
        unset($oSession);

        if (isset($_POST['submit_join_user2']))
        {
            if (\PFBC\Form::isValid($_POST['submit_join_user2']))
                (new JoinFormProcess)->step2();

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_join_user2', 650);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_join_user2', 'form_join_user2'));
        $oForm->addElement(new \PFBC\Element\Token('join2'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', array('female'=>t('Female') . ' &#9792;', 'male'=>t('Male') . ' &#9794;', 'couple'=>t('Couple')), array('value'=>'female', 'title'=>t('Please specify your gender.'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Interested in:'), 'match_sex', array('male'=>t('Male') . ' &#9794;', 'female'=>t('Female') . ' &#9792;', 'couple'=>t('Couple')), array('value'=>'male', 'title'=>t('Please specify whom you are looking for'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Date(t('Your Date of birth:'), 'birth_date', array('placeholder'=>t('Month/Day/Year'), 'id'=>'birth_date', 'title'=>t('Please specify your birth date using the calendar or with this format: Month/Day/Year.'), 'onblur'=>'CValid(this.value, this.id)', 'validation'=> new \PFBC\Validation\BirthDate, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));
        $oForm->addElement(new \PFBC\Element\Country(t('Your Country:'), 'country', array('id'=>'str_country', 'value'=> Geo::getCountryCode(), 'title'=>t('Select the country where you live.'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your City:'), 'city', array('id'=>'str_city', 'value'=> Geo::getCity(), 'onblur' =>'CValid(this.value,this.id,2,150)', 'title'=>t('Specify the city where you live.'), 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_city"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your State or Province:'), 'state', array('id'=>'str_state', 'value'=> Geo::getState(), 'onblur' =>'CValid(this.value,this.id,2,150)', 'title'=>t('Specify your state.'), 'validation'=>new \PFBC\Validation\Str(2,150), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_state"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your ZIP/Postal Code:'), 'zip_code', array('id'=>'str_zip_code', 'value'=> Geo::getZipCode(), 'onblur' =>'CValid(this.value,this.id,2,15)', 'title'=>t('Enter your post code (Zip).'), 'validation'=>new \PFBC\Validation\Str(2,15), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_zip_code"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script><script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    public static function step3()
    {
        if (!(new Session)->exists('mail_step2'))
            Header::redirect(Uri::get('user', 'signup', 'step2'));

        if (isset($_POST['submit_join_user3']))
        {
            if (\PFBC\Form::isValid($_POST['submit_join_user3']))
                (new JoinFormProcess)->step3();

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_join_user3', 650);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_join_user3', 'form_join_user3'));
        $oForm->addElement(new \PFBC\Element\Token('join3'));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Description:'), 'description', array('id'=>'str_description', 'title'=>t('Describe yourself in a few words. Your description should be at least 20 characters long.'), 'onblur' =>'CValid(this.value,this.id,10,2000)', 'validation'=>new \PFBC\Validation\Str(20,4000), 'required' =>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script>'));
        $oForm->render();
    }

}
