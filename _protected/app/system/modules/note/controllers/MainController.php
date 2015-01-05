<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Controller
 */
namespace PH7;

use
PH7\Framework\Security\Ban\Ban,
PH7\Framework\Navigation\Page,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class MainController extends Controller
{

    /**
     * @access protected Protected access for the AdminController class derived from this class.
     * @var object $oNoteModel
     * @var object $oPage
     * @var string $sTitle
     * @var integer $iTotalNotes
     */
    protected $oNoteModel, $oPage, $sTitle, $iTotalNotes, $iApproved;

    public function __construct()
    {
        parent::__construct();
        $this->oNoteModel = new NoteModel;
        $this->oPage = new Page;
        $this->iApproved = (AdminCore::auth() && !$this->session->exists('login_user_as')) ? null : 1;

        $this->view->member_id = $this->session->get('member_id');
    }

    public function index()
    {
        $this->view->total_pages = $this->oPage->getTotalPages($this->oNoteModel->totalPosts($this->iApproved), 5);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->page_title = t('The Notes of %site_name%');
        $oPosts = $this->oNoteModel->getPosts($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), SearchCoreModel::UPDATED, $this->iApproved);
        $this->setMenuVars();

        if(empty($oPosts))
        {
            $this->sTitle = t('Empty Note');
            $this->notFound(false); // We disable the HTTP error code 404 for Ajax requests running
            $this->view->error = t('Oops! Empty Note...'); // We change the error message
        }
        else
        {
            $this->view->posts = $oPosts;
        }

        $this->output();
    }

    public function read($sUsername, $sPostId)
    {
        if(isset($sUsername, $sPostId))
        {
            $iProfileId = (new UserCoreModel)->getId(null, $sUsername);
            $oPost = $this->oNoteModel->readPost($sPostId, $iProfileId, $this->iApproved);

            if(!empty($oPost->postId) && $this->str->equals($sPostId, $oPost->postId))
            {
                $aVars = [
                    /***** META TAGS *****/
                    'page_title' => Ban::filterWord($oPost->pageTitle, false),
                    'meta_description' => Ban::filterWord($oPost->metaDescription, false),
                    'meta_keywords' => Ban::filterWord($oPost->metaKeywords, false),

                    'slogan' => Ban::filterWord($oPost->slogan, false),
                    'meta_author' => Ban::filterWord($oPost->metaAuthor, false),
                    'meta_robots' => Ban::filterWord($oPost->metaRobots, false),
                    'meta_copyright' => Ban::filterWord($oPost->metaCopyright, false),

                    /***** CONTENTS *****/
                    'h1_title' => Ban::filterWord($oPost->title),
                    'categories' => $this->oNoteModel->getCategory($oPost->noteId, 0,300),

                    /** Date **/
                    'dateTime' => $this->dateTime,
                    'post' => $oPost
                ];
                $this->view->assigns($aVars);

                // Set Notes Post Views Statistics
                Framework\Analytics\Statistic::setView($oPost->noteId, 'Notes');
            }
            else
            {
                $this->sTitle = t('Note Not Found');
                $this->notFound();
            }
        }
        else
        {
            Header::redirect(Uri::get('note', 'main', 'index'));
        }

        $this->output();
    }

    public function category()
    {
        $sCategory = str_replace('-', ' ', $this->httpRequest->get('name'));
        $sOrder = $this->httpRequest->get('order');
        $sSort = $this->httpRequest->get('sort');

        $this->iTotalNotes = $this->oNoteModel->category($sCategory, true, $sOrder, $sSort, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalNotes, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->category($sCategory, false, $sOrder, $sSort, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->setMenuVars();

        $sCategoryTxt = substr($sCategory,0,60);
        if(empty($oSearch))
        {
            $this->sTitle = t('Not found "%0%" category!', $sCategoryTxt);
            $this->notFound();
        }
        else
        {
            $this->sTitle = t('Search by Category: "%0%" Note', $sCategoryTxt);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Result!', '%n% Notes Result!', $this->iTotalNotes);
            $this->view->meta_description = t('Search Note Post by Category %0% - Dating Social Community Note', $sCategoryTxt);
            $this->view->meta_keywords = t('search,post,blog,note,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function author()
    {
        $sAuthor = $this->httpRequest->get('author');
        $sOrder = $this->httpRequest->get('order');
        $sSort = $this->httpRequest->get('sort');

        $this->iTotalNotes = $this->oNoteModel->author($sAuthor, true, $sOrder, $sSort, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalNotes, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->author($sAuthor, false, $sOrder, $sSort, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->setMenuVars();

        $sAuthorTxt = substr($sAuthor,0,60);
        if(empty($oSearch))
        {
            $this->sTitle = t('Not found "%0%" author!', $sAuthorTxt);
            $this->notFound(false); // For the Ajax profile blocks, we can not put HTTP error code 404, so the attribute is "false"
            $this->view->error = t('No found the note post of %0%.', $sAuthor); // We change the error message
        }
        else
        {
            $this->sTitle = t('Search by Author: "%0%" Note', $sAuthorTxt);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Result!', '%n% Notes Result!', $this->iTotalNotes);
            $this->view->meta_description = t('Search Note Post by Author %0% - Dating Social Community Note', $sAuthorTxt);
            $this->view->meta_keywords = t('author,search,post,blog,note,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Search Note - Looking a post');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function result()
    {
        $this->iTotalNotes = $this->oNoteModel->search($this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null, $this->iApproved);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalNotes, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->search($this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), $this->iApproved);
        $this->setMenuVars();

        if(empty($oSearch))
        {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->notFound();
        }
        else
        {
            $this->sTitle = t('Dating Social Note - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Result!', '%n% Notes Result!', $this->iTotalNotes);
            $this->view->meta_description = t('Search - Dating Social Community Note');
            $this->view->meta_keywords = t('search,note,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add a Note');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit the Note');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function delete()
    {
        $iId = $this->httpRequest->post('id');
        $iProfileId = $this->session->get('member_id');

        CommentCoreModel::deleteRecipient($iId, 'Note');
        $this->oNoteModel->deleteCategory($iId);

        $this->_deleteThumbFile($iId, $iProfileId);
        $this->oNoteModel->deletePost($iId, $iProfileId);

        /* Clean NoteModel Cache */
        (new Framework\Cache\Cache)->start(NoteModel::CACHE_GROUP, null, null)->clear();
        Header::redirect(Uri::get('note', 'main', 'index'), t('Your post was deleted!'));
    }

    public function removeThumb($iId)
    {
        if(!(new Framework\Security\CSRF\Token)->checkUrl())
            exit(Form::errorTokenMsg());

        $iProfileId = $this->session->get('member_id');

        $this->_deleteThumbFile($iId, $iProfileId);
        $this->oNoteModel->deleteThumb($iId, $iProfileId);

        /* Clean BlogModel Cache */
        (new Framework\Cache\Cache)->start(NoteModel::CACHE_GROUP, null, null)->clear();

        Header::redirect(Uri::get('note','main','edit', $iId), t('The thumbnail has been deleted successfully!'));
    }

    /**
     * Sets the Menu Variables for the template.
     *
     * @access protected
     * @return void
     */
    protected function setMenuVars()
    {
        $this->view->top_views = $this->oNoteModel->getPosts(0, 5, SearchCoreModel::VIEWS, $this->iApproved);
        $this->view->top_rating = $this->oNoteModel->getPosts(0, 5, SearchCoreModel::RATING, $this->iApproved);
        $this->view->authors = $this->oNoteModel->getAuthor(0, 5, true);
        $this->view->categories = $this->oNoteModel->getCategory(null, 0, 50, true);
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
        if($b404Status) Framework\Http\Http::setHeadersByCode(404);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = t('Sorry, we weren\'t able to find the page you requested.<br />
                May we suggest <a href="%0%">exploring some tags</a> or <a href="%1%">make a new search</a>.', Uri::get('note','main','index'), Uri::get('note','main','search'));
    }

    /**
     * @internal Warning! Thumbnail must be removed before the note post in the database.
     * @param integer $iId
     * @param integer $iProfileId
     * @return boolean
     */
    private function _deleteThumbFile($iId, $iProfileId)
    {
        $oFile = $this->oNoteModel->readPost($this->oNoteModel->getPostId($iId), $iProfileId, null);
        return (new Note)->deleteThumb($this->session->get('member_username') . PH7_DS . $oFile->thumb, 'note', $this->file);
    }

    public function __destruct()
    {
        unset($this->oNoteModel, $this->oPage, $this->sTitle, $this->iTotalNotes, $this->iApproved);
    }

}
