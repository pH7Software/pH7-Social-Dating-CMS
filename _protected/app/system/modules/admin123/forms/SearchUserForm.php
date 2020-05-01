<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PH7\Framework\Mvc\Router\Uri;

class SearchUserForm
{
    public static function display()
    {
        $oGroupId = (new AdminModel)->getMemberships();

        $aGroupName = [];
        foreach ($oGroupId as $iId) {
            $aGroupName[$iId->groupId] = $iId->name;
        }

        $oForm = new \PFBC\Form('form_user_search');
        $oForm->configure(
            [
                'action' => Uri::get(PH7_ADMIN_MOD, 'user', 'result') . PH7_SH,
                'method' => 'get'
            ]
        );
        $oForm->addElement(
            new Hidden(
                'submit_user_search',
                'form_user_search'
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Search for:'),
                'what'
            )
        );
        $oForm->addElement(
            new Select(
                t('Where:'),
                'where',
                [
                    'all' => t('All'),
                    SearchCoreModel::USERNAME => t('Username'),
                    SearchCoreModel::EMAIL => t('Email'),
                    SearchCoreModel::FIRST_NAME => t('First Name'),
                    SearchCoreModel::LAST_NAME => t('Last Name'),
                    SearchCoreModel::IP => t('IP Address')
                ],
                ['required' => 1]
            )
        );

        $oForm->addElement(
            new Select(
                t('Membership Group:'),
                'group_id',
                $aGroupName,
                ['value' => 2]
            )
        );
        unset($aGroupName);

        $oForm->addElement(
            new Checkbox('', 'ban', ['1' => '<span class="bold">' . t('Only banned user') . '</span>'])
        );
        $oForm->addElement(
            new Select(
                t('Browse By:'),
                'order',
                [
                    SearchCoreModel::LATEST => t('Newest'),
                    SearchCoreModel::LAST_ACTIVITY => t('Last Activity'),
                    SearchCoreModel::LAST_EDIT => t('Last Account Edit'),
                    SearchCoreModel::PENDING_APPROVAL => t('Pending approval'),
                    SearchCoreModel::VIEWS => t('Popular'),
                    SearchCoreModel::RATING => t('Rated'),
                    SearchCoreModel::USERNAME => t('Username'),
                    SearchCoreModel::FIRST_NAME => t('First Name'),
                    SearchCoreModel::LAST_NAME => t('Last Name'),
                    SearchCoreModel::EMAIL => t('Email')
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Direction:'),
                'sort',
                [
                    SearchCoreModel::DESC => t('Descending'),
                    SearchCoreModel::ASC => t('Ascending')
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Search'),
                'submit',
                ['icon' => 'search']
            )
        );
        $oForm->render();
    }
}
