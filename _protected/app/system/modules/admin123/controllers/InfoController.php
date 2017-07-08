<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

class InfoController extends Controller
{
    private $sTitle;

    public function index()
    {
        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get(PH7_ADMIN_MOD, 'info', 'software'));
    }

    public function language()
    {
        $this->sTitle = t('PHP Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function software()
    {
        $this->sTitle = t('%software_name% Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->release_date = $this->dateTime->get(Framework\Security\Version::KERNEL_RELASE_DATE)->date();
        $this->output();
    }
}
