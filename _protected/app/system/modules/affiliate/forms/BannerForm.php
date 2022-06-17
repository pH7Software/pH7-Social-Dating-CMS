<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

declare(strict_types=1);

namespace PH7;

use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textarea;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Parse\SysVar;

class BannerForm
{
    private const ADS_PER_PAGE = 10;

    public static function display(): void
    {
        $oForm = new \PFBC\Form('form_banner_ads');
        $oPage = new Page;
        $oAdsModel = new AdsCoreModel;

        $oPage->getTotalPages(
            $oAdsModel->total(AdsCore::AFFILIATE_AD_TABLE_NAME),
            self::ADS_PER_PAGE
        );
        $oAds = $oAdsModel->get(
            null,
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage(),
            DbTableName::AD_AFFILIATE
        );
        unset($oPage, $oAdsModel);

        $oSysVar = new SysVar;
        foreach ($oAds as $oRow) {
            $oForm->addElement(new HTMLExternal('<div id="ad_' . $oRow->adsId . '">')); // Begin ads div tag

            $oForm->addElement(new Hidden('id_ads', $oRow->adsId));
            $oForm->addElement(new HTMLExternal('<h2>' . $oRow->name . '</h2>'));
            $oForm->addElement(new HTMLExternal('<p>' . t('Preview Banner:') . '</p>'));
            $oForm->addElement(new HTMLExternal('<div>' . $oSysVar->parse($oRow->code) . '</div>'));
            $oForm->addElement(
                new Textarea(
                    t('Banner:'),
                    'code',
                    ['readonly' => 'readonly', 'onclick' => 'this.select()', 'value' => $oSysVar->parse($oRow->code)]
                )
            );
            $oForm->addElement(new HTMLExternal('</div>')); // End ads div tag
            $oForm->addElement(new HTMLExternal('<br /><hr /><br />'));
        }
        unset($oSysVar);

        $oForm->render();
    }
}
