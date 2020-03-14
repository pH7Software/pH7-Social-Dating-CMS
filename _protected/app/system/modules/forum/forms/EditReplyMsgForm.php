<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditReplyMsgForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_reply_msg'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_reply_msg'])) {
                new EditReplyMsgFormProcess();
            }

            Header::redirect();
        }

        $oHttpRequest = new HttpRequest;
        $oMsg = (new ForumModel)->getMessage(
            $oHttpRequest->get('topic_id'),
            $oHttpRequest->get('message_id'),
            (new Session)->get('member_id'),
            '1',
            0,
            1
        );
        unset($oHttpRequest);

        $oForm = new \PFBC\Form('form_edit_reply_msg');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_edit_reply_msg', 'form_edit_reply_msg'));
        $oForm->addElement(new Token('edit_reply_msg'));

        $sEditorClass = FormHelper::getEditorPfbcClassName();
        $oForm->addElement(
            new $sEditorClass(
                t('Message:'),
                'message',
                [
                    'value' => $oMsg->message,
                    'required' => 1,
                    'validation' => new Str(4)
                ]
            )
        );
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
