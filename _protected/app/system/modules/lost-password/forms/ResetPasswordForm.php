<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Password;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;

class ResetPasswordForm
{
    public static function display(string $sMod, string $sEmail, string $sToken): void
    {
        if (isset($_POST['submit_reset_password'])) {
            if (\PFBC\Form::isValid('form_reset_password')) {
                new ResetPasswordFormProcess($sMod, $sEmail, $sToken);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_reset_password');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_reset_password', 'form_reset_password'));
        $oForm->addElement(new Token('reset_password'));
        $oForm->addElement(new Password(t('New password:'), 'new_password', [
            'required' => 1,
            'autocomplete' => 'new-password',
            'validation' => new \PFBC\Validation\Password()
        ]));
        $oForm->addElement(new Password(t('Repeat new password:'), 'new_password2', [
            'required' => 1,
            'autocomplete' => 'new-password'
        ]));
        $oForm->addElement(new Button(t('Save new password'), 'submit', ['icon' => 'key']));
        $oForm->render();
    }
}
