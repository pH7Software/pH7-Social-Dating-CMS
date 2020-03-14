<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Search;
use PFBC\Element\Select;
use PH7\Framework\Mvc\Router\Uri;

class SearchTopicForm
{
    public static function display()
    {
        $oForm = new \PFBC\Form('form_search');
        $oForm->configure(['action' => Uri::get('forum', 'forum', 'result') . PH7_SH, 'method' => 'get']);
        $oForm->addElement(
            new Search(
                t('Name, Keyword of message, Author (username) or ID of Topic Forum:'),
                'looking'
            )
        );
        $oForm->addElement(
            new Select(
                t('Browse By:'),
                'order',
                [
                    SearchCoreModel::TITLE => t('Title'),
                    SearchCoreModel::VIEWS => t('Popular'),
                    SearchCoreModel::CREATED => t('Created Date'),
                    SearchCoreModel::UPDATED => t('Updated Date')
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
                ],
                [
                    'value' => SearchCoreModel::DESC
                ]
            )
        );
        $oForm->addElement(new Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->render();
    }
}
