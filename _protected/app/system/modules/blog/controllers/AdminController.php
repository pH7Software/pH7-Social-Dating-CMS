<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Controller
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;

class AdminController extends MainController
{
    public function index()
    {
        Header::redirect(
            Uri::get('blog', 'main', 'index'),
            t('Welcome to the Blog administrator mode.')
        );
    }

    public function add()
    {
        $this->view->page_title = $this->view->h1_title = t('Add a Post');
        $this->output();
    }

    public function edit()
    {
        $this->view->page_title = $this->view->h1_title = t('Edit the Post');

        $this->output();
    }

    public function delete()
    {
        $iId = $this->httpRequest->post('id');

        CommentCoreModel::deleteRecipient($iId, 'Blog');
        $this->oBlogModel->deleteCategory($iId);
        $this->oBlogModel->deletePost($iId);
        (new Blog)->deleteThumb($iId, 'blog', $this->file);

        /* Clean BlogModel Cache  */
        (new Cache)->start(BlogModel::CACHE_GROUP, null, null)->clear();

        Header::redirect(
            Uri::get('blog', 'main', 'index'),
            t('Your post has been deleted!')
        );
    }

    public function removeThumb($iId)
    {
        if (!(new SecurityToken)->checkUrl()) {
            exit(Form::errorTokenMsg());
        }

        (new Blog)->deleteThumb($iId, 'blog', $this->file);

        Header::redirect(
            Uri::get('blog', 'admin', 'edit', $iId),
            t('The thumbnail has been deleted successfully!')
        );
    }
}
