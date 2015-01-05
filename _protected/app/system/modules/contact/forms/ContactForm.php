<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Form
 */
namespace PH7;

class ContactForm
{

    public static function display()
    {
        // Display the contact form on the template
        if (isset($_POST['submit_contact']))
        {
            if (\PFBC\Form::isValid($_POST['submit_contact']))
                new ContactFormProcess();

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_contact', 400);
        $oForm->configure(array('action'=> '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_contact', 'form_contact'));
        $oForm->addElement(new \PFBC\Element\Token('contact'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', array('id'=>'str_first_name', 'onblur'=>'CValid(this.value, this.id,2,20)', 'title'=>t('Enter your first name.'),'required'=> 1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_first_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Last Name:'), 'last_name', array('id'=>'str_last_name', 'onblur'=>'CValid(this.value, this.id,2,20)', 'title'=>t('Enter your last name.'), 'required'=> 1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_last_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', array('id'=>'email', 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('Enter your valid email address.'), 'required'=> 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
        $oForm->addElement(new \PFBC\Element\Phone(t('Your Phone Number:'), 'phone', array('id'=>'phone', 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('Enter full phone number with area code.'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error phone"></span>'));
        $oForm->addElement(new \PFBC\Element\Url(t('Your Website:'), 'website', array('id'=>'url', 'onblur'=>'CValid(this.value, this.id)', 'title'=>t('If you have a website, please enter your site address.'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error url"></span>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Subject:'), 'subject', array('id'=>'str_subject', 'onblur'=>'CValid(this.value, this.id,4,25)', 'title'=>t('Enter the subject of the message.'), 'required'=> 1, 'validation'=>new \PFBC\Validation\Str(4,25))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_subject"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your Message:'), 'message', array('id'=>'str_message', 'onblur'=>'CValid(this.value, this.id,10,1500)', 'title'=>t('Enter your message.'), 'required'=>1, 'validation'=>new \PFBC\Validation\Str(10,1500))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_message"></span>'));
        $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha:'), 'captcha', array('id'=>'ccaptcha', 'onkeyup'=>'CValid(this.value, this.id)', 'title'=>t('Enter the code above.'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        $oForm->addElement(new \PFBC\Element\Button(t('Contact US'),'submit', array('icon'=>'contact')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script>'));
        $oForm->render();
    }

}
