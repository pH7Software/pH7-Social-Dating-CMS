<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Url\Header;

/** For "user", "affiliate" and "admin" module **/
class ChangePasswordCoreForm
{
    public static function display()
    {
        if (isset($_POST['submit_change_password'])) {
            if (\PFBC\Form::isValid($_POST['submit_change_password'])) {
                new ChangePasswordCoreFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_change_password');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_change_password', 'form_change_password'));
        $oForm->addElement(new \PFBC\Element\Token('change_password'));
        $oForm->addElement(new \PFBC\Element\Password(t('Old password:'), 'old_password', ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Password(t('New password:'), 'new_password', ['id' => 'password', 'onkeyup' => 'checkPassword(this.value)', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new \PFBC\Validation\Password]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error password"></span>'));
        $oForm->addElement(new \PFBC\Element\Password(t('Repeat new password:'), 'new_password2', ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Change Password!'), 'submit', ['icon' => 'key']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
