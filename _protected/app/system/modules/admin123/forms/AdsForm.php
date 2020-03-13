<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
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
        $oForm->addElement(new Hidden('submit_ads', 'form_ads'));
        $oForm->addElement(new Token('ads'));
        $oForm->addElement(new Textbox(t('Title:'), 'title', ['required' => 1, 'validation' => new Str(2, 40)]));
        $oForm->addElement(new Select(t('Size of the Banner:'), 'size', $aAdSizes, ['required' => 1]));
        $oForm->addElement(new Textarea(t('Banner:'), 'code', ['description' => self::getBannerDesc(), 'required' => 1]));
        $oForm->addElement(new Button(t('Save')));
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
