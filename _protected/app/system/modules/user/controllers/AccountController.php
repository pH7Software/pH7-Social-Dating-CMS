<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

class AccountController extends Controller
{

    private $sTitle;

    public function index()
    {
        $this->sTitle = t('Hello <em>%0%</em>, welcome to %site_name%!', $this->session->get('member_first_name'));
        $this->view->page_title = $this->sTitle; // Note: HTML tags are automatically deleted in the title tag of the header.
        $this->view->h1_title = $this->sTitle;
        $this->view->h3_title = t('How are you today?');
        $this->output();
    }

    public function activate($sMail, $sHash)
    {
        (new UserCore)->activateAccount($sMail, $sHash, $this->config, $this->registry);
    }

}
