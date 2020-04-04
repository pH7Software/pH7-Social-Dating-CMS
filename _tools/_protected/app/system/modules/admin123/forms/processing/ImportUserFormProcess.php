<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class ImportUserFormProcess
{
    public function __construct()
    {
        $oHR = new HttpRequest;
        $aData = (new ImportUser(
            $_FILES['csv_file'],
            $oHR->post('delimiter'),
            $oHR->post('enclosure')
        ))->getResponse();

        if (!$aData['status']) {
            \PFBC\Form::setError('form_import_user', $aData['msg']);
        } else {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                $aData['msg']
            );
        }
    }
}
