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
                static::getNationalities()
            )
        );
        $oForm->addElement(new \PFBC\Element\Button(t('Generate Profiles')));
        $oForm->render();
    }

    /**
     * @return array
     */
    private static function getNationalities()
    {
        return [
            '' => t('Random Nationalities'),
            'en_US' => t('American'),
            'en_AU' => t('Australian'),
            'nl_BE' => t('Dutch Belgian'),
            'fr_BE' => t('French Belgian'),
            'pt_BR' => t('Brazilian'),
            'en_GB' => t('British'),
            'en_CA' => t('English Canadian'),
            'fr_CA' => t('French Canadian'),
            'da_DK' => t('Danish'),
            'nl_NL' => t('Dutch'),
            'fi_FI' => t('Finnish'),
            'fr_FR' => t('French'),
            'de_DE' => t('German'),
            'fa_IR' => t('Iranian'),
            'en_IE' => t('Irish'),
            'en_NZ' => t('New Zealander'),
            'nb_NO' => t('Norwegian'),
            'ro_RO' => t('Romanian'),
            'ru_RU' => t('Russian'),
            'es_ES' => t('Spanish'),
            'fr_CH' => t('French Swiss'),
            'it_CH' => t('Italian Swiss'),
            'de_CH' => t('German Swiss'),
            'tr_TR' => t('Turkish')
        ];
    }
}
