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

class EditForumForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_forum'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_forum'])) {
                new EditForumFormProcess();
            }

            Header::redirect();
        }

        $oForumModel = new ForumModel;
        $oForumData = $oForumModel->getForum((new Http)->get('forum_id'), 0, 1);

        $aCategoriesName = [];
        $aCategories = $oForumModel->getCategory();
        foreach ($aCategories as $oCategory) {
            $aCategoriesName[$oCategory->categoryId] = $oCategory->title;
        }
        unset($oForumModel, $aCategories);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_edit_forum');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_forum', 'form_edit_forum'));
        $oForm->addElement(new \PFBC\Element\Token('edit_forum'));
        $oForm->addElement(new \PFBC\Element\Select(t('Category Name:'), 'category_id', $aCategoriesName, ['value' => $oForumData->categoryId, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Forum Name:'), 'name', ['value' => $oForumData->name, 'id' => 'str_name', 'onblur' => 'CValid(this.value,this.id,2,60)', 'pattern' => $sTitlePattern, 'required' => 1, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_name"></span>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), 'description', ['value' => $oForumData->description, 'id' => 'str_description', 'required' => 1, 'onblur' => 'CValid(this.value,this.id,4,255)', 'validation' => new \PFBC\Validation\Str(4, 255)]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
