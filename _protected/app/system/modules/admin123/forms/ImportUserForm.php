<?php

/**
 * @title          Import Users Class
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Select;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;

class ImportUserForm
{
    public static function display()
    {
        if (isset($_POST['submit_import_user'])) {
            if (\PFBC\Form::isValid('form_import_user')) {
                new ImportUserFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_import_user');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new HTMLExternal('<p>' . t('Preview or import members from a CSV export. Preview checks email validity and duplicates without changing member data.') . '</p>'));
        $oForm->addElement(new HTMLExternal('<p class="small">' . t('For UNA 14.0.0, first create a compatible CSV with the documented pH7Builder migration command. Password hashes and profile photos are not copied.') . '</p>'));
        $oForm->addElement(new Hidden('submit_import_user', 'form_import_user'));
        $oForm->addElement(new Token('import_user'));
        $oForm->addElement(new File(t('CSV file:'), 'csv_file', ['accept' => '.csv', 'required' => 1]));
        $oForm->addElement(
            new Select(
                t('Migration action:'),
                'migration_action',
                [
                    'preview' => t('Preview only (recommended)'),
                    'import' => t('Import after reviewing a preview')
                ],
                ['value' => 'preview', 'required' => 1]
            )
        );
        $oForm->addElement(new Select(t('Delimiter:'), 'delimiter', [',', ';', '|'], ['required' => 1]));
        $oForm->addElement(new Select(t('Enclosure:'), 'enclosure', ['"', '/'], ['required' => 1]));
        $oForm->addElement(new Button());
        $oForm->render();
    }
}
