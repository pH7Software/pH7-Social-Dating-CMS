<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_video', 'form_video'));
        $oForm->addElement(new \PFBC\Element\Token('video'));

        $oForm->addElement(new \PFBC\Element\Select(t('Choose your album - OR - <a href="%0%">Add a new Album</a>', Uri::get('video', 'main', 'addalbum')), 'album_id', $aAlbumName, ['value' => self::getAlbumId(), 'required' => 1]));
        unset($aAlbumName);

        $oForm->addElement(new \PFBC\Element\Hidden('album_title', @$oAlbums[0]->name));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Video Name:'), 'title', ['pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern)]));
        $oForm->addElement(new \PFBC\Element\Select('Video Type:', 'type', [t('Choose...'), 'embed' => t('Embed'), 'regular' => t('Regular')], ['id' => 'video-type', 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="hidden" id="regular">'));
        $oForm->addElement(new \PFBC\Element\File(t('Video:'), 'video', ['description' => '<span class="bold">' . t('Note:') . '</span> ' . t('Please be patient while downloading video, this may take time (especially if you download a long video).') . '</em>', 'accept' => 'video/*']));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'agree', ['1' => t('I have the right to distribute this video')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="hidden" id="embed">'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Embed URL:'), 'embed_code', ['description' => t('e.g., %0%', DbConfig::getSetting('defaultVideo')), 'title' => t('Video from Youtube, Vimeo or DailyMotion.'), 'validation' => new \PFBC\Validation\Url]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        $oForm->addElement(new \PFBC\Element\Textarea(t('Video Description:'), 'description', ['validation' => new \PFBC\Validation\Str(2, 200)]));
        $oForm->addElement(new \PFBC\Element\Button(t('Upload'), 'submit', ['icon' => 'video']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'form.js"></script>'));
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
