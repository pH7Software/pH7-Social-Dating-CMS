<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CKEditor;
use PFBC\Element\Country;
use PFBC\Element\Date;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Password;
use PFBC\Element\Phone;
use PFBC\Element\Radio;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Element\Url;
use PFBC\Validation\BankAccount;
use PFBC\Validation\BirthDate;
use PFBC\Validation\CEmail;
use PFBC\Validation\Name;
use PFBC\Validation\Str;
use PFBC\Validation\Username;
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
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_add_aff', 'form_add_aff'));
        $oForm->addElement(new Token('add_aff'));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', ['required' => 1, 'validation' => new Username(DbTableName::AFFILIATE)]));
        $oForm->addElement(new Email(t('Login Email:'), 'mail', ['required' => 1, 'validation' => new CEmail(CEmail::GUEST_MODE, DbTableName::AFFILIATE)]));
        $oForm->addElement(new Password(t('Password:'), 'password', ['required' => 1]));
        $oForm->addElement(new Textbox(t('First Name:'), 'first_name', ['required' => 1, 'validation' => new Name]));
        $oForm->addElement(new Textbox(t('Last Name:'), 'last_name', ['required' => 1, 'validation' => new Name]));
        $oForm->addElement(new Textbox(t('Middle Name:'), 'middle_name', ['validation' => new Name]));
        $oForm->addElement(
            new Radio(
                t('Gender:'),
                'sex',
                [
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::FEMALE => t('Woman')
                ],
                ['value' => GenderTypeUserCore::MALE, 'required' => 1]
            )
        );
        $oForm->addElement(new Date(t('Date of birth:'), 'birth_date', ['title' => t('Please specify the date of birth using the calendar.'), 'required' => 1, 'validation' => new BirthDate]));
        $oForm->addElement(new Country(t('Country:'), 'country', ['id' => 'str_country', 'value' => Geo::getCountryCode(), 'required' => 1]));
        $oForm->addElement(new Textbox(t('City:'), 'city', ['id' => 'str_city', 'validation' => new Str(2, 150), 'required' => 1]));
        $oForm->addElement(new Textbox(t('State/Province:'), 'state', ['id' => 'str_state', 'validation' => new Str(2, 150), 'required' => 1]));
        $oForm->addElement(new Textbox(t('Postal Code:'), 'zip_code', ['id' => 'str_zip_code', 'validation' => new Str(2, 15), 'required' => 1]));
        $oForm->addElement(new Phone(t('Phone Number:'), 'phone', ['description' => t('Enter full phone number with area code (e.g., +44768374890).'), 'required' => 1]));
        $oForm->addElement(new CKEditor(t('Description:'), 'description', ['description' => t("Description of the affiliate's site(s)."), 'validation' => new Str(10, 2000), 'required' => 1]));
        $oForm->addElement(new Url(t('Website:'), 'website', ['description' => t('Main website where the affiliate is the owner (e.g. http://ph7cms.com)'), 'required' => 1]));
        $oForm->addElement(new Email(t('Bank Account:'), 'bank_account', ['description' => t('Bank Account (PayPal Email Address).'), 'validation' => new BankAccount]));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
