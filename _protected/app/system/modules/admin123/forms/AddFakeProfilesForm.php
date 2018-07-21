<?php
/**
 * @title          Add Fake Profiles Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Url\Header;

class AddFakeProfilesForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_fake_profiles'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_fake_profiles'])) {
                new AddFakeProfilesFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_fake_profiles');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_fake_profiles', 'form_add_fake_profiles'));
        $oForm->addElement(new \PFBC\Element\Token('fake_profiles'));
        $oForm->addElement(new \PFBC\Element\Select(t('Number of Profile:'), 'num', [1, 5, 10, 15, 25], ['description' => t('Number of fake profiles to add in the same time. Choosing 15 or 25 profiles might takes a few minutes.'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Gender:'), 'sex', ['both' => t('Gentlemen &amp; Ladies'), 'male' => t('Only Gentlemen'), 'female' => t('Only Ladies')], ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Nationality:'), 'nat', static::getNationalities(), ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Add Fake Profiles!')));
        $oForm->render();
    }

    /**
     * Returns the available nationalities accepted by the API -> https://randomuser.me/documentation#nationalities
     *
     * @return array
     */
    private static function getNationalities()
    {
        return [
            'ALL' => t('Random Nationalities'),
            'US' => t('American'),
            'AU' => t('Australian'),
            'BR' => t('Brazilian'),
            'GB' => t('British'),
            'CA' => t('Canadian'),
            'DK' => t('Danish'),
            'NL' => t('Dutch'),
            'FI' => t('Finnish'),
            'FR' => t('French'),
            'DE' => t('German'),
            'IR' => t('Iranian'),
            'IE' => t('Irish'),
            'NZ' => t('New Zealander'),
            'NO' => t('Norwegian'),
            'ES' => t('Spanish'),
            'CH' => t('Swiss'),
            'TR' => t('Turkish')
        ];
    }
}
