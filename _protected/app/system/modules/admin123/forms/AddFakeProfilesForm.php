<?php
/**
 * @title          Add Fake Profiles Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

class AddFakeProfilesForm
{

    public static function display()
    {
        if (isset($_POST['submit_add_fake_profiles']))
        {
            if (\PFBC\Form::isValid($_POST['submit_add_fake_profiles']))
                new AddFakeProfilesFormProcess;

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_fake_profiles',550);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_fake_profiles', 'form_add_fake_profiles'));
        $oForm->addElement(new \PFBC\Element\Token('fake_profiles'));
        $oForm->addElement(new \PFBC\Element\Select(t('Number:'), 'num', array('1', '5', '10', '15'), array('description' => t('Number of fake profiles to add in the same time.'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Button(t('Add Fake Profiles!')));
        $oForm->render();
    }

}
