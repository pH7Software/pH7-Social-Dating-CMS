<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    /** @var ForumModel */
    private $oForumModel;

    /** @var string */
    private $sMsg;

    public function __construct()
    {
        parent::__construct();

        $this->oForumModel = new ForumModel();
    }

    public function index()
    {
        Header::redirect(
            Uri::get('forum', 'forum', 'index'),
            t('Welcome to the forum administrator mode.')
        );
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
        if ($this->oForumModel->deleteCategory($this->httpRequest->post('id'))) {
            $this->sMsg = t('Your Category has been deleted.');
        } else {
            $this->sMsg = t('Oops! Your Category could not be deleted.');
        }

        Header::redirect(
            Uri::get('forum', 'forum', 'index'),
            $this->sMsg
        );
    }

    public function deleteForum()
    {
        if ($this->oForumModel->deleteForum($this->httpRequest->post('id'))) {
            $this->sMsg = t('Your Forum has been deleted.');
        } else {
            $this->sMsg = t('Oops! Your Forum could not be deleted.');
        }

        Header::redirect(
            Uri::get('forum', 'forum', 'index'),
            $this->sMsg
        );
    }
}
