<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Form / Processing
 */
namespace PH7;

defined('PH7') or die('Restricted access');

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Url\Header,
PH7\Framework\Mvc\Router\Uri;

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
            $aData = [
                'post_id' => $sPostId,
                'lang_id' => $this->httpRequest->post('lang_id'),
                'title' => $this->httpRequest->post('title'),
                'content' => $this->httpRequest->post('content', Http::ONLY_XSS_CLEAN), // HTML contents, So we use the constant: \PH7\Framework\Mvc\Request\Http::ONLY_XSS_CLEAN
                'slogan' => $this->httpRequest->post('$slogan'),
                'tags'=> $this->httpRequest->post('tags'),
                'page_title' => $this->httpRequest->post('page_title'),
                'meta_description' => $this->httpRequest->post('meta_description'),
                'meta_keywords' => $this->httpRequest->post('meta_keywords'),
                'meta_robots' => $this->httpRequest->post('meta_robots'),
                'meta_author' => $this->httpRequest->post('meta_author'),
                'meta_copyright' => $this->httpRequest->post('meta_copyright'),
                'enable_comment' => $this->httpRequest->post('enable_comment'),
                'created_date' => $this->dateTime->get()->dateTime('Y-m-d H:i:s')
            ];

            if (!$oBlogModel->addPost($aData)) {
                \PFBC\Form::setError('form_blog', t('An error occurred while adding the post.'));
            } else {
                $this->setCategories($oBlogModel);

                /*** Set the thumbnail if there's one ***/
                $oPost = $oBlogModel->readPost($aData['post_id']);
                $oBlog->setThumb($oPost, $this->file);

                Blog::clearCache();
                Header::redirect(Uri::get('blog', 'main', 'read', $sPostId), t('Post successfully created!'));
            }
        }
    }

    /**
     * Set the categorie(s).
     *
     * @param \PH7\BlogModel $oBlogModel
     * @return void
     *
     * @internal WARNING: Be careful, you should use the \PH7\Framework\Mvc\Request\Http::ONLY_XSS_CLEAN constant,
     * otherwise the Http::post() method removes the special tags and damages the SQL queries for entry into the database.
     */
    protected function setCategories(BlogModel $oBlogModel)
    {
        $iBlogId = Db::getInstance()->lastInsertId();

        foreach ($this->httpRequest->post('category_id', Http::ONLY_XSS_CLEAN) as $iCategoryId) {
            $oBlogModel->addCategory($iCategoryId, $iBlogId);
        }
    }
}
