<?php
/**
 * @title          Import Users Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Select;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Router\Uri;
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
        $oForm->addElement(new HTMLExternal('<p>' . t('Import members into your site from a <strong><abbr title="Comma Separated Values">CSV</abbr></strong> file. You should be able to find websites that offer dating profiles database. Make sure they offer CSV format.') . '</p>'));
        $oForm->addElement(new HTMLExternal('<p class="small">' . t('Note: This tool cannot import profile photos. Please use the <a href="%0%">Fake Profile Generator</a> if you need profiles with photos', Uri::get(PH7_ADMIN_MOD, 'user', 'addfakeprofiles')) . '</p>'));
        $oForm->addElement(new Hidden('submit_import_user', 'form_import_user'));
        $oForm->addElement(new Token('import_user'));
        $oForm->addElement(new File(t('CSV file:'), 'csv_file', ['accept' => '.csv', 'required' => 1]));
        $oForm->addElement(new Select(t('Delimiter:'), 'delimiter', [',', ';', '|'], ['required' => 1]));
        $oForm->addElement(new Select(t('Enclosure:'), 'enclosure', ['"', '/'], ['required' => 1]));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
