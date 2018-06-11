<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Url\Header;

class CategoryForm
{
    public static function display()
    {
        if (isset($_POST['submit_category'])) {
            if (\PFBC\Form::isValid($_POST['submit_category'])) {
                new CategoryFormProcess();
            }

            Header::redirect();
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_category');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_category', 'form_category'));
        $oForm->addElement(new \PFBC\Element\Token('category'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Category Name:'), 'title', ['id' => 'str_category', 'onblur' => 'CValid(this.value,this.id,2,60)', 'pattern' => $sTitlePattern, 'required' => 1, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_category"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
