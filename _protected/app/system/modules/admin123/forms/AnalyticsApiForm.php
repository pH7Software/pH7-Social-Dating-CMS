<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Design as DesignModel;

class AnalyticsApiForm
{

    public static function display()
    {
        if (isset($_POST['submit_analytics']))
        {
            if (\PFBC\Form::isValid($_POST['submit_analytics']))
                new AnalyticsApiFormProcessing;
            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_analytics', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_analytics', 'form_analytics'));
        $oForm->addElement(new \PFBC\Element\Token('analytics'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Your code analytics (e.g., Google Analytics)'), 'code', array('value' => (new DesignModel)->analyticsApi(false, false))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
