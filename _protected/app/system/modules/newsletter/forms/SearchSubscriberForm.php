<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class SearchSubscriberForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(['action' => Uri::get('newsletter', 'admin', 'browse') . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new \PFBC\Element\Search(t('Search an Subscriber:'), 'looking', ['description' => t('Enter their ID, Name, Email or IP address.')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', [SearchCoreModel::EMAIL => t('Email'), SearchCoreModel::NAME => t('Name'), SearchCoreModel::LATEST => t('Latest')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', [SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending')]));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
