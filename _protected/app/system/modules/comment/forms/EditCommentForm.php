<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_comment', 'form_edit_comment'));
        $oForm->addElement(new \PFBC\Element\Token('edit_comment'));

        $oData = (new CommentModel)->get($oHttpRequest->get('id'), 1, $oHttpRequest->get('table'));
        $oForm->addElement(
            new \PFBC\Element\Textarea(
                t('Edit your comment:'),
                'comment',
                [
                    'value' => $oData->comment,
                    'id' => 'str_com',
                    'onblur' => 'CValid(this.value,this.id,2,2500)',
                    'required' => 1,
                    'validation' => new \PFBC\Validation\Str(2, 2500)
                ]
            )
        );
        unset($oHttpRequest, $oData);

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_com"></span>'));

        $oForm->addElement(new \PFBC\Element\Button(t('Save')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
