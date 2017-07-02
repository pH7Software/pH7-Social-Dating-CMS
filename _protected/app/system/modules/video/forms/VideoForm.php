<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form
 */
namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;

class VideoForm
{
    public static function display()
    {
        if (isset($_POST['submit_video']))
        {
            if (\PFBC\Form::isValid($_POST['submit_video']))
                new VideoFormProcess();

            Framework\Url\Header::redirect();
        }

        $oHttpRequest = new Http;
        $iAlbumIdVal = ($oHttpRequest->getExists('album_id')) ? $oHttpRequest->get('album_id') : null; // Album ID Value
        unset($oHttpRequest);

        $oAlbumId = (new VideoModel)->getAlbumsName((new Session)->get('member_id'));
        $aAlbumName = array();
        foreach ($oAlbumId as $iId) $aAlbumName[$iId->albumId] = $iId->name;

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_video');
        $oForm->configure(array('action' =>''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_video', 'form_video'));
        $oForm->addElement(new \PFBC\Element\Token('video'));

        $oForm->addElement(new \PFBC\Element\Select(t('Choose your album - OR - <a href="%0%">Add a new Album</a>', Uri::get('video', 'main', 'addalbum')), 'album_id', $aAlbumName, array('value'=>$iAlbumIdVal, 'required'=>1)));
        unset($aAlbumName);

        $oForm->addElement(new \PFBC\Element\Hidden('album_title', @$iId->name)); // Bad title! Thanks for finding a solution and commit it on http://github.com/pH7Software/pH7-Social-Dating-CMS or send it by email
        $oForm->addElement(new \PFBC\Element\Textbox(t('Name of your video:'), 'title', array('pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern))));
        $oForm->addElement(new \PFBC\Element\Select('Video type:', 'type', array(t('Choose...'), 'embed' => t('Embed Code'), 'regular' => t('Regular')), array('id' => 'video-type', 'required'=>1)));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="hidden" id="regular">'));
        $oForm->addElement(new \PFBC\Element\File(t('Video:'), 'video', array('description'=>'<span class="bold">' . t('Note:') . '</span> ' . t('Please be patient while downloading video, this may take time (especially if you download a long video).') . '</em>', 'multiple'=>'multiple', 'accept'=>'video/*')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'agree', array('1'=>t('I have the right to distribute this video'))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div><div class="hidden" id="embed">'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Embed Code:'), 'embed_code', array('description'=>t('Example: %0%', DbConfig::getSetting('defaultVideo')), 'title'=>t('Video from Youtube, Vimeo, DailyMotion or MetaCafe.'), 'validation'=>new \PFBC\Validation\Url)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        $oForm->addElement(new \PFBC\Element\Textarea(t('Description of your video:'), 'description', array('validation'=>new \PFBC\Validation\Str(2,200))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'form.js"></script>'));
        $oForm->render();
    }
}
