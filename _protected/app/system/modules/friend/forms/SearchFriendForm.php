<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Friend / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;

class SearchFriendForm
{
    public static function display()
    {
        $oHttpRequest = new HttpRequest;
        $sUsername = $oHttpRequest->get('username');
        $sAction = $oHttpRequest->getExists('action') ? 'mutual' : 'index';
        unset($oHttpRequest);

        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(['action' => Uri::get('friend', 'main', $sAction, $sUsername) . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new \PFBC\Element\Search(t('Search a Friend of "%0%"', $sUsername), 'looking', ['description' => t('Enter the First/Last Name, Username, Email or ID of your friend.')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', [SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email'), SearchCoreModel::LATEST => t('Latest'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::VIEWS => t('Popular'), SearchCoreModel::RATING => t('Rated')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
