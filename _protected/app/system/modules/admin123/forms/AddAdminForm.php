<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

class AddAdminForm
{

    public static function display()
    {
        if (isset($_POST['submit_admin_add']))
        {
            if (\PFBC\Form::isValid($_POST['submit_admin_add']))
                new AddAdminFormProcessing;

            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_admin_add', 550);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_admin_add', 'form_admin_add'));
        $oForm->addElement(new \PFBC\Element\Token('admin_add'));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('required' => 1, 'validation' => new \PFBC\Validation\Username('Admins'))));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest', 'Admins'))));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('required' => 1, 'validation' => new \PFBC\Validation\Str(2, 20))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('required' => 1, 'validation' => new \PFBC\Validation\Str(2, 20))));
        $oForm->addElement(new \PFBC\Element\Radio(t('Sex:'), 'sex', array('female' => t('Female'), 'male' => t('Male'), 'couple' => t('Couple')), array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
