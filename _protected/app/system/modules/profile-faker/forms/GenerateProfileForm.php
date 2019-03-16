<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form
 */

namespace PH7;

use PH7\Framework\Url\Header;

class GenerateProfileForm
{
    const DEFAULT_AMOUNT_VALUE = 20;

    public static function display($sProfileType)
    {
        if (isset($_POST['submit_generate_profiles'])) {
            if (\PFBC\Form::isValid($_POST['submit_generate_profiles'])) {
                new GenerateProfileFormProcess($sProfileType);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_generate_profiles');
        $oForm->configure(['action' => '']);
        $oForm->addElement(
            new \PFBC\Element\Hidden(
                'submit_generate_profiles',
                'form_generate_profiles'
            )
        );
        $oForm->addElement(new \PFBC\Element\Token('generate_profiles'));
        $oForm->addElement(
            new \PFBC\Element\Number(
                t('Number of Profiles:'),
                'amount',
                [
                    'min' => 1,
                    'value' => self::DEFAULT_AMOUNT_VALUE,
                    'description' => t('Number of profiles to generate. Choosing a high number might takes a few minutes to load.'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Nationality:'),
                'locale',
                static::getNationalities(),
                [
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new \PFBC\Element\Button(t('Generate Profiles')));
        $oForm->render();
    }

    private static function getNationalities()
    {
        return [
            'ALL' => t('Random Nationalities'),
            'en_US' => t('American'),
            'en_AU' => t('Australian'),
            'nl_BE' => t('Dutch Belgian'),
            'fr_BE' => t('French Belgian'),
            'BR' => t('Brazilian'),
            'en_GB' => t('British'),
            'en_CA' => t('English Canadian'),
            'fr_CA' => t('French Canadian'),
            'DK' => t('Danish'),
            'nl_NL' => t('Dutch'),
            'FI' => t('Finnish'),
            'fr_FR' => t('French'),
            'de_DE' => t('German'),
            'IR' => t('Iranian'),
            'en_IE' => t('Irish'),
            'en_NZ' => t('New Zealander'),
            'NO' => t('Norwegian'),
            'es_ES' => t('Spanish (from Spain)'),
            'fr_CH' => t('French Swiss'),
            'it_CH' => t('Italian Swiss'),
            'de_CH' => t('German Swiss'),
            'TR' => t('Turkish')
        ];
    }
}
