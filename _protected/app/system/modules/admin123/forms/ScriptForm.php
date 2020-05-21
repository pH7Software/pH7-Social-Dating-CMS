<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Url\Header;

class ScriptForm
{
    public static function display()
    {
        if (isset($_POST['submit_script'])) {
            if (\PFBC\Form::isValid($_POST['submit_script'])) {
                new ScriptFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_script');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_script', 'form_script'));
        $oForm->addElement(new Token('script'));
        $oForm->addElement(
            new Textarea(
                t('Your custom JavaScript code'),
                'code',
                [
                    'value' => (new Design)->customCode('js'),
                    'description' => t("WARNING! Here you don't have to add %0% tags.", '<b><i>&lt;script&gt;&lt;/script&gt;</i></b>'),
                    'style' => 'height:35rem'
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Save Changes'),
                'submit',
                [
                    'icon' => 'check'
                ]
            )
        );
        $oForm->render();
    }
}
