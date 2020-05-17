<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
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

        $oForm = new \PFBC\Form('form_compose_mail');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_compose_mail', 'form_compose_mail'));
        $oForm->addElement(new Token('compose_mail'));
        $oForm->addElement(
            new Textbox(
                t('Recipient:'),
                'recipient',
                [
                    'id' => 'recipient',
                    'value' => $oHttpRequest->get('recipient'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Subject:'),
                'title',
                [
                    'id' => 'str_title',
                    'onblur' => 'CValid(this.value,this.id,2,60)',
                    'value' => self::getSubjectValue($oHttpRequest),
                    'validation' => new Str(2, 60),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_title"></span>'));
        $oForm->addElement(
            new Textarea(
                t('Message:'),
                'message',
                [
                    'id' => 'str_msg',
                    'onblur' => 'CValid(this.value,this.id,2,2500)',
                    'placeholder' => t('Say something nice to %0% 😊', $oHttpRequest->get('recipient')),
                    'value' => $oHttpRequest->get('message'),
                    'validation' => new Str(2, 2500),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_msg"></span>'));

        unset($oHttpRequest);

        if (!AdminCore::auth() && DbConfig::getSetting('isCaptchaMail')) {
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
        }

        $oForm->addElement(new Button(t('Send'), 'submit', ['icon' => 'mail-closed']));
        $oForm->addElement(
            new HTMLExternal(
                '<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'autocompleteUsername.js"></script>'
            )
        );
        $oForm->render();
    }

    private static function getSubjectValue(HttpRequest $oHttpRequest)
    {
        $sSubjectValue = '';
        if ($oHttpRequest->getExists('title')) {
            $sSubjectValue = t('RE: ') . str_replace('-', ' ', $oHttpRequest->get('title'));
        }

        return $sSubjectValue;
    }
}
