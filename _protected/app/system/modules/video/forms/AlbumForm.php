<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form
 */
namespace PH7;

class AlbumForm
{

    public static function display()
    {
        if (isset($_POST['submit_video_album']))
        {
            if (\PFBC\Form::isValid($_POST['submit_video_album']))
                new AlbumFormProcess;

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_video_album', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_video_album', 'form_video_album'));
        $oForm->addElement(new \PFBC\Element\Token('album'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Name of your album:'), 'name', array('required'=>1, 'validation' => new \PFBC\Validation\Str(2, 40))));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description of your album:'), 'description', array('validation' => new \PFBC\Validation\Str(2, 200))));
        $oForm->addElement(new \PFBC\Element\File(t('Thumb of the your album'), 'album', array('accept' => 'image/*', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
