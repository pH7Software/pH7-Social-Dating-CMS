<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminBlogFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oBlog = new Blog;
        $oBlogModel = new BlogModel;

        $sPostId = $this->str->lower($this->httpRequest->post('post_id'));
        if (!$oBlog->checkPostId($sPostId, $oBlogModel)) {
            \PFBC\Form::setError('form_blog', t('The post ID already exists or is incorrect.'));
        } else {
            $aPostData = [
                'post_id' => $sPostId,
                'lang_id' => $this->httpRequest->post('lang_id'),
                'title' => $this->httpRequest->post('title'),
                'content' => $this->httpRequest->post('content', Http::ONLY_XSS_CLEAN), // HTML contents, so we use Http::ONLY_XSS_CLEAN constant
                'slogan' => $this->httpRequest->post('$slogan'),
                'tags' => $this->httpRequest->post('tags'),
                'page_title' => $this->httpRequest->post('page_title'),
                'meta_description' => $this->httpRequest->post('meta_description'),
                'meta_keywords' => $this->httpRequest->post('meta_keywords'),
                'meta_robots' => $this->httpRequest->post('meta_robots'),
                'meta_author' => $this->httpRequest->post('meta_author'),
                'meta_copyright' => $this->httpRequest->post('meta_copyright'),
                'enable_comment' => $this->httpRequest->post('enable_comment'),
                'created_date' => $this->dateTime->get()->dateTime('Y-m-d H:i:s')
            ];

            if (!$oBlogModel->addPost($aPostData)) {
                \PFBC\Form::setError('form_blog', t('An error occurred while adding the post.'));
            } else {
                $this->setCategories($oBlogModel);

                /*** Set the thumbnail if there's one ***/
                $oPost = $oBlogModel->readPost($aPostData['post_id']);
                $oBlog->setThumb($oPost, $this->file);

                Blog::clearCache();

                $this->redirectToPostPage($sPostId);
            }
        }
    }

    /**
     * Set the categorie(s).
     *
     * @param BlogModel $oBlogModel
     *
     * @return void
     *
     * @internal WARNING: Be careful, you should use Http::NO_CLEAN constant,
     * otherwise Http::post() method removes the special tags and damages the SQL queries for entry into the database.
     */
    private function setCategories(BlogModel $oBlogModel)
    {
        $iBlogId = Db::getInstance()->lastInsertId();

        foreach ($this->httpRequest->post('category_id', Http::NO_CLEAN) as $iCategoryId) {
            $oBlogModel->addCategory($iCategoryId, $iBlogId);
        }
    }

    /**
     * @param string $sPostId
     */
    private function redirectToPostPage($sPostId)
    {
        Header::redirect(
            Uri::get('blog', 'main', 'read', $sPostId),
            t('Post successfully created!')
        );
    }
}
