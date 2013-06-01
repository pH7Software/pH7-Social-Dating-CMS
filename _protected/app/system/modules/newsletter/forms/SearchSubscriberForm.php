<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class SearchSubscriberForm
{

    public static function display()
    {
        $oForm = new \PFBC\Form('form_search', 500);
        $oForm->configure(array('action' => UriRoute::get('newsletter', 'admin', 'browse') . '/', 'method'=>'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search an Subscriber:'), 'looking', array('description'=>t('Enter their ID, Name, Email or IP address.'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('email'=>t('Email'), 'name'=>t('Name'), 'mail'=>t('Email'), 'latest'=>t('Latest'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc'=>t('Ascending'), 'desc'=>t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->render();
    }

}
