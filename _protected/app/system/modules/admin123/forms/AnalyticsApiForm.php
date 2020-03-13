<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
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
        $oForm->addElement(new Hidden('submit_analytics', 'form_analytics'));
        $oForm->addElement(new Token('analytics'));
        $oForm->addElement(new Textarea(t('Your analytics tracking code (e.g., Google Analytics, Matomo)'), 'code', ['value' => (new Design)->analyticsApi(false)]));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
