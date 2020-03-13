<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Search;
use PFBC\Element\Select;
use PH7\Framework\Mvc\Router\Uri;

class SearchAdminForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_admin_search');
        $oForm->configure(['action' => Uri::get(PH7_ADMIN_MOD, 'admin', 'browse') . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new Search(t('Search an Admin:'), 'looking', ['description' => t('Enter their ID, First Name, Last Name, Username, Email, Sex or IP address.')]));
        $oForm->addElement(new Select(t('Browse By:'), 'order', [SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email'), SearchCoreModel::LATEST => t('Latest Admins'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::LAST_EDIT => t('Last Account Edit')]));
        $oForm->addElement(new Select(t('Direction:'), 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
