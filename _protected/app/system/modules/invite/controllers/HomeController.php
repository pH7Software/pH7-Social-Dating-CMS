<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Invite / Controller
 */
namespace PH7;

class HomeController extends Controller
{
    private $sTitle;

    public function invitation()
    {
        $this->sTitle = t('Invite your friends');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('Invite your friends to join %site_name%');
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }
}
