<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

use PH7\Framework\Navigation\Page;

class AdsController extends Controller
{
    const ADS_PER_PAGE = 10;

    /** @var string */
    private $sTitle;

    public function index()
    {
        $iTotalAds = (new AdsCoreModel)->total(AdsCore::AFFILIATE_AD_TABLE_NAME);

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($iTotalAds, self::ADS_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        unset($oPage);

        $this->sTitle = t('Banners');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->h3_title = nt('%n% Banner', '%n% Banners', $iTotalAds);

        $this->output();
    }
}
