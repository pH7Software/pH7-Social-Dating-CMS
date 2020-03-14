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
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Url\Header;

class EditCategoryForm
{
    public static function display()
    {
        if (isset($_POST['submit_category_edit'])) {
            if (\PFBC\Form::isValid($_POST['submit_category_edit'])) {
                new EditCategoryFormProcess();
            }

            Header::redirect();
        }

        $oCategoryData = (new ForumModel)->getCategory((new HttpRequest)->get('category_id'), 0, 1);
        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_category_edit');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_category_edit', 'form_category_edit'));
        $oForm->addElement(new Token('category_edit'));
        $oForm->addElement(
            new Textbox(
                t('Category Name:'),
                'title',
                [
                    'id' => 'str_category',
                    'value' => $oCategoryData->title,
                    'onblur' => 'CValid(this.value,this.id,2,60)',
                    'pattern' => $sTitlePattern,
                    'required' => 1,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_category"></span>'));
        $oForm->addElement(new Button);
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
