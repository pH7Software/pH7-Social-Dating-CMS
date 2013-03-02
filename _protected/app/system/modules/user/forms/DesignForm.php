<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

class DesignForm
{

    public static function display()
    {
        if(isset($_POST['submit_design'])) {
            if(\PFBC\Form::isValid($_POST['submit_design']))
                new DesignFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_design', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_design', 'form_design'));
        $oForm->addElement(new \PFBC\Element\Token('design'));
        $oForm->addElement(new \PFBC\Element\File(t('Your Wallpaper for your Profile:'), 'wallpaper', array('accept'=>'image/*', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
