<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class DesignForm
{
    public static function display()
    {
        if (isset($_POST['submit_design'])) {
            if (\PFBC\Form::isValid($_POST['submit_design'])) {
                new DesignFormProcess;
            }
            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_design');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_design', 'form_design'));
        $oForm->addElement(new Token('design'));

        if (AdminCore::auth() && !User::auth()) {
            $oForm->addElement(
                new HTMLExternal('<p><a class="s_tMarg bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'user', 'browse') . '">' . t('Back to Browse Users') . '</a></p>')
            );
        }

        $oForm->addElement(new File(t('Your Wallpaper for your Profile'), 'wallpaper', ['accept' => 'image/*', 'required' => 1]));
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
