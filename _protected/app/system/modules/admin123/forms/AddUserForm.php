<?php
/**
 * @title          Add a User Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_user', 'form_add_user'));
        $oForm->addElement(new \PFBC\Element\Token('add_user'));
        $oForm->addElement(new \PFBC\Element\Username(t('Nickname:'), 'username', ['required' => 1, 'validation' => new \PFBC\Validation\Username]));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', ['required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest')]));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', ['required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', ['required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Middle Name:'), 'middle_name', ['validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', ['female' => t('Woman'), 'male' => t('Man'), 'couple' => t('Couple')], ['value' => 'female', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'match_sex', ['male' => t('Man'), 'female' => t('Woman'), 'couple' => t('Couple')], ['value' => 'male', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Date(t('Date of birth:'), 'birth_date', ['title' => t('Please specify the date of birth using the calendar.'), 'validation' => new \PFBC\Validation\BirthDate, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', ['id' => 'str_country', 'value' => Geo::getCountryCode(), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', ['id' => 'str_city', 'validation' => new \PFBC\Validation\Str(2, 150), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State/Province:'), 'state', ['id' => 'str_state', 'validation' => new \PFBC\Validation\Str(2, 150)]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), 'zip_code', ['id' => 'str_zip_code', 'validation' => new \PFBC\Validation\Str(2, 15)]));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Description:'), 'description', ['validation' => new \PFBC\Validation\Str(10, 2000), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\File(t('Profile Photo:'), 'avatar', ['accept' => 'image/*']));
        $oForm->addElement(new \PFBC\Element\Url(t('Your Website:'), 'website'));
        $oForm->addElement(new \PFBC\Element\Url(t('Social Network Site:'), 'social_network_site', ['description' => t('The url of your profile Facebook, Twitter, Google+, etc.')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
