<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Controller
 */
namespace PH7;

use
PH7\Framework\Parse\Emoticon,
PH7\Framework\Navigation\Page,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class MainController extends Controller
{
    /**
     * @access protected Protected access because AdminController class is derived from this class and will use these attributes.
     * @var object $oBlogModel
     * @var object $oPage
     * @var string $sTitle
     * @var integer $iTotalBlogs
     */
    protected $oBlogModel, $oPage, $sTitle, $iTotalBlogs;

    public function __construct()
    {
        parent::__construct();
        $this->oBlogModel = new BlogModel;
        $this->oPage = new Page;
    }

    public function index()
    {
        $this->view->page_title = t('The Blog of %site_name%');

        $this->view->total_pages = $this->oPage->getTotalPages($this->oBlogModel->totalPosts(), 5);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oPosts = $this->oBlogModel->getPosts($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->setMenuVars();

        if (empty($oPosts))
        {
            $this->sTitle = t('No Posts');
            $this->notFound(false); // We disable the HTTP error code 404 for Ajax requests running
            $this->view->error = t('Oops! There are no posts at the moment. Please come back soon 😉'); // We change the error message
        }
        else
        {
            $this->view->posts = $oPosts;
        }

        $this->output();
    }

    public function read($sPostId)
    {
        if (!empty($sPostId))
        {
            $oPost = $this->oBlogModel->readPost($sPostId);

            if (!empty($oPost->postId) && $this->str->equals($sPostId, $oPost->postId))
            {
                $aVars = [
                    /***** META TAGS *****/
                    'page_title' => $oPost->pageTitle,
                    'meta_description' => $oPost->metaDescription,
                    'meta_keywords' => $oPost->metaKeywords,

                    'slogan' => $oPost->slogan,
                    'meta_author' => $oPost->metaAuthor,
                    'meta_robots' => $oPost->metaRobots,
                    'meta_copyright' => $oPost->metaCopyright,

                    /***** TITLE AND CONTENT OF PAGE *****/
                    'post_id' => $oPost->postId,
                    'blog_id' => $oPost->blogId,
                    'h1_title' => $oPost->title,
                    'content' => Emoticon::init($oPost->content),
                    'categories' => $this->oBlogModel->getCategory($oPost->blogId, 0, 300),
                    'enable_comment' => $oPost->enableComment,

                    /** Date **/
                    'created_date' => $this->dateTime->get($oPost->createdDate)->dateTime(),
                    'updated_date' => $this->dateTime->get($oPost->updatedDate)->dateTime()
                ];
                $this->view->assigns($aVars);

                // Set Blogs Post Views Statistics
                Framework\Analytics\Statistic::setView($oPost->blogId, 'Blogs');
            }
            else
            {
                $this->sTitle = t('No Blog Found');
                $this->notFound();
            }
        }
        else
        {
            Header::redirect(Uri::get('blog', 'main', 'index'));
        }

        $this->output();
    }

    public function category()
    {
        $sCategory = str_replace('-', ' ', $this->httpRequest->get('name'));
        $sOrder = $this->httpRequest->get('order');
        $sSort = $this->httpRequest->get('sort');

        $this->iTotalBlogs = $this->oBlogModel->category($sCategory, true, $sOrder, $sSort, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalBlogs, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oBlogModel->category($sCategory, false, $sOrder, $sSort, $this->
            oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->setMenuVars();

        $sCategoryTxt = substr($sCategory, 0, 60);
        if (empty($oSearch))
        {
            $this->sTitle = t('No "%0%" category found!', $sCategoryTxt);
            $this->notFound();
        }
        else
        {
            $this->sTitle = t('Search by Category: "%0%" Blog', $sCategoryTxt);
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Post Found!', '%n% Posts Found!', $this->iTotalBlogs);
            $this->view->meta_description = t('Search Blog Post by Category %0% - Dating Social Community Blog', $sCategoryTxt);
            $this->view->meta_keywords = t('search,post,blog,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function search()
    {
        $this->view->page_title = $this->view->h2_title = t('Blog Search - Looking for a post');
        $this->output();
    }

    public function result()
    {
        $this->iTotalBlogs = $this->oBlogModel->search($this->httpRequest->get('looking'), true,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);

        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalBlogs, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oBlogModel->search($this->httpRequest->get('looking'), false,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->
            getFirstItem(), $this->oPage->getNbItemsByPage());

        $this->setMenuVars();

        if (empty($oSearch))
        {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->notFound();
        }
        else
        {
            $this->sTitle = t('Dating Social Blog - Your search returned');
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Post Found!', '%n% Posts Found!', $this->iTotalBlogs);
            $this->view->meta_description = t('Search - Dating Social Community Blog');
            $this->view->meta_keywords = t('search,blog,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    /**
     * Sets the Menu Variables for the template.
     *
     * @access protected
     * @return void
     */
    protected function setMenuVars()
    {
        $this->view->top_views = $this->oBlogModel->getPosts(0, 5, SearchCoreModel::
            VIEWS);
        $this->view->top_rating = $this->oBlogModel->getPosts(0, 5, SearchCoreModel::
            RATING);
        $this->view->categories = $this->oBlogModel->getCategory(null, 0, 50, true);
    }

    /**
     * Set a custom Not Found Error Message with HTTP 404 Code Status.
     *
     * @access protected
     * @param boolean $b404Status For the Ajax blocks and others, we can not put HTTP error code 404, so the attribute must be set to "false". Default: TRUE
     * @return void
     */
    protected function notFound($b404Status = true)
    {
        if ($b404Status) {
            Framework\Http\Http::setHeadersByCode(404);
        }

        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $this->view->error = t('Sorry, we weren\'t able to find the page you requested.<br />
        May we suggest <a href="%0%">exploring some tags</a> or <a href="%1%">creating a new search</a>.',
            Uri::get('blog', 'main', 'index'), Uri::get('blog', 'main', 'search'));
    }

    public function __destruct()
    {
        unset($this->oBlogModel, $this->oPage, $this->sTitle, $this->iTotalBlogs);
    }
}
