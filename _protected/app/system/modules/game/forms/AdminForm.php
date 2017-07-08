<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;

class AdminForm
{
    public static function display()
    {
        if (isset($_POST['submit_game'])) {
            if (\PFBC\Form::isValid($_POST['submit_game']))
                new AdminFormProcess();

            Framework\Url\Header::redirect();
        }

        $oCategoriesData = (new GameModel)->getCategory(null, 0, 500);
        $aCategoriesName = array();
        foreach ($oCategoriesData as $oId)
            $aCategoriesName[$oId->categoryId] = $oId->name;
        unset($oCategoriesData);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_game');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_game', 'form_game'));
        $oForm->addElement(new \PFBC\Element\Token('game'));
        $oForm->addElement(new \PFBC\Element\Select(t('Category Name:'), 'category_id', $aCategoriesName, array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Name of the Game:'), 'name', array('pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Title of the Game:'), 'title', array('validation' => new \PFBC\Validation\Str(2, 120), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Description:'), 'description', array('validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Keywords:'), 'keywords', array('validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\File(t('Thumbnail of the Game:'), 'thumb', array('accept' => 'image/*', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\File(t('File of the Game:'), 'file', array('accept' => 'application/x-shockwave-flash', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}