<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class LoginForm implements Authenticable
{
    const CAPTCHA_NUMBER_ATTEMPTS = 3;

    public static function display()
    {
        static::clearCurrentSessions();

        if (isset($_POST['submit_login_user'])) {
            if (\PFBC\Form::isValid($_POST['submit_login_user'])) {
                new LoginFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_login_user');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_login_user', 'form_login_user'));
        $oForm->addElement(new \PFBC\Element\Token('login'));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', ['id' => 'email_login', 'onblur' => 'CValid(this.value, this.id,\'user\')', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email_login"></span>'));
        $oForm->addElement(new \PFBC\Element\Password(t('Your Password:'), 'password', ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Checkbox('', RememberMeCore::CHECKBOX_FIELD_NAME, [1 => t('Stay signed in')]));

        if (static::isCaptchaEligible()) {
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Button(t('Login'), 'submit', ['icon' => 'key']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }

    /**
     * {@inheritDoc}
     */
    public static function isCaptchaEligible()
    {
        return (new Session)->get('captcha_user_enabled') > self::CAPTCHA_NUMBER_ATTEMPTS;
    }

    /**
     * {@inheritDoc}
     */
    public static function clearCurrentSessions()
    {
        if (AffiliateCore::auth() || AdminCore::auth()) {
            (new Session)->destroy();
        }
    }
}
