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

class AvatarForm
{
    public static function display()
    {
        if (isset($_POST['submit_avatar'])) {
            if (\PFBC\Form::isValid($_POST['submit_avatar'])) {
                new AvatarFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_avatar');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_avatar', 'form_avatar'));
        $oForm->addElement(new \PFBC\Element\Token('avatar'));

        if (AdminCore::auth() && !User::auth()) {
            $oForm->addElement(
                new \PFBC\Element\HTMLExternal('<p><a class="s_tMarg bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'user', 'browse') . '">' . t('Back to Browse Users') . '</a></p>')
            );
        }

        $oForm->addElement(new \PFBC\Element\File(t('Your Profile Photo'), 'avatar', ['accept' => 'image/*', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
