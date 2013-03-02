<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class SearchUserForm
{

    public static function display()
    {
        $oAdminModel = new AdminModel;
        $oGroupId = $oAdminModel->getMemberships();
        unset($oAdminModel);

        $aGroupName = array();
        foreach ($oGroupId as $iId) $aGroupName[$iId->groupId] = $iId->name;

        // Generate form Search User
        $oForm = new \PFBC\Form('form_user_search', 500);
        $oForm->configure(array('action' => UriRoute::get(PH7_ADMIN_MOD, 'user', 'result') . '/', 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_user_search', 'form_user_search'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Search for:'), 'what'));
        $oForm->addElement(new \PFBC\Element\Select(t('Where:'), 'where', array('all' => t('All'), 'username' => t('Username'), 'email' => t('Email'), 'firstName' => t('First Name'), 'lastName' => t('Last Name'), 'ip' => t('IP Address')), array('required' => 1)));

        $oForm->addElement(new \PFBC\Element\Select(t('Membership Group:'), 'group_id', $aGroupName, array('value' => 2)));
        unset($aGroupName);

        $oForm->addElement(new \PFBC\Element\Checkbox('', 'ban', array('1' => '<span class="bold">' . t('Only banned user') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('latest' => t('Latest Members'), 'last_activity' => t('Last Activity'), 'last_edit'=> t('Last Account Edit'), 'pending_approval' => t('Pending approval'), 'views' => t('Popular'), 'rating' => t('Rated'), 'username' => t('Username'), 'first_name' => t('First Name'), 'last_name' => t('Last Name'), 'mail' => t('Email'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('desc' => t('Descending'), 'asc' => t('Ascending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', array('icon' => 'search')));
        $oForm->render();
    }

}
