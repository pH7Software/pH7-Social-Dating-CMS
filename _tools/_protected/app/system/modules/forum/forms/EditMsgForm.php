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
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
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
        $oForm->addElement(new Hidden('submit_edit_msg', 'form_edit_msg'));
        $oForm->addElement(new Token('edit_msg'));
        $oForm->addElement(
            new Textbox(
                t('Subject:'),
                'title',
                [
                    'value' => $oMsg->title,
                    'id' => 'str_title',
                    'onblur' => 'CValid(this.value,this.id,2,60)',
                    'pattern' => $sTitlePattern,
                    'required' => 1,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_title"></span>'));

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
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
