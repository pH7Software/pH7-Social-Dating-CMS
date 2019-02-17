<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Form
 */

namespace PH7;

use PH7\Framework\Url\Header;

class PhoneNumberForm
{
    public static function display()
    {
        if (isset($_POST['submit_phone_number_verification'])) {
            if (\PFBC\Form::isValid($_POST['submit_phone_number_verification'])) {
                new PhoneNumberFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_phone_number_verification');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_phone_number_verification', 'form_phone_number_verification'));
        $oForm->addElement(new \PFBC\Element\Token('phone_number_verification'));
        $oForm->addElement(
            new \PFBC\Element\Phone(
                t('Your Phone Number'),
                'phone_number',
                [
                    'description' => t('In order to verify your account, please enter your phone number and we will text you with a verification code.'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
