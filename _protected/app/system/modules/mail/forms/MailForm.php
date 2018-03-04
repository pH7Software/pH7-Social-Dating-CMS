<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Url\Header;

class MailForm
{
    public static function display()
    {
        if (isset($_POST['submit_compose_mail'])) {
            if (\PFBC\Form::isValid($_POST['submit_compose_mail'])) {
                new MailFormProcess;
            }

            Header::redirect();
        }

        $oHttpRequest = new HttpRequest; // For Reply Function

        $sSubjectValue = '';
        if ($oHttpRequest->getExists('title')) {
            $sSubjectValue = t('RE: ') . str_replace('-', ' ', $oHttpRequest->get('title'));
        }

        $oForm = new \PFBC\Form('form_compose_mail');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_compose_mail', 'form_compose_mail'));
        $oForm->addElement(new \PFBC\Element\Token('compose_mail'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Recipient:'), 'recipient', ['id' => 'recipient', 'value' => $oHttpRequest->get('recipient'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Subject:'), 'title', ['id' => 'str_title', 'onblur' => 'CValid(this.value,this.id,2,60)', 'value' => $sSubjectValue, 'validation' => new \PFBC\Validation\Str(2, 60), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_title"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your message:'), 'message', ['id' => 'str_msg', 'onblur' => 'CValid(this.value,this.id,2,2500)', 'value' => $oHttpRequest->get('message'), 'validation' => new \PFBC\Validation\Str(2, 2500), 'basic' => 1, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_msg"></span>'));

        unset($oHttpRequest);

        if (!AdminCore::auth() && DbConfig::getSetting('isCaptchaMail')) {
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Button(t('Send'), 'submit', ['icon' => 'mail-closed']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'autocompleteUsername.js"></script>'));
        $oForm->render();
    }
}
