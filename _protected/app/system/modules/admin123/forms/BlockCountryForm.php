<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_country_blocklist', 'form_country_blocklist'));
        $oForm->addElement(new \PFBC\Element\Token('block_country'));
        $oForm->addElement(
            new \PFBC\Element\Country(
                t('Countries to exclude'),
                'countries[]',
                [
                    'description' => t("Visitors who come from one of those selected countries will receive a friendly message saying that the service isn't available in they country. Logged admins and admin panel won't be affected, so you will still be able to login to your admin panel from anywhere."),
                    'multiple' => 'multiple',
                    'size' => self::FORM_COUNTRY_FIELD_SIZE,
                    'value' => (new BlockCountryModel)->getBlockedCountries()
                ]
            )
        );
        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
