<?php
/**
 * @title          Add a User Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\CKEditor;
use PFBC\Element\Country;
use PFBC\Element\Date;
use PFBC\Element\Email;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Password;
use PFBC\Element\Radio;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\BirthDate;
use PFBC\Validation\CEmail;
use PFBC\Validation\Name;
use PFBC\Validation\Str;
use PFBC\Validation\Username;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Url\Header;

class AddUserForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_user'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_user'])) {
                new AddUserFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_user');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_add_user', 'form_add_user'));
        $oForm->addElement(new Token('add_user'));
        $oForm->addElement(new \PFBC\Element\Username(t('Nickname:'), 'username', ['required' => 1, 'validation' => new Username]));
        $oForm->addElement(new Email(t('Login Email:'), 'mail', ['required' => 1, 'validation' => new CEmail(CEmail::GUEST_MODE)]));
        $oForm->addElement(new Password(t('Password:'), 'password', ['required' => 1]));
        $oForm->addElement(new Textbox(t('First Name:'), 'first_name', ['required' => 1, 'validation' => new Name]));
        $oForm->addElement(new Textbox(t('Last Name:'), 'last_name', ['required' => 1, 'validation' => new Name]));
        $oForm->addElement(
            new Radio(
                t('Gender:'),
                'sex',
                [
                    GenderTypeUserCore::FEMALE => t('Woman'),
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::COUPLE => t('Couple')
                ],
                ['value' => GenderTypeUserCore::FEMALE, 'required' => 1]
            )
        );
        $oForm->addElement(
            new Checkbox(
                t('Looking for:'),
                'match_sex',
                [
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::FEMALE => t('Woman'),
                    GenderTypeUserCore::COUPLE => t('Couple')
                ],
                ['value' => GenderTypeUserCore::MALE, 'required' => 1]
            )
        );
        $oForm->addElement(new Date(t('Date of birth:'), 'birth_date', ['title' => t('Please specify the date of birth using the calendar.'), 'validation' => new BirthDate, 'required' => 1]));
        $oForm->addElement(new Country(t('Country:'), 'country', ['id' => 'str_country', 'value' => Geo::getCountryCode(), 'required' => 1]));
        $oForm->addElement(new Textbox(t('City:'), 'city', ['id' => 'str_city', 'validation' => new Str(2, 150), 'required' => 1]));
        $oForm->addElement(new Textbox(t('State/Province:'), 'state', ['id' => 'str_state', 'validation' => new Str(2, 150)]));
        $oForm->addElement(new Textbox(t('Postal Code:'), 'zip_code', ['id' => 'str_zip_code', 'validation' => new Str(2, 15)]));
        $oForm->addElement(new Textbox(t('Punchline/Headline:'), 'punchline', ['validation' => new Str(5, 150)]));
        $oForm->addElement(new CKEditor(t('Description:'), 'description', ['validation' => new Str(10, 2000), 'required' => 1]));
        $oForm->addElement(new File(t('Profile Photo:'), 'avatar', ['accept' => 'image/*']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
