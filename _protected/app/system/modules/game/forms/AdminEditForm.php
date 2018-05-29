<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

class AdminEditForm
{
    const MAX_CATEGORIES = 500;

    public static function display()
    {
        if (isset($_POST['submit_edit'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit'])) {
                new AdminEditFormProcess();
            }

            Header::redirect();
        }

        $oHttpRequest = new Http;
        $oGameModel = new GameModel;
        $iGameId = $oHttpRequest->get('id', 'int');
        $oGame = $oGameModel->get(strstr($oHttpRequest->get('title'), '-', true), $iGameId, 0, 1);

        $oCategoriesData = $oGameModel->getCategory(null, 0, self::MAX_CATEGORIES);
        $aCategoriesName = [];
        foreach ($oCategoriesData as $oCategory) {
            $aCategoriesName[$oCategory->categoryId] = $oCategory->name;
        }
        unset($oHttpRequest, $oGameModel);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        if (!empty($oGame) && (new Str)->equals($iGameId, (int)$oGame->gameId)) {
            $oForm = new \PFBC\Form('form_edit');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new \PFBC\Element\Hidden('submit_edit', 'form_edit'));
            $oForm->addElement(new \PFBC\Element\Token('edit'));
            $oForm->addElement(new \PFBC\Element\Select(t('Category Name:'), 'category_id', $aCategoriesName, ['value' => $oGame->categoryId, 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\Textbox(t('Name of the Game:'), 'name', ['value' => $oGame->name, 'pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern), 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\Textbox(t('Title of the Game:'), 'title', ['value' => $oGame->title, 'validation' => new \PFBC\Validation\Str(2, 120), 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\Textbox(t('Description:'), 'description', ['value' => $oGame->description, 'validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\Textbox(t('Keywords:'), 'keywords', ['value' => $oGame->keywords, 'validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\Button);
            $oForm->render();
        } else {
            echo '<p class="center bold">' . t('Game Not Found!') . '</p>';
        }
    }
}
