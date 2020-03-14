<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Url\Header;

class AdminForm
{
    const MAX_CATEGORIES = 500;

    public static function display()
    {
        if (isset($_POST['submit_game'])) {
            if (\PFBC\Form::isValid($_POST['submit_game'])) {
                new AdminFormProcess();
            }

            Header::redirect();
        }

        $oCategoriesData = (new GameModel)->getCategory(null, 0, self::MAX_CATEGORIES);
        $aCategoriesName = [];
        foreach ($oCategoriesData as $oCategory) {
            $aCategoriesName[$oCategory->categoryId] = $oCategory->name;
        }
        unset($oCategoriesData);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_game');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_game', 'form_game'));
        $oForm->addElement(new Token('game'));
        $oForm->addElement(new Select(t('Category Name:'), 'category_id', $aCategoriesName, ['required' => 1]));
        $oForm->addElement(new Textbox(t('Name of the Game:'), 'name', ['pattern' => $sTitlePattern, 'validation' => new RegExp($sTitlePattern), 'required' => 1]));
        $oForm->addElement(new Textbox(t('Title of the Game:'), 'title', ['validation' => new Str(2, 120), 'required' => 1]));
        $oForm->addElement(new Textbox(t('Description:'), 'description', ['validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'required' => 1]));
        $oForm->addElement(new Textbox(t('Keywords:'), 'keywords', ['validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'required' => 1]));
        $oForm->addElement(new File(t('Thumbnail of the Game:'), 'thumb', ['accept' => 'image/*', 'required' => 1]));
        $oForm->addElement(new File(t('File of the Game:'), 'file', ['accept' => 'application/x-shockwave-flash', 'required' => 1]));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
