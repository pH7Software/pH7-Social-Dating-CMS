<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class SearchMailForm
{

    public static function display()
    {
        $bAdminLogged = (AdminCore::auth() && !UserCore::auth());

        $oForm = new \PFBC\Form('form_search');
        $sUrl = ($bAdminLogged) ? Uri::get('mail', 'admin', 'msglist') : Uri::get('mail', 'main', 'result');
        $oForm->configure(array('action' => $sUrl . PH7_SH, 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search a message:'), 'looking', array('description' => t('Enter a keyword in the Subject, Contents, Author (username, first name, last name) or message ID.'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array(SearchCoreModel::TITLE => t('Subject'), SearchCoreModel::USERNAME => t('Author (username)'), SearchCoreModel::SEND_DATE => t('Recent'))));
        if (!$bAdminLogged) {
            $oForm->addElement(new \PFBC\Element\Select(t('Where:'), 'where', array(MailModel::INBOX => t('Inbox'), MailModel::OUTBOX => t('Outbox'), MailModel::TRASH => t('Trash'))));
        }
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array(SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', array('icon' => 'search')));
        $oForm->render();
    }

}
