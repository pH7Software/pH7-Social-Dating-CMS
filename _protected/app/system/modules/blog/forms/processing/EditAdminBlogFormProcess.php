<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use stdClass;

class EditAdminBlogFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oBlog = new Blog;
        $oBlogModel = new BlogModel;
        $iBlogId = $this->httpRequest->get('id');
        $sPostId = $oBlogModel->getPostId($iBlogId);
        $oPost = $oBlogModel->readPost($sPostId);

        /*** Updating the ID of the post if it has changed ***/
        $sPostId = $this->str->lower($this->httpRequest->post('post_id'));

        $this->updateCategories($iBlogId, $oPost, $oBlogModel);

        if (!$this->str->equals($sPostId, $oPost->postId)) {
            if ($oBlog->checkPostId($sPostId, $oBlogModel)) {
                $oBlogModel->updatePost('postId', $sPostId, $iBlogId);
            } else {
                \PFBC\Form::setError('form_edit_blog', t('The post ID already exists or is incorrect.'));
                return;
            }
        }

        // Thumbnail
        $oBlog->setThumb($oPost, $this->file);

        if (!$this->str->equals($this->httpRequest->post('title'), $oPost->title)) {
            $oBlogModel->updatePost('title', $this->httpRequest->post('title'), $iBlogId);
        }

        // HTML contents, So we use Http::ONLY_XSS_CLEAN constant
        if (!$this->str->equals($this->httpRequest->post('content', Http::ONLY_XSS_CLEAN), $oPost->content)) {
            $oBlogModel->updatePost(
                'content',
                $this->httpRequest->post('content', Http::ONLY_XSS_CLEAN),
                $iBlogId
            );
        }

        if (!$this->str->equals($this->httpRequest->post('lang_id'), $oPost->langId)) {
            $oBlogModel->updatePost('langId', $this->httpRequest->post('lang_id'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('slogan'), $oPost->slogan)) {
            $oBlogModel->updatePost('slogan', $this->httpRequest->post('slogan'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('tags'), $oPost->tags)) {
            $oBlogModel->updatePost('tags', $this->httpRequest->post('tags'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('page_title'), $oPost->pageTitle)) {
            $oBlogModel->updatePost('pageTitle', $this->httpRequest->post('page_title'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('meta_description'), $oPost->metaDescription)) {
            $oBlogModel->updatePost('metaDescription', $this->httpRequest->post('meta_description'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('meta_keywords'), $oPost->metaKeywords)) {
            $oBlogModel->updatePost('metaKeywords', $this->httpRequest->post('meta_keywords'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('meta_robots'), $oPost->metaRobots)) {
            $oBlogModel->updatePost('metaRobots', $this->httpRequest->post('meta_robots'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('meta_author'), $oPost->metaAuthor)) {
            $oBlogModel->updatePost('metaAuthor', $this->httpRequest->post('meta_author'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('meta_copyright'), $oPost->metaCopyright)) {
            $oBlogModel->updatePost('metaCopyright', $this->httpRequest->post('meta_copyright'), $iBlogId);
        }

        if (!$this->str->equals($this->httpRequest->post('enable_comment'), $oPost->enableComment)) {
            $oBlogModel->updatePost('enableComment', $this->httpRequest->post('enable_comment'), $iBlogId);
        }

        // Updated the modification Date
        $oBlogModel->updatePost('updatedDate', $this->dateTime->get()->dateTime('Y-m-d H:i:s'), $sPostId);
        unset($oBlog, $oBlogModel);

        Blog::clearCache();

        $this->redirectToPostPage($sPostId);
    }

    /**
     * Update categories.
     *
     * @param integer $iBlogId
     * @param stdClass $oPost Post data from the database.
     * @param BlogModel $oBlogModel
     *
     * @return void
     *
     * @internal WARNING: Be careful, you should use Http::NO_CLEAN constant,
     * otherwise Http::post() method removes the special tags and damages the SET function SQL for entry into the database.
     */
    private function updateCategories($iBlogId, stdClass $oPost, BlogModel $oBlogModel)
    {
        if (!$this->str->equals($this->httpRequest->post('category_id', Http::NO_CLEAN), $oPost->categoryId)) {
            $oBlogModel->deleteCategory($iBlogId);

            foreach ($this->httpRequest->post('category_id', Http::NO_CLEAN) as $iCategoryId) {
                $oBlogModel->addCategory($iCategoryId, $iBlogId);
            }
        }
    }

    /**
     * @param string $sPostId
     */
    private function redirectToPostPage($sPostId)
    {
        Header::redirect(
            Uri::get(
                'blog',
                'main',
                'read',
                $sPostId
            ),
            t('Post successfully updated!')
        );
    }
}
