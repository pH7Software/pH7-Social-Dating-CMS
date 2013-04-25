<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

class EditFieldForm
{

    public static function display()
    {
        if (isset($_POST['submit_edit_field']))
        {
            if (\PFBC\Form::isValid($_POST['submit_edit_field']))
                new EditFieldFormProcessing;

            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_edit_field', 550);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_field', 'form_edit_field'));
        $oForm->addElement(new \PFBC\Element\Token('edit_field'));

        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
