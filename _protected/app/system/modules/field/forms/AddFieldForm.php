<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Form
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Url\Header;

class AddFieldForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_field'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_field'])) {
                new AddFieldFormProcess;
            }

            Header::redirect();
        }

        $sFieldPattern = Config::getInstance()->values['module.setting']['field.pattern'];

        $oForm = new \PFBC\Form('form_add_field');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_field', 'form_add_field'));
        $oForm->addElement(new \PFBC\Element\Token('add_field'));
        $oForm->addElement(new \PFBC\Element\Select(t('Field Type:'), 'type', ['textbox' => t('Text Box'), 'number' => t('Number')], ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Field Name:'), 'name', ['description' => t('Field Name must contain 2-30 alphanumeric characters ([a-z], [A-Z], [0-9] and [_], [-]). After you can translate this language key in <span class="italic underline">%0%</span>', PH7_PATH_APP_LANG . PH7_LANG_NAME . PH7_DS . 'language.php'), 'pattern' => $sFieldPattern, 'required' => 1, 'validation' => new \PFBC\Validation\RegExp($sFieldPattern)]));
        $oForm->addElement(new \PFBC\Element\Number(t('Length Field:'), 'length', ['description' => t('Length of the field in numeric number (e.g., 250).'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Default Field Value'), 'value', ['description' => t('The value by default of the field (optional).'), 'validation' => new \PFBC\Validation\Str(2, 120)]));
        $oForm->addElement(new \PFBC\Element\Button(t('Add')));
        $oForm->render();
    }
}
