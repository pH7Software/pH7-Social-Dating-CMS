<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Invite / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Name;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class InviteForm
{
    public static function display()
    {
        if (isset($_POST['submit_invite'])) {
            if (\PFBC\Form::isValid($_POST['submit_invite'])) {
                new InviteFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_invite');
        $oForm->configure(['action' => Uri::get('invite', 'home', 'invitation')]);
        $oForm->addElement(new Hidden('submit_invite', 'form_invite'));
        $oForm->addElement(new Token('invite'));
        $oForm->addElement(new Textbox(t('Your name:'), 'first_name', ['id' => 'name_first', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_first"></span>'));
        $oForm->addElement(new Textarea(t('To:'), 'to', ['description' => t('Upto 10 email addresses separated by commas.'), 'required' => 1]));
        $oForm->addElement(new Textarea(t('Message:'), 'message', ['id' => 'str_msg', 'onblur' => 'CValid(this.value,this.id,4)', 'required' => 1, 'validation' => new Str('4')]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_msg"></span>'));
        $oForm->addElement(new CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
        $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
        $oForm->addElement(new Button(t('Invite your Friends!'), 'submit', ['icon' => 'mail-closed']));
        $oForm->addElement(new Button(t('Cancel'), 'cancel', ['onclick' => 'parent.$.colorbox.close();return false', 'icon' => 'cancel']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'str.js"></script>'));
        $oForm->render();
    }
}
