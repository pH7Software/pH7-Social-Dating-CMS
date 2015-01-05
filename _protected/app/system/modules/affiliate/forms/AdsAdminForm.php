<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */
namespace PH7;

class AdsAdminForm
{

    public static function display()
    {
        if (isset($_POST['submit_admin_ads']))
        {
            if (\PFBC\Form::isValid($_POST['submit_admin_ads']))
                new AdsAdminFormProcess();

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_admin_ads', 500);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_admin_ads', 'form_admin_ads'));
        $oForm->addElement(new \PFBC\Element\Token('admin_ads'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Title'),'title', array('required'=>1, 'validation'=>new \PFBC\Validation\Str(2,20))));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Advertisement'),'code', array('required'=>1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
