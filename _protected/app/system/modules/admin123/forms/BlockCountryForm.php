<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Country;
use PFBC\Element\Hidden;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Model\BlockCountry as BlockCountryModel;
use PH7\Framework\Url\Header;

class BlockCountryForm
{
    const FORM_COUNTRY_FIELD_SIZE = 20;

    public static function display()
    {
        if (isset($_POST['submit_country_blocklist'])) {
            if (\PFBC\Form::isValid($_POST['submit_country_blocklist'])) {
                new BlockCountryFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_country_blocklist');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_country_blocklist', 'form_country_blocklist'));
        $oForm->addElement(new Token('block_country'));
        $oForm->addElement(
            new Country(
                t('Countries to exclude'),
                'countries[]',
                [
                    'description' => t("Visitors who come from one of these selected countries will receive a friendly message saying that the service isn't available in their country. Logged admins and admin panel won't be affected, so you will still be able to login to your admin panel from anywhere in the world."),
                    'multiple' => 'multiple',
                    'size' => self::FORM_COUNTRY_FIELD_SIZE,
                    'value' => (new BlockCountryModel)->getBlockedCountries()
                ]
            )
        );
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
