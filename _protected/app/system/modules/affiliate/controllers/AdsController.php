<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */
namespace PH7;
use PH7\Framework\Navigation\Page;

class AdsController extends Controller
{

    private $sTitle;

    public function index()
    {
        $iTotalAds = (new AdsCoreModel)->total('AdsAffiliates');

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($iTotalAds, 10);
        $this->view->current_page = $oPage->getCurrentPage();
        unset($oPage);

        $this->sTitle = t('Banners');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->h3_title = nt('%n% Banner', '%n% Banners', $iTotalAds);
        $this->output();
    }

}
