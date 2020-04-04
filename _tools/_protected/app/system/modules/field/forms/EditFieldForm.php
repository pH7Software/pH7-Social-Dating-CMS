<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Number;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class EditFieldForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_field'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_field'])) {
                new EditFieldFormProcess;
            }

            Header::redirect();
        }

        $sFieldPattern = Config::getInstance()->values['module.setting']['field.pattern'];

        $oForm = new \PFBC\Form('form_edit_field');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_edit_field', 'form_edit_field'));
        $oForm->addElement(new Token('edit_field'));
        $oForm->addElement(
            new Select(
                t('Field Type:'),
                'type',
                [
                    'textbox' => t('Text Box'),
                    'number' => t('Number')
                ],
                [
                    'description' => '<small>' . t('Note: The field will always have the default value even if you updated to another value. Your change is saved, but cannot be retrieve on the form.') . '</small>',
                    'value' => 'textbox',
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Field Name:'),
                'name',
                [
                    'description' => t('Field Name must contain 2-30 alphanumeric characters ([a-z], [A-Z], [0-9] and [_]). Then, you can translate the language key in <span class="italic underline">%0%</span>', PH7_PATH_APP_LANG . PH7_LANG_NAME . PH7_DS . 'language.php'),
                    'value' => (new Http)->get('name'),
                    'pattern' => $sFieldPattern,
                    'title' => t('Field name must contain 2-30 alphanumeric characters ([a-z], [A-Z], [0-9] and [_]).'),
                    'required' => 1,
                    'validation' => new RegExp($sFieldPattern)
                ]
            ));
        $oForm->addElement(
            new Number(
                t('Length Field:'),
                'length',
                [
                    'description' => t('Length of the field in numeric number (e.g., 150).') . '<br /><small>' . t('Note: The field will always have the default value (%0%) even if you updated to another value. Your change is saved, but cannot be retrieve on the form.', FieldModel::MAX_VARCHAR_LENGTH) . '</small>',
                    'value' => FieldModel::MAX_VARCHAR_LENGTH,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Default Field Value'),
                'value',
                [
                    'description' => t('The default value of the field (optional).') . '<br /><small>' . t('Note: The default value will always be empty even if you added a default string. Your change is saved, but cannot be retrieve on the form.') . '</small>',
                    'validation' => new Str(1, 120)
                ]
            )
        );
        $oForm->addElement(new Button(t('Update')));
        $oForm->render();
    }
}
