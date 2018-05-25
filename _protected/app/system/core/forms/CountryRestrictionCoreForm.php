<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

use PH7\Framework\Url\Header;

class CountryRestrictionCoreForm
{
    public static function display($sTable = DbTableName::MEMBER_COUNTRY)
    {
        if (isset($_POST['submit_country_restriction'])) {
            if (\PFBC\Form::isValid($_POST['submit_country_restriction'])) {
                new CountryRestrictionCoreFormProcess($sTable);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_country_restriction');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_country_restriction', 'form_country_restriction'));
        $oForm->addElement(new \PFBC\Element\Token('block_country'));
        $oForm->addElement(new \PFBC\Element\Country(t('Countries showing on forms'), 'countries[]', ['description' => t('You can limit the amount of countries to be displayed on the registration form and search forms.'), 'multiple' => 'multiple', 'size' => 20, 'value' => (new UserCoreModel)->getCountries()]));
        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
