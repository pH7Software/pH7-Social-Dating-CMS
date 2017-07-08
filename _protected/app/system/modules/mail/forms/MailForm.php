<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;

class MailForm
{
    public static function display()
    {
        if (isset($_POST['submit_compose_mail'])) {
            if (\PFBC\Form::isValid($_POST['submit_compose_mail'])) {
                new MailFormProcess;
            }

            Framework\Url\Header::redirect();
        }

        $oHttpRequest = new Http; // For Reply Function

        $oForm = new \PFBC\Form('form_compose_mail');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_compose_mail', 'form_compose_mail'));
        $oForm->addElement(new \PFBC\Element\Token('compose_mail'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Recipient:'), 'recipient', array('id' => 'recipient', 'value' => $oHttpRequest->get('recipient'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Subject:'), 'title', array('id' => 'str_title', 'onblur' => 'CValid(this.value,this.id,2,60)', 'value' => ($oHttpRequest->get('title') != '') ? t('RE: ') . str_replace('-', ' ', $oHttpRequest->get('title')) : '', 'validation' => new \PFBC\Validation\Str(2, 60), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_title"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your message:'), 'message', array('id' => 'str_msg', 'onblur' => 'CValid(this.value,this.id,2,2500)', 'value' => $oHttpRequest->get('message'), 'validation' => new \PFBC\Validation\Str(2, 2500), 'basic' => 1, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_msg"></span>'));

        unset($oHttpRequest);

        if (!AdminCore::auth() && DbConfig::getSetting('isCaptchaMail')) {
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', array('id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:'))));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'autocompleteUsername.js"></script>'));
        $oForm->render();
    }
}
