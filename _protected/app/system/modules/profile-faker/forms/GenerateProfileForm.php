<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form
 */

declare(strict_types=1);

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
    public const MIN_AMOUNT_VALUE = 1;
    public const MAX_AMOUNT_VALUE = 100;

    private const DEFAULT_AMOUNT_VALUE = 20;

    public static function isValidAmount(int $iAmount): bool
    {
        return $iAmount >= self::MIN_AMOUNT_VALUE && $iAmount <= self::MAX_AMOUNT_VALUE;
    }

    /**
     * @param int $iProfileType
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public static function display(int $iProfileType): void
    {
        if (isset($_POST['submit_generate_profiles'])) {
            if (\PFBC\Form::isValid('form_generate_profiles')) {
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
                    'min' => self::MIN_AMOUNT_VALUE,
                    'max' => self::MAX_AMOUNT_VALUE,
                    'value' => self::DEFAULT_AMOUNT_VALUE,
                    'description' => t(
                        'Generate up to %0% profiles per request. Run another batch if you need more.',
                        self::MAX_AMOUNT_VALUE
                    ),
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

    private static function getNationalities(): array
    {
        $aNationalities = [];
        foreach (FakerFactory::SUPPORTED_LOCALES as $sLocale => $sNationality) {
            $aNationalities[$sLocale] = t($sNationality);
        }

        return $aNationalities;
    }
}
