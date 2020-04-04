<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Search;
use PFBC\Element\Select;
use PH7\Framework\Mvc\Router\Uri;

class SearchSubscriberForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(
            [
                'action' => Uri::get('newsletter', 'admin', 'browse') . PH7_SH,
                'method' => 'get'
            ]
        );
        $oForm->addElement(
            new Search(
                t('Search an Subscriber:'),
                'looking',
                [
                    'description' => t('Enter their ID, Name, Email or IP address.')
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Browse By:'),
                'order',
                [
                    SearchCoreModel::EMAIL => t('Email'),
                    SearchCoreModel::NAME => t('Name'),
                    SearchCoreModel::LATEST => t('Latest')
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Direction:'),
                'sort',
                [
                    SearchCoreModel::ASC => t('Ascending'),
                    SearchCoreModel::DESC => t('Descending')
                ]
            )
        );
        $oForm->addElement(new Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
