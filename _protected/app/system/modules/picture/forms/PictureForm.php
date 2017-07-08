<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Form
 */
namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;

class PictureForm
{
    public static function display()
    {
        if (isset($_POST['submit_picture']))
        {
            if (\PFBC\Form::isValid($_POST['submit_picture']))
                new PictureFormProcess();

            Framework\Url\Header::redirect();
        }

        $oHttpRequest = new Http;
        $iAlbumIdVal = ($oHttpRequest->getExists('album_id')) ? $oHttpRequest->get('album_id') : null; // Album ID Value
        unset($oHttpRequest);

        $oAlbumId = (new PictureModel)->getAlbumsName((new Session)->get('member_id'));
        $aAlbumName = array();
        foreach ($oAlbumId as $iId) $aAlbumName[$iId->albumId] = $iId->name;

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_picture');
        $oForm->configure(array('action' =>''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_picture', 'form_picture'));
        $oForm->addElement(new \PFBC\Element\Token('picture'));

        $oForm->addElement(new \PFBC\Element\Select(t('Choose your album - OR - <a href="%0%">Add a new Album</a>', Uri::get('picture', 'main', 'addalbum')), 'album_id', $aAlbumName, array('value'=>$iAlbumIdVal, 'required'=>1)));
        unset($aAlbumName);

        $oForm->addElement(new \PFBC\Element\Hidden('album_title', @$iId->name)); // Bad title! Thanks for finding a solution and commit it on http://github.com/pH7Software/pH7-Social-Dating-CMS or send it by email
        $oForm->addElement(new \PFBC\Element\Textbox(t('Name for your photo(s):'), 'title', array('pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern))));
        $oForm->addElement(new \PFBC\Element\File(t('Your photo(s):'), 'photos[]', array('description'=>'<span class="bold">' . t('Tip:') . '</span> ' . t('You can select multiple photos at once by clicking multiple files while holding down the "CTRL" key.'), 'multiple'=>'multiple', 'accept'=>'image/*', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="pfbc-label"><em><span class="bold">' . t('Note:') . '</span> ' . t('Please be patient while downloading pictures, this may take time (especially if you download a lot of photos at once).') . '</em></p>'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description for your photo(s):'), 'description', array('validation'=>new \PFBC\Validation\Str(2,200))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
