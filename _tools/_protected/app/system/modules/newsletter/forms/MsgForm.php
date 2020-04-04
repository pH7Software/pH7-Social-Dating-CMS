<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\CKEditor;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Url\Header;

class MsgForm
{
    public static function display()
    {
        if (isset($_POST['submit_msg'])) {
            if (\PFBC\Form::isValid($_POST['submit_msg'])) {
                new MsgFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_msg');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_msg', 'form_msg'));
        $oForm->addElement(new Token('msg'));
        $oForm->addElement(new HTMLExternal('<p class="center italic bold s_bMarg">' . t('ATTENTION! Sending emails may take several tens of minutes/hours.') . '<br />' . t('Once the form is sent, do not close the browser page!') . '</p>'));
        $oForm->addElement(new Checkbox('', 'only_subscribers', ['1' => t('Only subscribers registered from the newsletter list')]));
        $oForm->addElement(new Textbox(t('Subject:'), 'subject', ['validation' => new Str(5, 80), 'required' => 1]));
        $oForm->addElement(new CKEditor(t('Body:'), 'body', ['required' => 1]));
        $oForm->addElement(new Button(t('Send!'), 'submit', ['icon' => 'mail-closed']));
        $oForm->render();
    }
}
