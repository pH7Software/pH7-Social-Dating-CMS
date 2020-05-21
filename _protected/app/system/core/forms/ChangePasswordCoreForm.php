<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Password;
use PFBC\Element\Token;
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
        $oForm->addElement(
            new Hidden(
                'submit_change_password',
                'form_change_password'
            )
        );
        $oForm->addElement(new Token('change_password'));

        $oForm->addElement(
            new Password(
                t('Current password:'),
                'current_password',
                [
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Password(
                t('New password:'),
                'new_password',
                [
                    'id' => 'password',
                    'onkeyup' => 'checkPassword(this.value)',
                    'onblur' => 'CValid(this.value, this.id)',
                    'required' => 1,
                    'validation' => new \PFBC\Validation\Password
                ]
            )
        );
        $oForm->addElement(
            new HTMLExternal(
                '<span class="input_error password"></span>'
            )
        );
        $oForm->addElement(
            new Password(
                t('Repeat new password:'),
                'new_password2',
                [
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Change Password!'),
                'submit',
                [
                    'icon' => 'key'
                ]
            )
        );
        $oForm->addElement(
            new HTMLExternal(
                '<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'
            )
        );
        $oForm->render();
    }
}
