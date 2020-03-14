<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SubscriptionForm
{
    public static function display()
    {
        $sActUrl = Uri::get('newsletter', 'home', 'subscription');

        if (isset($_POST['submit_subscription'])) {
            if (\PFBC\Form::isValid($_POST['submit_subscription'])) {
                new SubscriptionFormProcess();
            }

            Header::redirect($sActUrl);
        }

        $oForm = new \PFBC\Form('form_subscription');
        $oForm->configure(['action' => $sActUrl]);
        $oForm->addElement(new Hidden('submit_subscription', 'form_subscription'));
        $oForm->addElement(new Token('subscription'));

        $oForm->addElement(new Textbox(t('Your full name:'), 'name', ['id' => 'str_name', 'onblur' => 'CValid(this.value, this.id,4,80)', 'validation' => new Str(4, 80), 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_name"></span>'));

        $oForm->addElement(new Email(t('Your email:'), 'email', ['id' => 'email', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1], false));
        $oForm->addElement(new HTMLExternal('<span class="input_error email"></span>'));

        $oForm->addElement(new CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
        $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));

        $oForm->addElement(new Hidden('direction', ''));
        $oForm->addElement(new Button(t('Subscribe'), 'submit', ['onclick' => '$("#form_subscription [name=direction]").val("subscribe");', 'icon' => 'check']));
        $oForm->addElement(new Button(t('Unsubscribe'), 'submit', ['onclick' => '$("#form_subscription [name=direction]").val("unsubscribe");', 'icon' => 'closethick']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
