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
use PFBC\Element\Select;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
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
        $oForm->addElement(new Hidden('submit_forum', 'form_forum'));
        $oForm->addElement(new Token('forum'));
        $oForm->addElement(
            new Select(
                t('Category Name:'),
                'category_id',
                $aCategoriesName,
                [
                    'value' => (new Http)->get('category_id'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Textbox(t('Forum Name:'),
                'name', [
                    'id' => 'str_name',
                    'onblur' => 'CValid(this.value,this.id,2,60)',
                    'pattern' => $sTitlePattern,
                    'required' => 1,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_name"></span>'));
        $oForm->addElement(
            new Textarea(
                t('Description:'),
                'description',
                [
                    'id' => 'str_description',
                    'required' => 1,
                    'onblur' => 'CValid(this.value,this.id,4,190)',
                    'validation' => new Str(4, 190)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_description"></span>'));
        $oForm->addElement(new Button);
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
