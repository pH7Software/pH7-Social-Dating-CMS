<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_contact', 'form_contact'));
        $oForm->addElement(new \PFBC\Element\Token('contact'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', ['value' => $oSession->get('member_first_name'), 'id' => 'name_first', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_first"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_last"></span>'));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', ['value' => $oSession->get('member_email'), 'id' => 'email', 'onblur' => 'CValid(this.value, this.id)', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new \PFBC\Element\Phone(t('Your Phone Number:'), 'phone', ['id' => 'phone', 'onblur' => 'CValid(this.value, this.id)', 'description' => t('Enter the full phone number including its country calling codes (e.g., +44768374890).')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error phone"></span>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Your Website:'), 'website', ['id' => 'url', 'onblur' => 'CValid(this.value, this.id)', 'description' => t("If you have a website (e.g., your company's website).")]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error url"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Subject:'), 'subject', ['id' => 'str_subject', 'onblur' => 'CValid(this.value, this.id,4,45)', 'required' => 1, 'validation' => new \PFBC\Validation\Str(4, 45)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_subject"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your Message:'), 'message', ['id' => 'str_message', 'onblur' => 'CValid(this.value, this.id,10,2000)', 'required' => 1, 'validation' => new \PFBC\Validation\Str(10, 2000)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_message"></span>'));
        $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['description' => t('Enter the below code:'), 'id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        $oForm->addElement(new \PFBC\Element\Button(t('Contact US'), 'submit', ['icon' => 'contact']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();

        unset($oSession);
    }
}
