<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Form
 */

namespace PH7;

use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;

class GenerateProfileForm
{
    const DEFAULT_AMOUNT_VALUE = 10;

    /**
     * @param string $sProfileType
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
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
            new \PFBC\Element\Button(
                t('Generate Profiles'),
                'submit',
                ['icon' => 'plus']
            )
        );
        $oForm->render();
    }
}
