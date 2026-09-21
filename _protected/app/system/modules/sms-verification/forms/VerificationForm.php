<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;

class VerificationForm
{
    public static function display(): void
    {
        if (isset($_POST['submit_sms_verification'])) {
            if (\PFBC\Form::isValid('form_sms_verification')) {
                new VerificationFormProcess();
            }

            Header::redirect();
        }

        // Form, ID, form action and form hidden field
        $oForm = new \PFBC\Form('form_sms_verification');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_sms_verification', 'form_sms_verification'));

        // CSRF token
        $oForm->addElement(new Token('sms_verification'));

        // Verification code field
        $iFieldLength = Verification::getCodeLength();
        $sFieldPattern = sprintf('\d{%d}', $iFieldLength);
        $oForm->addElement(
            new Textbox(
                t('Your Verification Code'),
                'verification_code',
                [
                    'pattern' => $sFieldPattern,
                    'maxlength' => $iFieldLength,
                    'autocomplete' => 'one-time-code',
                    'inputmode' => 'numeric',
                    'description' => t('Codes expire after five minutes. Maximum five attempts per code.'),
                    'required' => 1
                ]
            )
        );

        // Submit button
        $oForm->addElement(new Button(t('Submit'), 'submit', ['icon' => 'check']));

        // Render the form
        $oForm->render();
    }
}
