<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_subscription', 'form_subscription'));
        $oForm->addElement(new \PFBC\Element\Token('subscription'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Your full name:'), 'name', ['id' => 'str_name', 'onblur' => 'CValid(this.value, this.id,4,80)', 'validation' => new \PFBC\Validation\Str(4, 80), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_name"></span>'));

        $oForm->addElement(new \PFBC\Element\Email(t('Your email:'), 'email', ['id' => 'email', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1], false));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));

        $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));

        $oForm->addElement(new \PFBC\Element\Hidden('direction', ''));
        $oForm->addElement(new \PFBC\Element\Button(t('Subscribe'), 'submit', ['onclick' => '$("#form_subscription [name=direction]").val("subscrire");', 'icon' => 'check']));
        $oForm->addElement(new \PFBC\Element\Button(t('Unsubscribe'), 'submit', ['onclick' => '$("#form_subscription [name=direction]").val("unsubscribe");', 'icon' => 'closethick']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
