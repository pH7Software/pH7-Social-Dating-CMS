<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Mvc\Router\Uri;

class EditForm
{

    public static function display()
    {
        if (isset($_POST['submit_admin_edit_account']))
        {
            if (\PFBC\Form::isValid($_POST['submit_admin_edit_account']))
                new EditFormProcess;

            Framework\Url\Header::redirect();
        }

        $oHR = new Http;
        // Prohibit other administrators to edit the Root Administrator (ID 1)
        $iProfileId = ($oHR->getExists('profile_id') && $oHR->get('profile_id', 'int') !== 1) ? $oHR->get('profile_id', 'int') : (new Session)->get('admin_id');

        $oAdmin = (new AdminModel)->readProfile($iProfileId, 'Admins');

        $oForm = new \PFBC\Form('form_admin_edit_account');
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_admin_edit_account', 'form_admin_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if ($oHR->getExists('profile_id') && $oHR->get('profile_id', 'int') !== 1)
        {
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="center"><a class="s_button" href="' . Uri::get(PH7_ADMIN_MOD, 'admin', 'browse') . '">' . t('Return to back admins browse') . '</a></p>'));
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', array('value' => $oAdmin->username, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('value' => $oAdmin->email, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('value' => $oAdmin->firstName, 'required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('value' => $oAdmin->lastName, 'required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Sex:'), 'sex', array('male' => t('Male'), 'female' => t('Female')), array('value' => $oAdmin->sex,'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Timezone('Time Zone:', 'time_zone', array('value' => $oAdmin->timeZone, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
