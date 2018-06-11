<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\File\Import;
use PH7\Framework\Url\Header;

class AdsForm
{
    public static function display()
    {
        if (isset($_POST['submit_ads'])) {
            if (\PFBC\Form::isValid($_POST['submit_ads'])) {
                new AdsFormProcess;
            }

            Header::redirect();
        }

        $aAdSizes = Import::file(PH7_PATH_APP_CONFIG . 'ad_sizes');

        $oForm = new \PFBC\Form('form_ads');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_ads', 'form_ads'));
        $oForm->addElement(new \PFBC\Element\Token('ads'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Title:'), 'title', ['required' => 1, 'validation' => new \PFBC\Validation\Str(2, 40)]));
        $oForm->addElement(new \PFBC\Element\Select(t('Size of the Banner:'), 'size', $aAdSizes, ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Banner:'), 'code', ['description' => self::getBannerDesc(), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

    private static function getBannerDesc()
    {
        if (AdsCore::getTable() === AdsCore::AFFILIATE_AD_TABLE_NAME) {
            return t('The predefined variable for the URL of an affiliate account to put in the HTML is: %0%.', '<strong>#!%affiliate_url%!#</strong>');
        }

        return t('The predefined variable to the URL of your site to indicate this in the HTML is: %0%.', '<strong>#!%site_url%!#</strong>');
    }
}
