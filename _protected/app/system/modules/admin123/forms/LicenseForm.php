<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Model\License;

class LicenseForm
{

    private static $_iLicenseId = 1;

    public static function display()
    {
        if (isset($_POST['submit_license']))
        {
            if (\PFBC\Form::isValid($_POST['submit_license']))
                new LicenseFormProcess(self::$_iLicenseId);
            Framework\Url\HeaderUrl::redirect();
        }

        $aLicenseContent = explode(';', (new License)->get(self::$_iLicenseId));

        $oForm = new \PFBC\Form('form_license', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_license', 'form_license'));
        $oForm->addElement(new \PFBC\Element\Token('license'));
        $oForm->addElement(new \PFBC\Element\Hidden('basic_key', $aLicenseContent[0]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your License Key'), 'copyright_key', array('description' => '<strong><a href="' . Core::SOFTWARE_LICENSE_KEY_URL . '">' . t('Buy a license') . '</a></strong> ' . t('to remove the links from us.'), 'value' => $aLicenseContent[1], 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button(t('Register'), 'submit', array('icon' => 'key')));
        $oForm->render();
    }

}
