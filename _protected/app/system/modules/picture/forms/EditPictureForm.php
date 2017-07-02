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
use PH7\Framework\Session\Session;

class EditPictureForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_picture']))
        {
            if (\PFBC\Form::isValid($_POST['submit_edit_picture']))
                new EditPictureFormProcess;

            Framework\Url\Header::redirect();
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_edit_picture');
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_picture', 'form_edit_picture'));
        $oForm->addElement(new \PFBC\Element\Token('edit_picture'));

        $oHttpRequest = new Http;
        $oPhoto = (new PictureModel)->photo((new Session)->get('member_id'), $oHttpRequest->get('album_id'), $oHttpRequest->get('picture_id'), 1, 0, 1);
        unset($oHttpRequest);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Name of your photo:'), 'title', array('value'=>$oPhoto->title, 'required'=>1, 'pattern' => $sTitlePattern, 'validation' => new \PFBC\Validation\RegExp($sTitlePattern))));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description of your photo:'), 'description', array('value'=>$oPhoto->description, 'validation'=>new \PFBC\Validation\Str(2,200))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
