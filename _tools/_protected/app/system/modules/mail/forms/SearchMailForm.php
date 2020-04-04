<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Search;
use PFBC\Element\Select;
use PH7\Framework\Mvc\Router\Uri;

class SearchMailForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(['action' => self::getActionUrl() . PH7_SH, 'method' => 'get']);
        
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-bar">'));
        
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-input">'));
        $oForm->addElement(new \PFBC\Element\Search('', 'looking', ['placeholder' => t('Keyword'), 'style' => 'width:250px']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-input">'));
        $oForm->addElement(new Select('', 'order', [SearchCoreModel::TITLE => t('Subject'), SearchCoreModel::USERNAME => t('Author (username)'), SearchCoreModel::SEND_DATE => t('Recent')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-input">'));
        if (!self::isAdminLoggedAndNotUser()) {
            $oForm->addElement(new Select((''), 'where', [MailModel::INBOX => t('Inbox'), MailModel::OUTBOX => t('Outbox'), MailModel::TRASH => t('Trash')]));
        }
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));
        
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-input">'));
        $oForm->addElement(new \PFBC\Element\Select('', 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

               
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="msg-search-input">'));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));

        
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
