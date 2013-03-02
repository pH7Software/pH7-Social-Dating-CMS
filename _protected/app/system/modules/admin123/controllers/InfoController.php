<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */
namespace PH7;
class InfoController extends Controller
{

    private $sTitle;

    public function index()
    {
        Framework\Url\HeaderUrl::redirect(Framework\Mvc\Router\UriRoute::get(PH7_ADMIN_MOD,
            'info', 'software'));
    }

    public function language()
    {
        $this->sTitle = t('Php Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function software()
    {
        $this->sTitle = t('%software_name% Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function __destruct()
    {
        unset($this->sTitle);
    }

}
