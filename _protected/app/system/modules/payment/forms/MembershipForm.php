<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Form
 */
namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Str\Str;

class MembershipForm
{

    public static function display()
    {
        if (isset($_POST['submit_membership']))
        {
            if (\PFBC\Form::isValid($_POST['submit_membership']))
                new MembershipFormProcess();

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_membership');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_membership', 'form_membership'));
        $oForm->addElement(new \PFBC\Element\Token('membership'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Name:'), 'name', array('required' => 1, 'validation' => new \PFBC\Validation\Str(2, 64))));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Description:'), 'description', array('required'=>1, 'validation' => new \PFBC\Validation\Str(5, 255))));

        $aPerms = include dirname(__DIR__) . PH7_DS . PH7_CONFIG . 'perms.inc.php';
        foreach ($aPerms as $sKey => $sVal)
        {
            $sLabel = (new Str)->upperFirstWords( str_replace('_', ' ', $sKey) );
            $oForm->addElement(new \PFBC\Element\Select($sLabel, 'perms[' . $sKey . ']', array(1=>t('Yes'), 0=>t('No')), array('value'=>$sVal)));
        }
        unset($aPerms);

        $oForm->addElement(new \PFBC\Element\Number(t('Price:'), 'price', array('description'=>t('Currency: %0%. 0 = Free. To change the currency, please <a href="%1%">go to settings</a>.', Config::getInstance()->values['module.setting']['currency'], Uri::get('payment','admin','config')), 'step' => '0.01', 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Number(t('Expiration Days:'), 'expiration_days', array('description'=>t('0 = Unlimited'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Status:'), 'enable', array(1=>t('Enabled'), 0=>t('Disabled')), array('value'=>1, 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Button(t('Add')));
        $oForm->render();
    }

}
