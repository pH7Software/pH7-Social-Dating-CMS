<?php
/**
 * @title          Import Users Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Url\Header;

class ImportUserForm
{
    public static function display()
    {
        if (isset($_POST['submit_import_user'])) {
            if (\PFBC\Form::isValid($_POST['submit_import_user'])) {
                new ImportUserFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_import_user');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p>' . t('Import members into your site from a <strong><abbr title="Comma Separated Values">CSV</abbr></strong> file. You can find database users <strong><a href="%0%">here</a></strong>.', 'http://www.saledatingprofiles.com/?from=ph7cms.com') . '</p>'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_import_user', 'form_import_user'));
        $oForm->addElement(new \PFBC\Element\Token('import_user'));
        $oForm->addElement(new \PFBC\Element\File(t('CSV file:'), 'csv_file', ['accept' => '.csv', 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Delimiter:'), 'delimiter', [',', ';', '|'], ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Enclosure:'), 'enclosure', ['"', '/'], ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
