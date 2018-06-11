<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Url\Header;

class AnalyticsApiForm
{
    public static function display()
    {
        if (isset($_POST['submit_analytics'])) {
            if (\PFBC\Form::isValid($_POST['submit_analytics'])) {
                new AnalyticsApiFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_analytics');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_analytics', 'form_analytics'));
        $oForm->addElement(new \PFBC\Element\Token('analytics'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your analytics code (e.g., Google Analytics)'), 'code', ['value' => (new Design)->analyticsApi(false)]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
