<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class EditCommentForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_comment'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_comment'])) {
                new EditCommentFormProcess();
            }

            Header::redirect();
        }

        $oHttpRequest = new Http;

        $oForm = new \PFBC\Form('form_edit_comment');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_edit_comment', 'form_edit_comment'));
        $oForm->addElement(new Token('edit_comment'));

        $oData = (new CommentModel)->get($oHttpRequest->get('id'), 1, $oHttpRequest->get('table'));
        $oForm->addElement(
            new Textarea(
                t('Edit your comment:'),
                'comment',
                [
                    'value' => $oData->comment,
                    'id' => 'str_com',
                    'onblur' => 'CValid(this.value,this.id,2,2500)',
                    'required' => 1,
                    'validation' => new Str(2, 2500)
                ]
            )
        );
        unset($oHttpRequest, $oData);

        $oForm->addElement(new HTMLExternal('<span class="input_error str_com"></span>'));

        $oForm->addElement(new Button(t('Save')));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
