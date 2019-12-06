<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;
use stdClass;
use Teapot\StatusCode;

class MainController extends Controller
{
    const POSTS_PER_PAGE = 10;
    const CATEGORIES_PER_PAGE = 10;
    const AUTHORS_PER_PAGE = 10;
    const ITEMS_MENU_TOP_VIEWS = 5;
    const ITEMS_MENU_TOP_RATING = 5;
    const ITEMS_MENU_AUTHORS = 6;
    const ITEMS_MENU_CATEGORIES = 10;
    const MAX_CATEGORIES = 300;

    const MAX_CATEGORY_LENGTH_SHOWN = 60;
    const MAX_AUTHOR_LENGTH_SHOWN = 60;

    /** @var NoteModel */
    protected $oNoteModel;

    /** @var Page */
    protected $oPage;

    /** @var string */
    protected $sTitle;

    /** @var int */
    protected $iTotalNotes;

    /** @var int|null */
    protected $iApproved;

    public function __construct()
    {
        parent::__construct();

        $this->oNoteModel = new NoteModel;
        $this->oPage = new Page;
        $this->iApproved = AdminCore::auth() && !UserCore::isAdminLoggedAs() ? null : 1;

        $this->view->member_id = $this->session->get('member_id');
    }

    public function index()
    {
        $this->view->page_title = t('Note Posts of %site_name% community');
        $this->view->h1_title = '<span class="cinnabar-red">' . t('Community Blog. Let Users talk!') . '</span>';

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oNoteModel->totalPosts($this->iApproved), self::POSTS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();
        $oPosts = $this->oNoteModel->getPosts(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage(),
            SearchCoreModel::CREATED,
            $this->iApproved
        );

        $this->setMenuVars();

        if (empty($oPosts)) {
            $this->sTitle = t('No notes found.');
            $this->notFound(false); // We disable the HTTP error code 404 for Ajax requests running
            $this->view->error = t('Oops! There are no notes at the moment. Please come back soon 😉'); // Amend the error message
        } else {
            $this->view->posts = $oPosts;
        }

        $this->output();
    }

    public function read($sUsername, $sPostId)
    {
        if (isset($sUsername, $sPostId)) {
            $iProfileId = (new UserCoreModel)->getId(null, $sUsername);
            $oPost = $this->oNoteModel->readPost($sPostId, $iProfileId, $this->iApproved);

            if ($oPost && $this->doesPostExist($sPostId, $oPost)) {
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
                    'categories' => $this->oNoteModel->getCategory($oPost->noteId, 0, self::MAX_CATEGORIES),

                    /** Date **/
                    'dateTime' => $this->dateTime,
                    'post' => $oPost
                ];
                $this->view->assigns($aVars);

                // Set Notes Post Views Statistics
                Statistic::setView($oPost->noteId, DbTableName::NOTE);
            } else {
                $this->sTitle = t('Note not found.');
                $this->notFound();
            }
        } else {
            Header::redirect(
                Uri::get('note', 'main', 'index')
            );
        }

        $this->output();
    }

    public function category()
    {
        $sCategory = str_replace('-', ' ', $this->httpRequest->get('name'));
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalNotes = $this->oNoteModel->category(
            $sCategory,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalNotes, self::CATEGORIES_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->category(
            $sCategory,
            false,
            $sOrder,
            $iSort,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->setMenuVars();

        $sCategoryTxt = substr($sCategory, 0, self::MAX_CATEGORY_LENGTH_SHOWN);
        if (empty($oSearch)) {
            $this->sTitle = t('Not "%0%" category found!', $sCategoryTxt);
            $this->notFound();
        } else {
            $this->sTitle = t('Search by Category: "%0%" Note', $sCategoryTxt);
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Found!', '%n% Notes Found!', $this->iTotalNotes);
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
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalNotes = $this->oNoteModel->author(
            $sAuthor,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalNotes, self::AUTHORS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->author(
            $sAuthor,
            false,
            $sOrder,
            $iSort,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->setMenuVars();

        $sAuthorTxt = substr($sAuthor, 0, self::MAX_AUTHOR_LENGTH_SHOWN);
        if (empty($oSearch)) {
            $this->sTitle = t('None "%0%" author was found!', $sAuthorTxt);
            $this->notFound(false); // For the Ajax profile blocks, we can not put HTTP error code 404, so the attribute is "false"
            $this->view->error = t("No %0%'s posts found.", $sAuthor); // Amend the error message
        } else {
            $this->sTitle = t('Search by Author: "%0%" Note', $sAuthorTxt);
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Found!', '%n% Notes Found!', $this->iTotalNotes);
            $this->view->meta_description = t('Search Note Post by Author %0% - Dating Social Community Note', $sAuthorTxt);
            $this->view->meta_keywords = t('author,search,post,blog,note,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function search()
    {
        $this->view->page_title = $this->view->h2_title = t('Note Search - Looking for a post');
        $this->output();
    }

    public function result()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalNotes = $this->oNoteModel->search(
            $sKeywords,
            true,
            $sOrder,
            $iSort,
            null,
            null,
            $this->iApproved
        );

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalNotes, self::POSTS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oNoteModel->search(
            $sKeywords,
            false,
            $sOrder,
            $iSort,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage(),
            $this->iApproved
        );

        $this->setMenuVars();

        if (empty($oSearch)) {
            $this->sTitle = t('Sorry, your search returned no results!');
            $this->notFound();
        } else {
            $this->sTitle = t('Dating Social Note - Your search returned');
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Note Found!', '%n% Notes Found!', $this->iTotalNotes);
            $this->view->meta_description = t('Search - Dating Social Community Note');
            $this->view->meta_keywords = t('search,note,dating,social network,community,news');

            $this->view->posts = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function add()
    {
        $this->view->page_title = $this->view->h1_title = t('Add a Note');
        $this->output();
    }

    public function edit()
    {
        $this->view->page_title = $this->view->h1_title = t('Edit the Note');
        $this->output();
    }

    public function delete()
    {
        $iId = $this->httpRequest->post('id');
        $iProfileId = $this->session->get('member_id');

        CommentCoreModel::deleteRecipient($iId, 'note');
        $this->oNoteModel->deleteCategory($iId);

        $this->deleteThumbFile($iId, $iProfileId);
        $this->oNoteModel->deletePost($iId, $iProfileId);

        Note::clearCache();
        Header::redirect(
            Uri::get('note', 'main', 'index'),
            t('Your post has been deleted!')
        );
    }

    public function removeThumb($iId)
    {
        if ((new SecurityToken)->checkUrl()) {
            $iProfileId = $this->session->get('member_id');
            $this->deleteThumbFile($iId, $iProfileId);
            $this->oNoteModel->deleteThumb($iId, $iProfileId);
            Note::clearCache();

            $sMsg = t('The thumbnail has been deleted successfully!');
            $sMsgType = Design::SUCCESS_TYPE;
        } else {
            $sMsg = Form::errorTokenMsg();
            $sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get('note', 'main', 'edit', $iId),
            $sMsg,
            $sMsgType
        );
    }

    /**
     * Sets the Menu Variables for the template.
     *
     * @return void
     */
    protected function setMenuVars()
    {
        $this->view->top_views = $this->oNoteModel->getPosts(
            0,
            self::ITEMS_MENU_TOP_VIEWS,
            SearchCoreModel::VIEWS,
            $this->iApproved
        );

        $this->view->top_rating = $this->oNoteModel->getPosts(
            0,
            self::ITEMS_MENU_TOP_RATING,
            SearchCoreModel::RATING,
            $this->iApproved
        );

        $this->view->authors = $this->getAuthorList();


        $this->view->categories = $this->getCategoryList();
    }

    /**
     * Set a custom Not Found Error Message with HTTP 404 Code Status.
     *
     * @param bool $b404Status For the Ajax blocks and others, we can not put HTTP error code 404, so the attribute must be set to FALSE
     *
     * @return void
     */
    protected function notFound($b404Status = true)
    {
        if ($b404Status) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $this->view->error = t("Sorry, we weren't able to find the page you requested.") . '<br />' .
            t('You can go back on the <a href="%0%">note homepage</a> or <a href="%1%">search with different keywords</a>.',
                Uri::get('note', 'main', 'index'), Uri::get('note', 'main', 'search')
            );
    }

    /**
     * @return array
     */
    private function getCategoryList()
    {
        $oCache = (new Cache)->start(NoteModel::CACHE_GROUP, 'categorylist', NoteModel::CACHE_LIFETIME);

        if (!$aCategories = $oCache->get()) {
            $aCategoryList = $this->oNoteModel->getCategory(null, 0, self::MAX_CATEGORIES);

            $aCategories = [];
            foreach ($aCategoryList as $oCategory) {
                $iTotalPostsPerCat = $this->oNoteModel->category(
                    $oCategory->name,
                    true,
                    SearchCoreModel::TITLE,
                    SearchCoreModel::ASC,
                    0,
                    self::MAX_CATEGORIES
                );

                if ($iTotalPostsPerCat > 0 && count($aCategories) <= self::ITEMS_MENU_CATEGORIES) {
                    $oData = new stdClass();
                    $oData->totalNotes = $iTotalPostsPerCat;
                    $oData->name = $oCategory->name;
                    $aCategories[] = $oData;
                }
            }
            $oCache->put($aCategories);
        }
        unset($oCache);

        return $aCategories;
    }

    /**
     * @return array
     */
    private function getAuthorList()
    {
        $oCache = (new Cache)->start(NoteModel::CACHE_GROUP, 'authorlist', NoteModel::CACHE_LIFETIME);

        if (!$aAuthors = $oCache->get()) {
            $aAuthorList = $this->oNoteModel->getAuthor(0, self::ITEMS_MENU_AUTHORS);

            $aAuthors = [];
            foreach ($aAuthorList as $oAuthor) {
                $iTotalPostsPerAuthor = $this->oNoteModel->author(
                    $oAuthor->username,
                    true,
                    SearchCoreModel::TITLE,
                    SearchCoreModel::ASC,
                    0,
                    self::ITEMS_MENU_AUTHORS
                );

                if ($iTotalPostsPerAuthor > 0) {
                    $oData = new stdClass();
                    $oData->totalNotes = $iTotalPostsPerAuthor;
                    $oData->username = $oAuthor->username;
                    $aAuthors[] = $oData;
                }
            }
            $oCache->put($aAuthors);
        }
        unset($oCache);

        return $aAuthors;
    }

    /**
     * @internal Warning! Thumbnail must be removed before the note post in the database.
     *
     * @param int $iId
     * @param int $iProfileId
     *
     * @return bool
     */
    private function deleteThumbFile($iId, $iProfileId)
    {
        $oFile = $this->oNoteModel->readPost(
            $this->oNoteModel->getPostId($iId),
            $iProfileId,
            null
        );

        return (new Note)->deleteThumb(
            $this->session->get('member_username') . PH7_DS . $oFile->thumb,
            'note',
            $this->file
        );
    }

    /**
     * @param string $sPostId
     * @param stdClass $oPost
     *
     * @return bool
     */
    private function doesPostExist($sPostId, stdClass $oPost)
    {
        return !empty($oPost->postId) && $this->str->equals($sPostId, $oPost->postId);
    }
}
