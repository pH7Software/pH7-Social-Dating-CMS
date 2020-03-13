<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Url\Header;

/** For "user" and "affiliate" module **/
class ResendActivationCoreForm
{
    /**
     * @param string $sTable
     */
    public static function display($sTable = DbTableName::MEMBER)
    {
        // Show the form only if the activation mode is activated by email
        if (self::isEmailActivation($sTable)) {
            if (isset($_POST['submit_resend_activation'])) {
                if (\PFBC\Form::isValid($_POST['submit_resend_activation'])) {
                    new ResendActivationCoreFormProcess($sTable);
                }

                Header::redirect();
            }

            $oForm = new \PFBC\Form('form_resend_activation');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new Hidden('submit_resend_activation', 'form_resend_activation'));
            $oForm->addElement(new Token('resend_activation'));
            $oForm->addElement(new Email(t('Your Email:'), 'mail', ['id' => 'email', 'onblur' => 'CValid(this.value, this.id,\'user\',\'' . $sTable . '\')', 'required' => 1]));
            $oForm->addElement(new HTMLExternal('<span class="input_error email"></span>'));
            $oForm->addElement(new CCaptcha(t('Captcha'), 'captcha', ['id' => 'ccaptcha', 'onkeyup' => 'CValid(this.value, this.id)', 'description' => t('Enter the below code:')]));
            $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
            $oForm->addElement(new Button(t('Send Activation'), 'submit', ['icon' => 'key']));
            $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
            $oForm->render();
        }
    }

    /**
     * @param string $sTable
     *
     * @return bool
     */
    private static function isEmailActivation($sTable)
    {
        $sMod = $sTable === DbTableName::AFFILIATE ? 'aff' : 'user';

        return DbConfig::getSetting($sMod . 'ActivationType') == RegistrationCore::EMAIL_ACTIVATION;
    }
}
