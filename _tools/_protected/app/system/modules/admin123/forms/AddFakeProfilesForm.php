<?php
/**
 * @title          Add Fake Profiles Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;

class AddFakeProfilesForm
{
    const RANGE_AMOUNT_PROFILE = [
        1,
        5,
        10,
        15,
        25
    ];

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
        $oForm->addElement(new Hidden('submit_add_fake_profiles', 'form_add_fake_profiles'));
        $oForm->addElement(new Token('fake_profiles'));
        $oForm->addElement(
            new Select(
                t('Number of Profile:'),
                'num',
                self::RANGE_AMOUNT_PROFILE,
                [
                    'description' => t('Number of fake profiles to add in the same time. Choosing 15 or 25 profiles might take a few minutes.'),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Gender:'),
                'sex',
                [
                    'both' => t('Gentlemen &amp; Ladies'),
                    GenderTypeUserCore::MALE => t('Only Gentlemen'),
                    GenderTypeUserCore::FEMALE => t('Only Ladies')
                ],
                [
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Select(
                t('Nationality:'),
                'nat',
                self::getNationalities(),
                [
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(
            new Button(
                t('Add Fake Profiles'),
                'submit',
                ['icon' => 'plus']
            )
        );
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
