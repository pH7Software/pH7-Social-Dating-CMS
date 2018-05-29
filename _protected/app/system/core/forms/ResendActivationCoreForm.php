<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Url\Header;

/** For "user" and "affiliate" module **/
class ResendActivationCoreForm
{
    public static function display($sTable = DbTableName::MEMBER)
    {
        // Show the form only if the activation mode is activated by email
        $sMod = $sTable === DbTableName::AFFILIATE ? 'aff' : 'user';
        if (DbConfig::getSetting($sMod . 'ActivationType') == 2) {
            if (isset($_POST['submit_resend_activation'])) {
                if (\PFBC\Form::isValid($_POST['submit_resend_activation'])) {
                    new ResendActivationCoreFormProcess($sTable);
                }

                Header::redirect();
            }

            $oForm = new \PFBC\Form('form_resend_activation');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new \PFBC\Element\Hidden('submit_resend_activation', 'form_resend_activation'));
            $oForm->addElement(new \PFBC\Element\Token('resend_activation'));
            $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', ['id' => 'email', 'onblur' => 'CValid(this.value, this.id,\'user\',\'' . $sTable . '\')', 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error email"></span>'));
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
            $oForm->addElement(new \PFBC\Element\Button(t('Send Activation'), 'submit', ['icon' => 'key']));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
            $oForm->render();
        }
    }
}
