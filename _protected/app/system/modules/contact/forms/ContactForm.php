<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Phone;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Element\Url;
use PFBC\Validation\Name;
use PFBC\Validation\Str;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class ContactForm
{
    public static function display()
    {
        // Display the contact form on the template
        if (isset($_POST['submit_contact'])) {
            if (\PFBC\Form::isValid($_POST['submit_contact'])) {
                new ContactFormProcess();
            }

            Header::redirect();
        }

        $oSession = new Session;

        $oForm = new \PFBC\Form('form_contact');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_contact', 'form_contact'));
        $oForm->addElement(new Token('contact'));
        $oForm->addElement(new Textbox(t('Your First Name:'), 'first_name', ['value' => $oSession->get('member_first_name'), 'id' => 'name_first', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_first"></span>'));
        $oForm->addElement(new Textbox(t('Your Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_last"></span>'));
        $oForm->addElement(new Email(t('Your Email:'), 'mail', ['value' => $oSession->get('member_email'), 'id' => 'email', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1]));
        $oForm->addElement(new HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new Phone(t('Your Phone Number:'), 'phone', ['id' => 'phone', 'onblur' => 'CValid(this.value, this.id)', 'description' => t('Enter the full phone number including its country calling codes (e.g., +44768374890).')]));
        $oForm->addElement(new HTMLExternal('<span class="input_error phone"></span>'));
        $oForm->addElement(new Url(t('Your Website:'), 'website', ['id' => 'url', 'onblur' => 'CValid(this.value, this.id)', 'description' => t("If you have a website (e.g., your company's website).")]));
        $oForm->addElement(new HTMLExternal('<span class="input_error url"></span>'));
        $oForm->addElement(new Textbox(t('Your Subject:'), 'subject', ['id' => 'str_subject', 'onblur' => 'CValid(this.value, this.id,4,45)', 'required' => 1, 'validation' => new Str(4, 45)]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_subject"></span>'));
        $oForm->addElement(new Textarea(t('Your Message:'), 'message', ['id' => 'str_message', 'onblur' => 'CValid(this.value, this.id,10,2000)', 'required' => 1, 'validation' => new Str(10, 2000)]));
        $oForm->addElement(new HTMLExternal('<span class="input_error str_message"></span>'));
        $oForm->addElement(new CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
        $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
        $oForm->addElement(new Button(t('Contact US'), 'submit', ['icon' => 'contact']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();

        unset($oSession);
    }
}
