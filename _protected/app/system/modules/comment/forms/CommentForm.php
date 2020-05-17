<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Url\Header;

class CommentForm
{
    public static function display()
    {
        if (isset($_POST['submit_comment'])) {
            if (\PFBC\Form::isValid($_POST['submit_comment'])) {
                new CommentFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_comment');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_comment', 'form_comment'));
        $oForm->addElement(new Token('comment'));
        $oForm->addElement(
            new Textarea(
                t('Your comment:'),
                'comment',
                [
                    'id' => 'str_com',
                    'onblur' => 'CValid(this.value,this.id,2,2500)',
                    'placeholder' => t('Write something nice... 🤗'),
                    'required' => 1,
                    'validation' => new Str(2, 2500)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_com"></span>'));

        if (DbConfig::getSetting('isCaptchaComment')) {
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
