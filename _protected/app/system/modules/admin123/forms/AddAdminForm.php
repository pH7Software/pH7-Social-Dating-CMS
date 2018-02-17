<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Url\Header;

class AddAdminForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_admin'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_admin'])) {
                new AddAdminFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_admin');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_admin', 'form_add_admin'));
        $oForm->addElement(new \PFBC\Element\Token('add_admin'));
        $oForm->addElement(new \PFBC\Element\Username(t('Login Username:'), 'username', array('required' => 1, 'validation' => new \PFBC\Validation\Username(DbTableName::ADMIN))));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest', DbTableName::ADMIN))));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', array('male' => t('Man'), 'female' => t('Woman'), 'couple' => t('Couple')), array('value' => 'male', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Timezone('Time Zone:', 'time_zone', array('description' => t('With your time zone, the other administrators may know when they can contact you easily.'), 'value' => '-6', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
