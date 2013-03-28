<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */
namespace PH7;

use PH7\Framework\Mvc\Router\UriRoute;

class SearchMailForm
{

    /**
     * Message search form
     *
     * @param boolean $bAdminUrl TRUE = Admin URL, otherwise user url for the research. Default FLASE
     * @return void
     */
    public static function display($bAdminUrl = false)
    {
        $oForm = new \PFBC\Form('form_search', 500);
        $sUrl = ($bAdminUrl) ? UriRoute::get('mail', 'admin', 'msglist') : UriRoute::get('mail', 'main', 'result');
        $oForm->configure(array('action' => $sUrl . '/', 'method'=>'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search a message:'), 'looking', array('title'=>t('Enter a keyword in the Subject, Contents, Author (username, first name, last name) or message ID.'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('title'=>t('Subject'), 'username'=>t('Author (username)'), 'send_date'=>t('Recent'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('asc'=>t('Ascending'), 'desc'=>t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->render();
    }

}
