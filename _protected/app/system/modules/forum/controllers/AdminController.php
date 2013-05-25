<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Controller
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute, PH7\Framework\Url\HeaderUrl;

class AdminController extends Controller
{

    private $oForumModel, $sMsg;

    public function __construct()
    {
        parent::__construct();
        $this->oForumModel = new ForumModel();
    }

    public function index()
    {
        HeaderUrl::redirect(UriRoute::get('forum', 'forum', 'index'), t('Welcome to the forum administrator mode.'));
    }

    public function addCategory()
    {
        $this->sTitle = t('Add a new Category');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function addForum()
    {
        $this->sTitle = t('Add a new Forum');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function editCategory()
    {
        $this->sTitle = t('Edit the Category');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function editForum()
    {
        $this->sTitle = t('Edit the Forum');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function deleteCategory()
    {
        if ($this->oForumModel->deleteCategory($this->httpRequest->post('id')))
            $this->sMsg = t('Your Category has been deleted!');
        else
            $this->sMsg = t('Oops! Your Category could not be deleted');

        HeaderUrl::redirect(UriRoute::get('forum', 'forum', 'index'), $this->sMsg);
    }

    public function deleteForum()
    {
        if ($this->oForumModel->deleteForum($this->httpRequest->post('id')))
            $this->sMsg = t('Your Forum has been deleted!');
        else
            $this->sMsg = t('Oops! Your Forum could not be deleted');

        HeaderUrl::redirect(UriRoute::get('forum', 'forum', 'index'), $this->sMsg);
    }

    public function __destruct()
    {
        unset($this->oForumModel, $this->sMsg);
    }

}
