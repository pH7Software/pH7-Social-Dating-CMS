<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_comment', 'form_comment'));
        $oForm->addElement(new \PFBC\Element\Token('comment'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your comment:'), 'comment', ['id' => 'str_com', 'onblur' => 'CValid(this.value,this.id,2,2500)', 'required' => 1, 'validation' => new \PFBC\Validation\Str(2, 2500)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_com"></span>'));

        if (DbConfig::getSetting('isCaptchaComment')) {
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
