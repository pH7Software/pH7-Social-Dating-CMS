<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Select;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PFBC\Validation\Url;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class VideoForm
{
    public static function display()
    {
        if (isset($_POST['submit_video'])) {
            if (\PFBC\Form::isValid($_POST['submit_video'])) {
                new VideoFormProcess();
            }

            Header::redirect();
        }

        $oAlbums = (new VideoModel)->getAlbumsName((new Session)->get('member_id'));
        $aAlbumName = [];
        foreach ($oAlbums as $oAlbum) {
            $aAlbumName[$oAlbum->albumId] = $oAlbum->name;
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_video');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_video', 'form_video'));
        $oForm->addElement(new Token('video'));

        $oForm->addElement(
            new Select(
                t('Choose your album - OR - <a href="%0%">Add a new Album</a>', Uri::get('video', 'main', 'addalbum')),
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
                t('Video Name:'),
                'title',
                [
                    'pattern' => $sTitlePattern,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Video Type:'),
                'type',
                [
                    t('Choose...'),
                    'embed' => t('Embed (from video platform)'),
                    'regular' => t('Regular (from device/computer)')
                ],
                [
                    'id' => 'video-type',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<div class="hidden" id="regular">'));
        $oForm->addElement(
            new File(
                t('Video:'),
                'video',
                [
                    'description' => '<span class="bold">' . t('Note:') . '</span> ' . t('Please be patient while downloading video, this may take time (especially if you download a long video).') . '</em>',
                    'accept' => 'video/*'
                ]
            )
        );
        $oForm->addElement(
            new Checkbox(
                '',
                'agree',
                [
                    '1' => t('I have the right to distribute this video')
                ]
            )
        );
        $oForm->addElement(
            new HTMLExternal(
                '</div><div class="hidden" id="embed">'
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Embed URL:'),
                'embed_code',
                [
                    'description' => t('e.g., %0%', DbConfig::getSetting('defaultVideo')),
                    'title' => t('Video from Youtube, Vimeo or DailyMotion.'),
                    'validation' => new Url
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('</div>'));

        $oForm->addElement(
            new Textarea(
                t('Video Description:'),
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
                    'icon' => 'video'
                ]
            )
        );
        $oForm->addElement(
            new HTMLExternal(
                '<script src="' . PH7_URL_STATIC . PH7_JS . 'form.js"></script>'
            )
        );
        $oForm->render();
    }

    /**
     * Get album ID value.
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
