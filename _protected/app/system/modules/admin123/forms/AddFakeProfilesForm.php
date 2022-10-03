<?php
/**
 * @title          Add Fake Profiles Class
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2014-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

declare(strict_types=1);

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Token;
use PH7\Framework\Url\Header;

class AddFakeProfilesForm
{
    private const RANGE_AMOUNT_PROFILE = [
        1,
        5,
        10,
        15,
        25
    ];

    public static function display(): void
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
     */
    private static function getNationalities(): array
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
            'IN' => t('Indian'),
            'IR' => t('Iranian'),
            'IE' => t('Irish'),
            'MX' => t('Mexican'),
            'NZ' => t('New Zealander'),
            'NO' => t('Norwegian'),
            'RS' => t('Serbian'),
            'ES' => t('Spanish'),
            'CH' => t('Swiss'),
            'TR' => t('Turkish'),
            'UA' => t('Ukrainian')
        ];
    }
}
