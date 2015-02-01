<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri, PH7\Framework\Mvc\Model\License;

class LicenseForm
{

    private static $_iLicenseId = 1;

    public static function display()
    {
        if (isset($_POST['submit_license']))
        {
            if (\PFBC\Form::isValid($_POST['submit_license']))
                new LicenseFormProcess(self::$_iLicenseId);
            Framework\Url\Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'license', '?set_msg=1'));
        }

        $sStatusColor = (PH7_VALID_LICENSE ? 'success' : 'danger');
        $sLicLink = '<a href="' . Core::SOFTWARE_LICENSE_KEY_URL . '">' . t('Buy a License') . '</a>';
        $sStatusTxt = '<span class="label label-' . $sStatusColor . '">' . (PH7_VALID_LICENSE ? t('Active') : t('Inactive')) . '</span>';
        $sLicTypeTxt = '<span class="italic">' . PH7_LICENSE_NAME . '</span>' . (PH7_LICENSE_NAME != 'Trial' ?: ' <span class="label label-warning">' . t('%0% to get Premium Features!', $sLicLink) . '</span>');

        $oForm = new \PFBC\Form('form_license', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_license', 'form_license'));
        $oForm->addElement(new \PFBC\Element\Token('license'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="bold">' . t('Paid License Status: %0%', $sStatusTxt) . '</p>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="bold">' . t('License Type: %0%', $sLicTypeTxt) . '</p>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your License Key'), 'copyright_key', array('description' => '<strong> ' . $sLicLink . '</strong> ' . t('to remove the Links from us and get the Premium Features.'), 'value' => (new License)->get(self::$_iLicenseId), 'autocomplete' => 'off', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button(t('Register'), 'submit', array('icon' => 'key')));
        $oForm->render();
    }

}
