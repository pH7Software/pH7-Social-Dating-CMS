<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Number;
use PFBC\Element\Radio;
use PFBC\Element\Select;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

class MembershipForm
{
    public static function display()
    {
        if (isset($_POST['submit_membership'])) {
            if (\PFBC\Form::isValid($_POST['submit_membership'])) {
                new MembershipFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_membership');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_membership', 'form_membership'));
        $oForm->addElement(new Token('membership'));
        $oForm->addElement(
            new Textbox(
                t('Name:'),
                'name',
                [
                    'required' => 1,
                    'validation' => new \PFBC\Validation\Str(2, 64)
                ]
            )
        );
        $oForm->addElement(
            new Textarea(
                t('Description:'),
                'description',
                [
                    'required' => 1,
                    'validation' => new \PFBC\Validation\Str(5, 190)
                ]
            )
        );

        $aPerms = include dirname(__DIR__) . PH7_DS . PH7_CONFIG . 'perms.inc.php';

        foreach ($aPerms as $sKey => $sVal) {
            $sLabel = (new Str)->upperFirstWords(str_replace('_', ' ', $sKey));
            $oForm->addElement(
                new Select(
                    $sLabel,
                    'perms[' . $sKey . ']',
                    [
                        1 => t('Yes'),
                        0 => t('No')
                    ],
                    [
                        'value' => $sVal
                    ]
                )
            );
        }
        unset($aPerms);

        $oForm->addElement(
            new Number(
                t('Price:'),
                'price',
                [
                    'description' => t('Currency: %0%. 0 = Free. To change the currency, please <a href="%1%">go to settings</a>.', Config::getInstance()->values['module.setting']['currency_code'], Uri::get('payment', 'admin', 'config')),
                    'step' => '0.01',
                    'min' => 0,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Number(
                t('Duration (expiration days):'),
                'expiration_days',
                [
                    'description' => t('0 = Unlimited'),
                    'min' => 0,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Radio(
                t('Status:'),
                'enable',
                [
                    1 => t('Enabled'),
                    0 => t('Disabled')
                ],
                [
                    'value' => 1,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new Button(t('Add')));
        $oForm->render();
    }
}
