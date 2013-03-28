<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

class AvatarForm
{

   public static function display()
   {
        if (isset($_POST['submit_avatar']))
        {
            if (\PFBC\Form::isValid($_POST['submit_avatar']))
                new AvatarFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_avatar', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_avatar', 'form_avatar'));
        $oForm->addElement(new \PFBC\Element\Token('avatar'));
        $oForm->addElement(new \PFBC\Element\File(t('Your Avatar'), 'avatar', array('accept'=>'image/*', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
