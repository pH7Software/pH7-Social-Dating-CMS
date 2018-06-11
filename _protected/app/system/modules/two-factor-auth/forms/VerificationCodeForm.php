<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

class VerificationCodeForm
{
    public static function display()
    {
        if (isset($_POST['submit_verification_code'])) {
            if (\PFBC\Form::isValid($_POST['submit_verification_code'])) {
                new VerificationCodeFormProcess((new Http)->get('mod'));
            }

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_verification_code');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_verification_code', 'form_verification_code'));
        $oForm->addElement(new \PFBC\Element\Token('verification_code'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Verification Code:'), 'verification_code', ['maxlength' => 6, 'autocomplete' => 'off', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
