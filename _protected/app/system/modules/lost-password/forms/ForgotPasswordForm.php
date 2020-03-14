<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class ForgotPasswordForm
{
    public static function display()
    {
        $sTable = Various::convertModToTable((new Http)->get('mod'));

        if (isset($_POST['submit_forgot_password'])) {
            if (\PFBC\Form::isValid($_POST['submit_forgot_password'])) {
                new ForgotPasswordFormProcess($sTable);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_forgot_password');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_forgot_password', 'form_forgot_password'));
        $oForm->addElement(new Token('forgot_password'));
        $oForm->addElement(
            new Email(
                t('Your Email:'),
                'mail',
                [
                    'id' => 'email',
                    'onblur' => 'CValid(this.value, this.id,\'user\',\'' . $sTable . '\')',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(
            new CCaptcha(
                t('Captcha'),
                'captcha',
                [
                    'id' => 'ccaptcha',
                    'onkeyup' => 'CValid(this.value, this.id)',
                    'description' => t('Enter the below code:')
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
        $oForm->addElement(new Button(t('Get a new password'), 'submit', ['icon' => 'key']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
