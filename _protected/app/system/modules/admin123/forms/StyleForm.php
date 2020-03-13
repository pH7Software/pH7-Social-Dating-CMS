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

class StyleForm
{
    public static function display()
    {
        if (isset($_POST['submit_style'])) {
            if (\PFBC\Form::isValid($_POST['submit_style'])) {
                new StyleFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_style');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_style', 'form_style'));
        $oForm->addElement(new Token('style'));
        $oForm->addElement(new Textarea(t('Your custom CSS code'), 'code', ['value' => (new Design)->customCode('css'), 'description' => t("WARNING! Here you don't have to add %0% tags.", '<b><i>&lt;style&gt;&lt;/style&gt;</i></b>'), 'style' => 'height:35rem']));
        $oForm->addElement(new Button(t('Save Changes'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
