<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

namespace PH7;

use PH7\Framework\Geo\Ip\Geo;

class AddAffiliateForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_aff'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_aff'])) {
                new AddAffiliateFormProcess;
            }
            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_aff');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_aff', 'form_add_aff'));
        $oForm->addElement(new \PFBC\Element\Token('add_aff'));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('required' => 1, 'validation' => new \PFBC\Validation\Username('Affiliates'))));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest', 'Affiliates'))));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Middle Name:'), 'middle_name', array('validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', array('male' => t('Man'), 'female' => t('Woman')), array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Date(t('Date of birth:'), 'birth_date', array('placeholder' => t('Month/Day/Year'), 'title' => t('Please specify the birth date using the calendar or with this format: Month/Day/Year.'), 'required' => 1, 'validation' => new \PFBC\Validation\BirthDate)));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', array('id' => 'str_country', 'value' => Geo::getCountryCode(), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', array('id' => 'str_city', 'validation' => new \PFBC\Validation\Str(2, 150), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State/Province:'), 'state', array('id' => 'str_state', 'validation' => new \PFBC\Validation\Str(2, 150), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), 'zip_code', array('id' => 'str_zip_code', 'validation' => new \PFBC\Validation\Str(2, 15), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Phone(t('Phone Number:'), 'phone', array('description' => t('Enter full phone number with area code (e.g., +44768374890).'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Description:'), 'description', array('description' => t("Description of the affiliate's site(s)."), 'validation' => new \PFBC\Validation\Str(10, 2000), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Url(t('Website:'), 'website', array('description' => t('Main website where the affiliate is the owner (e.g. http://ph7cms.com)'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Bank Account:'), 'bank_account', array('description' => t('Bank Account (PayPal Email Address).'), 'validation' => new \PFBC\Validation\BankAccount, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
