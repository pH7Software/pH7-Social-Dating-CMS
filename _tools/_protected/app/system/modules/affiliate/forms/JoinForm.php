<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Checkbox;
use PFBC\Element\Date;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Radio;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\BirthDate;
use PFBC\Validation\CEmail;
use PFBC\Validation\Name;
use PFBC\Validation\Password;
use PFBC\Validation\Str;
use PFBC\Validation\Username;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class JoinForm
{
    public static function step1()
    {
        if (isset($_POST['submit_join_aff'])) {
            if (\PFBC\Form::isValid($_POST['submit_join_aff'])) {
                (new JoinFormProcess)->step1();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_join_aff');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_join_aff', 'form_join_aff'));
        $oForm->addElement(new Token('join'));

        $oForm->addElement(new Textbox(t('Your First Name:'), 'first_name', ['id' => 'name_first', 'onblur' => 'CValid(this.value,this.id)', 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_first"></span>'));

        $oForm->addElement(new Textbox(t('Your Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_last"></span>'));

        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', ['id' => 'username', 'onkeyup' => 'CValid(this.value, this.id,\'Affiliates\')', 'description' => t('Your username will be your unique advertiser ID.'), 'required' => 1, 'validation' => new Username(DbTableName::AFFILIATE)]));
        $oForm->addElement(new HTMLExternal('<span class="input_error username"></span>'));

        $oForm->addElement(new Email(t('Your Email:'), 'mail', ['id' => 'email', 'onblur' => 'CValid(this.value, this.id,\'guest\',\'Affiliates\')', 'description' => t('Your Professional Valid Email.'), 'required' => 1, 'validation' => new CEmail(CEmail::GUEST_MODE, DbTableName::AFFILIATE)]));
        $oForm->addElement(new HTMLExternal('<span class="input_error email"></span>'));

        $oForm->addElement(new \PFBC\Element\Password(t('Your Password:'), 'password', ['id' => 'password', 'onkeyup' => 'checkPassword(this.value)', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new Password]));
        $oForm->addElement(new HTMLExternal('<span class="input_error password"></span>'));

        $oForm->addElement(
            new Radio(
                t('Your Gender:'),
                'sex',
                [
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::FEMALE => t('Woman')
                ],
                ['required' => 1]
            )
        );

        $oForm->addElement(new Date(t('Your Date of Birth:'), 'birth_date', ['id' => 'birth_date', 'description' => t('Please specify your date of birth using the calendar.'), 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new BirthDate]));
        $oForm->addElement(new HTMLExternal('<span class="input_error birth_date"></span>'));

        $oForm->addElement(new Select(t('Your Country:'), 'country', Form::getCountryValues(DbTableName::AFFILIATE_COUNTRY), ['id' => 'str_country', 'value' => Geo::getCountryCode(), 'description' => t('Select the country where you are legally resident.'), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Your City:'), 'city', ['id' => 'str_city', 'value' => Geo::getCity(), 'onblur' => 'CValid(this.value,this.id,2,150)', 'description' => t('Specify the city where you currently live.'), 'validation' => new Str(2, 150), 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_city"></span>'));

        $oForm->addElement(new Textbox(t('Your State/Province:'), 'state', ['id' => 'str_state', 'value' => Geo::getState(), 'onblur' => 'CValid(this.value,this.id,2,150)', 'validation' => new Str(2, 150), 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_state"></span>'));

        $oForm->addElement(new Textbox(t('Your Postal Code:'), 'zip_code', ['id' => 'str_zip_code', 'value' => Geo::getZipCode(), 'onblur' => 'CValid(this.value,this.id,2,15)', 'validation' => new Str(2, 15), 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_zip_code"></span>'));

        if (DbConfig::getSetting('isCaptchaAffiliateSignup')) {
            $oForm->addElement(new CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new Checkbox(t('Terms of Service'), 'terms', [1 => '<em>' . t('I have read and agree to the %0%.', '<a href="' . Uri::get('page', 'main', 'affiliateterms') . '" rel="nofollow" target="_blank">' . t('Terms of Service') . '</a>') . '</em>'], ['id' => 'terms', 'onblur' => 'CValid(this.checked, this.id)', 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error terms-0"></span>'));

        $oForm->addElement(new Button(t('Become an Affiliate!'), 'submit', ['icon' => 'cart']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }
}
