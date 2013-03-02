<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute, PH7\Framework\Mvc\Request\HttpRequest;

class SearchFriendForm
{

    public static function display()
    {
        $oHttpRequest = new HttpRequest;
        $sUsername = $oHttpRequest->get('username');
        $sAction = ($oHttpRequest->getExists('action')) ? 'mutual' : 'index';
        unset($oHttpRequest);

        $oForm = new \PFBC\Form('form_search', 500);
        $oForm->configure(array('action' => UriRoute::get('user', 'friend', $sAction, $sUsername) . '/' , 'method'=>'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search a Friend of "%0%"', $sUsername), 'looking', array('title'=>t('Enter its First Name, Last Name, Username, Email address or ID of your Friend'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('username'=>t('Username'), 'first_name'=>t('First Name'), 'last_name'=>t('Last Name'), 'mail'=>t('Email'), 'latest'=>t('Latest'), 'last_activity'=>t('Last Activity'),'views'=>t('Popular'), 'rating'=>t('Rated'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc'=>t('Ascending'), 'desc'=>t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->render();
    }

}
