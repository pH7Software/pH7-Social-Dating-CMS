<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_msg', 'form_msg'));
        $oForm->addElement(new \PFBC\Element\Token('msg'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="center italic bold s_bMarg">' . t('ATTENTION! Sending emails may take several tens of minutes/hours.') . '<br />' . t('Once the form is sent, do not close the browser page!') . '</p>'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'only_subscribers', ['1' => t('Only subscribers registered from the newsletter list')]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Subject:'), 'subject', ['validation' => new \PFBC\Validation\Str(5, 80), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Body:'), 'body', ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Send!'), 'submit', ['icon' => 'mail-closed']));
        $oForm->render();
    }
}
