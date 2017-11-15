<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

class SignupController extends Controller
{
    private $sTitle;

    public function step1()
    {
        $this->sTitle = t('Signup Account | Affiliate - %site_name%');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }
}
