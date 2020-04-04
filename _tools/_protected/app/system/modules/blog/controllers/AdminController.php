<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
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

        CommentCoreModel::deleteRecipient($iId, 'blog');
        $this->oBlogModel->deleteCategory($iId);
        $this->oBlogModel->deletePost($iId);
        (new Blog)->deleteThumb($iId, 'blog', $this->file);

        Blog::clearCache();

        Header::redirect(
            Uri::get('blog', 'main', 'index'),
            t('Your post has been deleted!')
        );
    }

    public function removeThumb($iId = 0)
    {
        if ((new SecurityToken)->checkUrl()) {
            (new Blog)->deleteThumb($iId, 'blog', $this->file);

            $sMsg = t('The thumbnail has been deleted successfully!');
            $sMsgType = Design::SUCCESS_TYPE;
        } else {
            $sMsg = Form::errorTokenMsg();
            $sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get('blog', 'admin', 'edit', $iId),
            $sMsg,
            $sMsgType
        );
    }
}
