<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class SearchMailForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(['action' => self::getActionUrl() . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new \PFBC\Element\Search(t('Search a message:'), 'looking', ['description' => t('Enter a keyword in the Subject, Contents, Author (username, first name, last name) or message ID.')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', [SearchCoreModel::TITLE => t('Subject'), SearchCoreModel::USERNAME => t('Author (username)'), SearchCoreModel::SEND_DATE => t('Recent')]));
        if (!self::isAdminLoggedAndNotUser()) {
            $oForm->addElement(new \PFBC\Element\Select(t('Where:'), 'where', [MailModel::INBOX => t('Inbox'), MailModel::OUTBOX => t('Outbox'), MailModel::TRASH => t('Trash')]));
        }
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }

    /**
     * @return string
     */
    private static function getActionUrl()
    {
        if (self::isAdminLoggedAndNotUser()) {
            return Uri::get('mail', 'admin', 'msglist');
        }

        return Uri::get('mail', 'main', 'result');
    }

    /**
     * @return bool
     */
    private static function isAdminLoggedAndNotUser()
    {
        return AdminCore::auth() && !UserCore::auth();
    }
}
