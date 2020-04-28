<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Friend / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Search;
use PFBC\Element\Select;
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
        $oForm->addElement(new Search(t("Find %0%'s friends", $sUsername), 'looking', ['description' => t('Enter the First/Last Name, Username, Email or ID of your friend.')]));
        $oForm->addElement(new Select(t('Browse By:'), 'order', [SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email'), SearchCoreModel::LATEST => t('Latest'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::VIEWS => t('Popular'), SearchCoreModel::RATING => t('Rated')]));
        $oForm->addElement(new Select(t('Direction:'), 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
