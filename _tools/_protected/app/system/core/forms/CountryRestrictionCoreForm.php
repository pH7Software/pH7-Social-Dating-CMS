<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Country;
use PFBC\Element\Hidden;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class CountryRestrictionCoreForm
{
    const FORM_COUNTRY_FIELD_SIZE = 20;

    public static function display($sTable = DbTableName::MEMBER_COUNTRY)
    {
        if (isset($_POST['submit_country_restriction'])) {
            if (\PFBC\Form::isValid($_POST['submit_country_restriction'])) {
                new CountryRestrictionCoreFormProcess($sTable);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_country_restriction');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_country_restriction', 'form_country_restriction'));
        $oForm->addElement(new Token('block_country'));
        $oForm->addElement(
            new Country(
                t('Countries to be showed on registration forms'),
                'countries[]',
                [
                    'description' => self::getCountryFieldDesc($sTable),
                    'multiple' => 'multiple',
                    'size' => self::FORM_COUNTRY_FIELD_SIZE,
                    'value' => self::getSelectedCountries($sTable)
                ]
            )
        );
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }

    /**
     * @param string $sTable
     *
     * @return array
     */
    private static function getSelectedCountries($sTable)
    {
        $aSelectedCountries = [];

        $aCountries = (new UserCoreModel)->getCountries($sTable);
        foreach ($aCountries as $oCountry) {
            $aSelectedCountries[] = $oCountry->countryCode;
        }

        return $aSelectedCountries;
    }

    /**
     * @param string $sModuleType
     *
     * @return string
     */
    private static function getCountryFieldDesc($sModuleType)
    {
        if ($sModuleType === DbTableName::MEMBER_COUNTRY) {
            $sMessage = t('You can select/multi-select the amount of countries to be displayed on the registration form and user search forms.');
        } else {
            $sMessage = t('You can select/multi-select the amount of countries to be displayed on the registration form.');
        }

        $sMessage .= '<br />';
        $sMessage .= t('If you need to block all Internet traffic to your website from a specific country, please use <a href="%0%">Country Blocker</a> tool.', Uri::get(PH7_ADMIN_MOD, 'tool', 'blockcountry'));

        return $sMessage;
    }
}
