<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class LoginSplashForm
{
    public static function display()
    {
        if (isset($_POST['submit_login_user'])) {
            if (\PFBC\Form::isValid($_POST['submit_login_user'])) {
                new LoginFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_login_user');
        $oForm->configure(['view' => new \PFBC\View\Horizontal, 'action' => Uri::get('user', 'main', 'login')]);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_login_user', 'form_login_user'));
        $oForm->addElement(new \PFBC\Element\Token('login'));
        $oForm->addElement(new \PFBC\Element\Email('', 'mail', ['placeholder' => t('Your Email'), 'style' => 'width:190px', 'required' => 1], false));
        $oForm->addElement(new \PFBC\Element\Password('', 'password', ['placeholder' => t('Your Password'), 'style' => 'width:190px', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Login'), 'submit', ['icon' => 'key']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="col-md-4 bt_login_remember">'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'remember', [1 => t('Stay signed in')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="col-md-4 bt_login_forgot">' . LostPwdDesignCore::link('user', false) . '</div>'));
        $oForm->render();
    }
}
