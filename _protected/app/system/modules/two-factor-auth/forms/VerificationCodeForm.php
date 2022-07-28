<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Form
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class VerificationCodeForm
{
    private const DIGIT_CODE_FIELD_LENGTH = 6;
    private const DIGIT_CODE_FIELD_PATTERN = '\d{6}';

    public static function display(): void
    {
        if (isset($_POST['submit_verification_code'])) {
            if (\PFBC\Form::isValid($_POST['submit_verification_code'])) {
                new VerificationCodeFormProcess((new Http)->get('mod'));
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_verification_code');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_verification_code', 'form_verification_code'));
        $oForm->addElement(new Token('verification_code'));
        $oForm->addElement(
            new Textbox(
                t('The 6 digit code provided by your authenticator app:'),
                'verification_code',
                [
                    'description' => '<i class="fa fa-mobile"></i> ' . t('Open your two-factor authentication app on your device to view the 6 digit code.'),
                    'pattern' => self::DIGIT_CODE_FIELD_PATTERN,
                    'maxlength' => self::DIGIT_CODE_FIELD_LENGTH,
                    'autocomplete' => 'off',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
