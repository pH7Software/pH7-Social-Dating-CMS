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
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Parse\SysVar;
use PH7\Framework\Security\CSRF\Token as CSRFToken;
use PH7\Framework\Url\Header;

class UpdateAdsForm
{
    const ADS_PER_PAGE = 10;

    public static function display()
    {
        if (isset($_POST['submit_update_ads'])) {
            if (\PFBC\Form::isValid($_POST['submit_update_ads'])) {
                new UpdateAdsFormProcess;
            }

            Header::redirect();
        }

        $oPage = new Page;
        $oAdsModel = new AdsCoreModel;
        $sTable = AdsCore::getTable();
        $sCSRFToken = (new CSRFToken)->generate('ads');
        $oPage->getTotalPages($oAdsModel->total($sTable), self::ADS_PER_PAGE);
        $oAds = $oAdsModel->get(
            null,
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage(),
            $sTable
        );
        unset($oPage, $oAdsModel);

        $oSysVar = new SysVar;
        foreach ($oAds as $oRow) {
            $oForm = new \PFBC\Form('form_update_ads');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new Hidden('submit_update_ads', 'form_update_ads'));
            $oForm->addElement(new Token('update_ads'));

            // Begin ads div tags
            $oForm->addElement(new HTMLExternal('<div id="ad_' . $oRow->adsId . '">'));

            $oForm->addElement(new Hidden('id_ads', $oRow->adsId));
            $oForm->addElement(new Textbox(t('Title:'), 'title', ['value' => $oRow->name, 'required' => 1, 'validation' => new Str(2, 40)]));
            $oForm->addElement(new HTMLExternal('<p>' . t('Preview Ad:') . '</p>'));
            $oForm->addElement(new HTMLExternal($oSysVar->parse($oRow->code)));

            // ID textarea form was generated with "mt_rand" because it is faster than "uniqid"
            // See also this discussion we asked: http://stackoverflow.com/questions/9152600/uniqid-versus-mt-rand-php-function
            $oForm->addElement(new Textarea(t('Banner (%0%px):', $oRow->width . 'x' . $oRow->height), 'code', ['id' => mt_rand(), 'value' => $oSysVar->parse($oRow->code), 'required' => 1]));
            // mt_rand() function for generate an ID different if it causes problems in the display.
            $oForm->addElement(new Button(t('Update'), 'submit', ['id' => mt_rand()]));

            if (AdsCore::getTable() === AdsCore::AD_TABLE_NAME) {// This feature is not available for affiliate banners
                $oForm->addElement(new HTMLExternal(t('Views: %0% | Clicks: %1%', $oRow->views, $oRow->clicks) . ' | '));
            }

            $oForm->addElement(new HTMLExternal('<a href="javascript:void(0)" onclick="ads(\'delete\',' . $oRow->adsId . ',\'' . $sTable . '\',\'' . $sCSRFToken . '\')">' . t('Delete') . '</a> | '));

            if ($oRow->active == 1) {
                $oForm->addElement(new HTMLExternal('<a href="javascript:void(0)" onclick="ads(\'deactivate\',' . $oRow->adsId . ',\'' . $sTable . '\',\'' . $sCSRFToken . '\')">' . t('Deactivate') . '</a>'));
            } else {
                $oForm->addElement(new HTMLExternal('<a href="javascript:void(0)" onclick="ads(\'activate\',' . $oRow->adsId . ',\'' . $sTable . '\',\'' . $sCSRFToken . '\')">' . t('Activate') . '</a>'));
            }

            // End ads div tags
            $oForm->addElement(new HTMLExternal('</div>'));

            $oForm->render();
        }
        unset($oSysVar);
    }
}
