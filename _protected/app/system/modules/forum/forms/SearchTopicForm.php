<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class SearchTopicForm
{

    public static function display()
    {
        $oForm = new \PFBC\Form('form_search', 500);
        $oForm->configure(array('action' => UriRoute::get('forum','forum','result') . '/', 'method'=>'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Name, Keyword of message, Author (username) or ID of Topic Forum:'), 'looking'));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('title'=>t('Title'), 'views'=>t('Popular'), 'created'=>t('Created Date'), 'updated'=>t('Updated Date'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc'=>t('Ascending'), 'desc'=>t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->render();
    }

}
