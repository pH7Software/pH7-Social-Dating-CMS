<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Url\Header;

class AlbumForm
{
    public static function display()
    {
        if (isset($_POST['submit_picture_album'])) {
            if (\PFBC\Form::isValid($_POST['submit_picture_album'])) {
                new AlbumFormProcess;
            }

            Header::redirect();
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_picture_album');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_picture_album', 'form_picture_album'));
        $oForm->addElement(new Token('album'));
        $oForm->addElement(
            new Textbox(
                t('Album Cover Name:'),
                'name',
                [
                    'required' => 1,
                    'pattern' => $sTitlePattern,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(
            new Textarea(
                t('Album Cover Description:'),
                'description',
                [
                    'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH)
                ]
            )
        );
        $oForm->addElement(
            new File(
                t('Album Cover Thumbnail:'),
                'album',
                [
                    'accept' => 'image/*',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Submit'),
                'submit',
                [
                    'icon' => 'image'
                ]
            )
        );
        $oForm->render();
    }
}
