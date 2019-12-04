<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

class SignupController extends Controller
{
    public function step1()
    {
        $this->setPageInfo(t('Signup Account | Affiliate - %site_name%'));
        $this->output();
    }

    /**
     * Set page title to page name, meta description and page heading.
     *
     * @param string $sPageTitle
     *
     * @return void
     */
    private function setPageInfo($sPageTitle)
    {
        $this->view->page_title = $sPageTitle;
        $this->view->meta_description = $sPageTitle;
        $this->view->h1_title = $sPageTitle;
    }
}
