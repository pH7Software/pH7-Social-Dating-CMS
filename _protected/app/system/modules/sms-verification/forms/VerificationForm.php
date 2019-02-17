<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Form
 */

namespace PH7;

use PH7\Framework\Url\Header;

class VerificationForm
{
    public static function display()
    {
        if (isset($_POST['submit_sms_verification'])) {
            if (\PFBC\Form::isValid($_POST['submit_sms_verification'])) {
                new VerificationFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_sms_verification');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_sms_verification', 'form_sms_verification'));
        $oForm->addElement(new \PFBC\Element\Token('sms_verification'));
        $oForm->addElement(
            new \PFBC\Element\Textbox(
                t('Your Verification Code'),
                'verification_code',
                [
                    'autocomplete' => 'off',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
