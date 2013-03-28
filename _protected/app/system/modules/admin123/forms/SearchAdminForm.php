<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class SearchAdminForm
{

    public static function display()
    {
        $oForm = new \PFBC\Form('form_admin_search', 500);
        $oForm->configure(array('action' => UriRoute::get(PH7_ADMIN_MOD, 'admin', 'browse') . '/', 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search an Admin:'), 'looking', array('description' => t('Enter their ID, First Name, Last Name, Username, Email, Sex or IP address.'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('username' => t('Username'), 'first_name' => t('First Name'), 'last_name' => t('Last Name'), 'mail' => t('Email'), 'latest' => t('Latest Admins'), 'last_activity' => t('Last Activity'), 'last_edit'=> t('Last Account Edit'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc' => t('Ascending'), 'desc' => t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', array('icon' => 'search')));
        $oForm->render();
    }

}
