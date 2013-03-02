<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Form
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute;

class SearchNoteForm
{

    public static function display($iWidth = 500)
    {
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => UriRoute::get('note', 'main', 'result') . '/', 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search Note Post:'), 'looking', array('title' => t('Enter Name, Keyword of post, Author (username, first name, last name) or ID of a Note.'), 'style' => 'width:' . ($iWidth*1.1) . 'px')));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('title' => t('Title'), 'views' => t('Popular'), 'rating' => t('Rated'), 'created' => t('Created Date'), 'updated' => t('Updated Date'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc' => t('Ascending'), 'desc' => t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', array('icon' => 'search')));
        $oForm->render();
    }

}
