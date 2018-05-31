<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_style', 'form_style'));
        $oForm->addElement(new \PFBC\Element\Token('style'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your custom CSS code'), 'code', ['value' => (new Design)->customCode('css'), 'description' => t("WARNING! Here you don't have to add %0% tags.", '<b><i>&lt;style&gt;&lt;/style&gt;</i></b>'), 'style' => 'height:35rem']));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
