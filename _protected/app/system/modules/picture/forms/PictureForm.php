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
use PFBC\Element\HTMLExternal;
use PFBC\Element\Select;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class PictureForm
{
    public static function display()
    {
        if (isset($_POST['submit_picture'])) {
            if (\PFBC\Form::isValid($_POST['submit_picture'])) {
                new PictureFormProcess();
            }

            Header::redirect();
        }

        $oAlbums = (new PictureModel)->getAlbumsName((new Session)->get('member_id'));
        $aAlbumName = [];

        foreach ($oAlbums as $oAlbum) {
            $aAlbumName[$oAlbum->albumId] = $oAlbum->name;
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_picture');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_picture', 'form_picture'));
        $oForm->addElement(new Token('picture'));

        $oForm->addElement(
            new Select(
                t('Choose your album - OR - <a href="%0%">Add a new Album</a>', Uri::get('picture', 'main', 'addalbum')),
                'album_id',
                $aAlbumName,
                [
                    'value' => self::getAlbumId(),
                    'required' => 1
                ]
            )
        );
        unset($aAlbumName);

        $oForm->addElement(new Hidden('album_title', @$oAlbums[0]->name));
        $oForm->addElement(
            new Textbox(
                t('Name for your photo(s):'),
                'title',
                [
                    'pattern' => $sTitlePattern,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(
            new File(
                t('Your photo(s):'),
                'photos[]',
                [
                    'description' => '<span class="bold">' . t('Tip:') . '</span> ' . t('You can select multiple photos at once by clicking multiple files while holding down the "CTRL" key.'),
                    'multiple' => 'multiple',
                    'accept' => 'image/*',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new HTMLExternal(
                '<p class="pfbc-label"><em><span class="bold">' . t('Note:') . '</span> ' . t('Please be patient while downloading pictures, this may take time (especially if you download a lot of photos at once).') . '</em></p>'
            )
        );
        $oForm->addElement(
            new Textarea(
                t('Description for your photo(s):'),
                'description',
                [
                    'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH)
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Upload'),
                'submit',
                [
                    'icon' => 'image'
                ]
            )
        );
        $oForm->render();
    }

    /**
     * Get the album ID value.
     *
     * @return int|null
     */
    private static function getAlbumId()
    {
        $oHttpRequest = new HttpRequest;
        $iAlbumId = $oHttpRequest->getExists('album_id') ? $oHttpRequest->get('album_id') : null;
        unset($oHttpRequest);

        return $iAlbumId;
    }
}
