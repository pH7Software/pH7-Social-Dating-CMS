<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditAlbumForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_picture_album'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_picture_album'])) {
                new EditAlbumFormProcess();
            }

            Header::redirect();
        }

        $oAlbum = (new PictureModel)->album(
            (new Session)->get('member_id'),
            (new Http)->get('album_id'),
            '1',
            0,
            1
        );

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_edit_picture_album');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_picture_album', 'form_edit_picture_album'));
        $oForm->addElement(new \PFBC\Element\Token('edit_album'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Album Cover Name:'), 'name', ['value' => $oAlbum->name, 'required' => 1, 'pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Album Cover Description:'), 'description', ['value' => $oAlbum->description, 'validation' => new \PFBC\Validation\Str(2, 200)]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
