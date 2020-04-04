<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\Statistic as StatModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use stdClass;
use Teapot\StatusCode;

class MainController extends Controller
{
    use ImageTaggable;

    const GAMES_PER_PAGE = 10;
    const CATEGORIES_PER_PAGE = 10;
    const ITEMS_MENU_TOP_VIEWS = 5;
    const ITEMS_MENU_TOP_RATING = 5;
    const ITEMS_MENU_LATEST = 5;
    const ITEMS_MENU_CATEGORIES = 10;

    const MAX_CATEGORY_LENGTH_SHOWN = 60;
    const MAX_TITLE_LENGTH_SHOWN = 100;

    /**
     * @internal Protected access because AdminController derived class uses these attributes
     */
    /** @var stdClass */
    protected $oGameModel;

    /** @var Page */
    protected $oPage;

    /** @var string */
    protected $sTitle;

    /** @var string */
    protected $sMetaKeywords;

    /** @var int */
    protected $iTotalGames;

    public function __construct()
    {
        parent::__construct();

        $this->oGameModel = new GameModel;
        $this->oPage = new Page;

        // Predefined meta keyword tags
        $this->sMetaKeywords = t('game,free,flash,game site,flash game,games,gaming,online game');
        $this->view->meta_keywords = $this->sMetaKeywords;
    }

    public function index()
    {
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oGameModel->totalGames(),
            self::GAMES_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oGames = $this->oGameModel->get(
            null,
            null,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->setMenuVars();

        if (empty($oGames)) {
            $this->sTitle = t('No Games Found!');
            $this->notFound();
        } else {
            $this->view->page_title = t('Games Zone - Free Games');
            $this->view->h1_title = t('Games Zone Party');
            $this->view->meta_description = t('Free Games for Gamers, Flash Games, Free Online Games');
            $this->view->h2_title = $this->sTitle;

            $this->view->games = $oGames;
        }

        $this->output();
    }

    public function game()
    {
        $oGame = $this->oGameModel->get(
            strstr($this->httpRequest->get('title'), '-', true),
            $this->httpRequest->get('id'),
            0,
            1
        );

        if (empty($oGame)) {
            $this->sTitle = t('No Games Found!');
            $this->notFound();
        } else {
            $this->sTitle = t('Game - %0%', substr($oGame->description, 0, self::MAX_TITLE_LENGTH_SHOWN));
            $this->view->page_title = t('%0% Games Zone - %1%', $oGame->name, $oGame->title);
            $this->view->h1_title = $oGame->title;
            $this->view->meta_description = t('Flash Game - %0%', $this->sTitle);
            $this->view->meta_keywords = $oGame->keywords . $this->sMetaKeywords;
            $this->view->h2_title = $this->sTitle;
            $this->view->downloads = $this->oGameModel->getDownloadStat($oGame->gameId);
            $this->view->views = StatModel::getView($oGame->gameId, DbTableName::GAME);
            $this->imageToSocialMetaTags($oGame);

            $this->view->game = $oGame;

            //Set Game Statistics
            StatModel::setView($oGame->gameId, DbTableName::GAME);
        }

        $this->output();
    }

    public function category()
    {
        $sCategory = str_replace('-', ' ', $this->httpRequest->get('name'));
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalGames = $this->oGameModel->category(
            $sCategory,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalGames, self::CATEGORIES_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oGameModel->category(
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
            $this->sTitle = t('No "%0%" category found.', $sCategoryTxt);
            $this->notFound();
        } else {
            $this->sTitle = t('Search by Category: "%0%" Game', $sCategoryTxt);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Game Found!', '%n% Games Found!', $this->iTotalGames);
            $this->view->meta_description = t('Search the Flash Game in the Category %0% - Community Dating Social Games', $sCategoryTxt);

            $this->view->games = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Search Game - Looking a new Game');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function result()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalGames = $this->oGameModel->search(
            $sKeywords,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalGames,
            self::GAMES_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oGameModel->search(
            $sKeywords,
            false,
            $sOrder,
            $iSort,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );
        $this->setMenuVars();

        if (empty($oSearch)) {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->notFound();
        } else {
            $this->sTitle = t('Game - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Game Found!', '%n% Games Found!', $this->iTotalGames);
            $this->view->meta_description = t('Search - Free Games for Gamers, Flash Games, Free Online Games');
            $this->view->meta_keywords = t('search,game,free,flash,game site,flash game,games,gaming,online game');

            $this->view->games = $oSearch;
        }

        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function download()
    {
        if ($this->httpRequest->getExists('id')) {
            $iId = $this->httpRequest->get('id');

            if (is_numeric($iId)) {
                $sFile = @$this->oGameModel->getFile($iId);
                $sPathFile = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/file/' . $sFile;

                if (!empty($sFile) && is_file($sPathFile)) {
                    $sFileName = basename($sFile);
                    $this->file->download($sPathFile, $sFileName);
                    $this->oGameModel->setDownloadStat($iId);
                    exit(0);
                }
            }
        }

        $this->sTitle = t('Wrong download ID specified!');
        $this->notFound();
        $this->manualTplInclude('game.tpl');
        $this->output();
    }

    protected function imageToSocialMetaTags(stdClass $oGame)
    {
        $sThumbnailUrl = PH7_URL_DATA_SYS_MOD . 'game/img/thumb/' . $oGame->thumb;
        $this->view->image_social_meta_tag = $sThumbnailUrl;
    }

    /**
     * Sets the Menu Variables for the template.
     *
     * @return void
     */
    protected function setMenuVars()
    {
        $this->view->top_views = $this->oGameModel->get(
            null,
            null,
            0,
            self::ITEMS_MENU_TOP_VIEWS,
            SearchCoreModel::VIEWS
        );
        $this->view->top_rating = $this->oGameModel->get(
            null,
            null,
            0,
            self::ITEMS_MENU_TOP_RATING,
            SearchCoreModel::RATING
        );
        $this->view->latest = $this->oGameModel->get(
            null,
            null,
            0,
            self::ITEMS_MENU_LATEST,
            SearchCoreModel::ADDED_DATE
        );
        $this->view->categories = $this->oGameModel->getCategory(
            null,
            0,
            self::ITEMS_MENU_CATEGORIES,
            true
        );
    }

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @return void
     *
     * @throws Framework\File\IOException
     * @throws Framework\Http\Exception
     */
    private function notFound()
    {
        Http::setHeadersByCode(StatusCode::NOT_FOUND);

        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = $this->sTitle . '<br />' . t('Please return to the <a href="%0%">main game page</a> or <a href="%1%">the previous page</a>.', Uri::get('game', 'main', 'index'), 'javascript:history.back();');
    }
}
