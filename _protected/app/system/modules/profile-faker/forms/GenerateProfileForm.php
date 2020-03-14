<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Number;
use PFBC\Element\Select;
use PFBC\Element\Token;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;

class GenerateProfileForm
{
    const DEFAULT_AMOUNT_VALUE = 20;

    /**
     * @param int $iProfileType
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public static function display($iProfileType)
    {
        if (isset($_POST['submit_generate_profiles'])) {
            if (\PFBC\Form::isValid($_POST['submit_generate_profiles'])) {
                new GenerateProfileFormProcess($iProfileType);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_generate_profiles');
        $oForm->configure(['action' => '']);
        $oForm->addElement(
            new Hidden(
                'submit_generate_profiles',
                'form_generate_profiles'
            )
        );
        $oForm->addElement(new Token('generate_profiles'));
        $oForm->addElement(
            new Number(
                t('Number of Profiles:'),
                'amount',
                [
                    'min' => 1,
                    'value' => self::DEFAULT_AMOUNT_VALUE,
                    'description' => t('Number of profiles to generate. Choosing a high number might take a few minutes to load.'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Gender:'),
                'sex',
                [
                    '' => t('Women &amp; Men'),
                    GenderTypeUserCore::FEMALE => t('Only Women'),
                    GenderTypeUserCore::MALE => t('Only Men')
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Type of Profile:'),
                'locale',
                self::getNationalities(),
                [
                    'value' => Lang::DEFAULT_LOCALE,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Generate Profiles'),
                'submit',
                ['icon' => 'plus']
            )
        );
        $oForm->render();
    }

    /**
     * @return array
     */
    private static function getNationalities()
    {
        return [
            'en_US' => t('American'),
            'en_AU' => t('Australian'),
            'pt_BR' => t('Brazilian'),
            'en_GB' => t('British'),
            'cs_CZ' => t('Czech'),
            'da_DK' => t('Danish'),
            'nl_NL' => t('Dutch'),
            'nl_BE' => t('Dutch Belgian'),
            'en_CA' => t('English Canadian'),
            'fr_CA' => t('French Canadian'),
            'fi_FI' => t('Finnish'),
            'fr_FR' => t('French'),
            'fr_BE' => t('French Belgian'),
            'fr_CH' => t('French Swiss'),
            'de_DE' => t('German'),
            'de_CH' => t('German Swiss'),
            'fa_IR' => t('Iranian'),
            'en_IE' => t('Irish'),
            'it_IT' => t('Italian'),
            'it_CH' => t('Italian Swiss'),
            'en_NZ' => t('New Zealander'),
            'nb_NO' => t('Norwegian'),
            'es_PE' => t('Peruvian'),
            'pt_PT' => t('Portuguese'),
            'ro_RO' => t('Romanian'),
            'ru_RU' => t('Russian'),
            'es_ES' => t('Spanish'),
            'tr_TR' => t('Turkish'),
            'es_VE' => t('Venezuelan')
        ];
    }
}
