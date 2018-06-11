<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class ForumForm
{
    public static function display()
    {
        if (isset($_POST['submit_forum'])) {
            if (\PFBC\Form::isValid($_POST['submit_forum'])) {
                new ForumFormProcess();
            }

            Header::redirect();
        }

        $aCategoriesName = [];
        $oCategories = (new ForumModel)->getCategory();
        foreach ($oCategories as $oCategory) {
            $aCategoriesName[$oCategory->categoryId] = $oCategory->title;
        }
        unset($oCategories);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_forum');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_forum', 'form_forum'));
        $oForm->addElement(new \PFBC\Element\Token('forum'));
        $oForm->addElement(new \PFBC\Element\Select(t('Category Name:'), 'category_id', $aCategoriesName, ['value' => (new Http)->get('category_id'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Forum Name:'), 'name', ['id' => 'str_name', 'onblur' => 'CValid(this.value,this.id,2,60)', 'pattern' => $sTitlePattern, 'required' => 1, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), 'description', ['id' => 'str_description', 'required' => 1, 'onblur' => 'CValid(this.value,this.id,4,255)', 'validation' => new \PFBC\Validation\Str(4, 255)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
