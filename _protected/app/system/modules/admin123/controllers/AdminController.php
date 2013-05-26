<?php
/**
 * @title          Admin Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */
namespace PH7;

use
PH7\Framework\Navigation\Page,
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Url\HeaderUrl,
PH7\Framework\Mvc\Router\UriRoute;

class AdminController extends Controller
{

    private $oAdminModel, $sTitle, $sMsg, $iTotalAdmins;

    public function __construct()
    {
        parent::__construct();

        $this->oAdminModel = new AdminModel;
    }

    public function index()
    {
        HeaderUrl::redirect(UriRoute::get(PH7_ADMIN_MOD, 'admin', 'browse'));
    }

    public function browse()
    {
        $this->iTotalAdmins = $this->oAdminModel->searchAdmin($this->httpRequest->get('looking'), true,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($this->iTotalAdmins, 10);
        $this->view->current_page = $oPage->getCurrentPage();
        $oSearch = $this->oAdminModel->searchAdmin($this->httpRequest->get('looking'), false,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $oPage->
            getFirstItem(), $oPage->getNbItemsByPage());
        unset($oPage);

        if (empty($oSearch))
        {
            $this->design->setRedirect(UriRoute::get(PH7_ADMIN_MOD, 'admin', 'search'));
            $this->displayPageNotFound(t('Sorry, Your search returned no results!'));
        }
        else
        {
            // Adding the static files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_CSS, 'browse.css');
            $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

            // Assigns variables for views
            $this->view->designSecurity = new Framework\Layout\Html\Security; // Security Design Class
            $this->view->dateTime = $this->dateTime; // Date Time Class

            $this->sTitle = t('Admins Browser');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Admin Result!', '%n% Admins Result!', $this->iTotalAdmins);
            $this->view->browse = $oSearch;
        }

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Search Admin - Looking an Admin');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add an Admin');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function delete()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        (new Admin)->delete($aData[0], $aData[1]);
        HeaderUrl::redirect(UriRoute::get(PH7_ADMIN_MOD, 'admin', 'browse'), t('The admin has been deleted.'));
    }

    public function deleteAll()
    {
        if(!(new Framework\Security\CSRF\Token)->check('admin_action'))
        {
            $this->sMsg = Form::errorTokenMsg();
        }
        elseif (count($this->httpRequest->post('action', HttpRequest::ONLY_XSS_CLEAN)) > 0)
        {
            foreach ($this->httpRequest->post('action', HttpRequest::ONLY_XSS_CLEAN) as $sAction)
            {
                $aData = explode('_', $sAction);
                $iId = (int) $aData[0];
                $sUsername = (string) $aData[1];

                (new Admin)->delete($iId, $sUsername);
            }
            $this->sMsg = t('The admin(s) has been deleted.');
        }

        HeaderUrl::redirect(UriRoute::get(PH7_ADMIN_MOD, 'admin', 'browse'), $this->sMsg);
    }

    public function __destruct()
    {
        unset($this->oAdminModel, $this->sTitle, $this->sMsg, $this->iTotalAdmins);
    }

}
