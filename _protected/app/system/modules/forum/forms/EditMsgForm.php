<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditMsgForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_msg'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_msg'])) {
                new EditMsgFormProcess();
            }

            Header::redirect();
        }

        $oHttpRequest = new HttpRequest;
        $oMsg = (new ForumModel)->getTopic(
            strstr($oHttpRequest->get('forum_name'), '-', true),
            $oHttpRequest->get('forum_id'),
            strstr($oHttpRequest->get('topic_name'), '-', true),
            $oHttpRequest->get('topic_id'),
            (new Session)->get('member_id'),
            '1',
            0,
            1
        );

        unset($oHttpRequest);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_edit_msg');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_msg', 'form_edit_msg'));
        $oForm->addElement(new \PFBC\Element\Token('edit_msg'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Subject:'), 'title', ['value' => $oMsg->title, 'id' => 'str_title', 'onblur' => 'CValid(this.value,this.id,2,60)', 'pattern' => $sTitlePattern, 'required' => 1, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_title"></span>'));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Message:'), 'message', ['value' => $oMsg->message, 'required' => 1, 'validation' => new \PFBC\Validation\Str(4)]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
