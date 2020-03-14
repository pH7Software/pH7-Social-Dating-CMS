<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Url\Header;

class ReplyMsgForm
{
    public static function display()
    {
        if (isset($_POST['submit_reply'])) {
            if (\PFBC\Form::isValid($_POST['submit_reply'])) {
                new ReplyMsgFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_reply');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_reply', 'form_reply'));
        $oForm->addElement(new Token('reply'));

        $sEditorClass = FormHelper::getEditorPfbcClassName();
        $oForm->addElement(
            new $sEditorClass(
                t('Message:'),
                'message',
                [
                    'required' => 1,
                    'validation' => new Str(4)
                ]
            )
        );

        if (DbConfig::getSetting('isCaptchaForum')) {
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

        $oForm->addElement(new Button);
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
