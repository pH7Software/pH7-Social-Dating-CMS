<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Controller
 */
namespace PH7;
use PH7\Framework\Security\Ban\Ban, PH7\Framework\Navigation\Page;

class MainController extends Controller
{

    private $oPictureModel, $oPage, $sUsername, $sUsernameLink, $iProfileId, $sTitle, $iTotalPictures;

    public function __construct()
    {
        parent::__construct();
        $this->oPictureModel = new PictureModel;
        $this->oPage = new Page;

        $this->sUsername = $this->httpRequest->get('username');

        $oUser = new UserCore;
        $this->sUsernameLink = $oUser->getProfileLink($this->sUsername);
        $this->view->oUser = $oUser;
        unset($oUser);

        $this->view->member_id = $this->session->get('member_id');
        $this->iProfileId = (new UserCoreModel)->getId(null, $this->sUsername);

        // Predefined meta_keywords tags
        $this->view->meta_keywords = t('picture,photo,pictures,photos,album,albums,picture album,photo album,gallery,picture dating');
    }

    public function index()
    {
        // Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('picture','main','albums'));
        $this->albums();
    }

    public function addAlbum()
    {
        $this->sTitle = t('Add a new Album');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function addPhoto()
    {
        $this->sTitle = t('Add some new Pictures');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function editAlbum()
    {
        $this->sTitle = t('Edit Album');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function editPhoto()
    {
        $this->sTitle = t('Edit Picture');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function albums()
    {
        $this->view->meta_description = t('%0%\'s Albums | Picture Albums of the Dating Social Community - %site_name%', $this->str->upperFirst($this->sUsername));
        $profileId = ($this->httpRequest->getExists('username')) ? $this->iProfileId : null;
        $this->view->total_pages = $this->oPage->getTotalPages($this->oPictureModel->totalAlbums($profileId), 16);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oAlbums = $this->oPictureModel->album($profileId, null, 1, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if (empty($oAlbums))
        {
            $this->sTitle = t('Empty Photo Album.');
            $this->_notFound(false); // Because the Ajax blocks profile, we cannot put HTTP error code 404, so the attribute is FALSE
        }
        else
        {
            // We can include HTML tags in the title since the template will erase them before display.
            $this->sTitle = (!empty($profileId)) ? t('The Album of <a href="%0%">%1%</a>', $this->sUsernameLink, $this->str->upperFirst($this->sUsername)) : t('Photo Gallery Community');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->albums = $oAlbums;
        }
        if (empty($profileId))
            $this->manualTplInclude('index.tpl');

        $this->output();
    }

    public function album()
    {
        $this->view->total_pages = $this->oPage->getTotalPages($this->oPictureModel->totalPhotos($this->iProfileId), 26);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oAlbum = $this->oPictureModel->photo($this->iProfileId, $this->httpRequest->get('album_id', 'int'), null, 1, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if (empty($oAlbum))
        {
            $this->sTitle = t('Album not found or in pending approval.');
            $this->_notFound();
        }
        else
        {
            $this->sTitle = t('Album of <a href="%0%">%1%</a>', $this->sUsernameLink, $this->str->upperFirst($this->sUsername));
            $this->view->page_title = t('Album of %0%', $this->str->upperFirst($this->sUsername));
            $this->view->meta_description = t('Browse Photos From %0% | Picture Album Social Community - %site_name%', $this->str->upperFirst($this->sUsername));
            $this->view->h2_title = $this->sTitle;
            $this->view->album = $oAlbum;

            // Set Picture Album Statistics since it needs the foreach loop and it is unnecessary to do both, we have placed in the file album.tpl
        }

        $this->output();
    }

    public function photo()
    {
        $oPicture = $this->oPictureModel->photo($this->iProfileId, $this->httpRequest->get('album_id', 'int'), $this->httpRequest->get('picture_id', 'int'), 1, 0, 1);

        if (empty($oPicture))
        {
            $this->sTitle = t('Photo not found or in pending approval.');
            $this->_notFound();
        }
        else
        {
            $this->sTitle = t('Photo of <a href="%0%">%1%</a>', $this->sUsernameLink, $this->str->upperFirst($this->sUsername));

            $sTitle = Ban::filterWord($oPicture->title, false);
            $this->view->page_title = t('Photo of %0%, %1%', $oPicture->firstName, $sTitle);
            $this->view->meta_description = t('Photo of %0%, %1%, %2%', $oPicture->firstName, $sTitle, substr(Ban::filterWord($oPicture->description, false), 0, 100));
            $this->view->meta_keywords = t('picture,photo,pictures,photos,album,albums,picture album,photo album,gallery,%0%,%1%,%2%', str_replace(' ', ',', $sTitle), $oPicture->firstName, $oPicture->username);
            $this->view->h1_title = $this->sTitle;
            $this->view->picture = $oPicture;

            //Set Photo Statistics
            Framework\Analytics\Statistic::setView($oPicture->pictureId, 'Pictures');
        }

        $this->output();
    }

    public function deletePhoto()
    {
        $iPictureId = $this->httpRequest->post('picture_id', 'int');
        CommentCoreModel::deleteRecipient($iPictureId, 'Picture');
        $this->oPictureModel->deletePhoto($this->session->get('member_id'), $this->httpRequest->post('album_id', 'int'), $iPictureId);
        (new Picture)->deletePhoto($this->httpRequest->post('album_id'), $this->session->get('member_username'), $this->httpRequest->post('picture_link'));

        /* Clean PictureModel Cache */
        (new Framework\Cache\Cache)->start(PictureModel::CACHE_GROUP, null, null)->clear();
        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('picture', 'main', 'album', $this->session->get('member_username') . ',' . $this->httpRequest->post('album_title') . ',' . $this->httpRequest->post('album_id')), t('Your picture has been deleted!'));
    }

    public function deleteAlbum()
    {
        $this->oPictureModel->deletePhoto($this->session->get('member_id'), $this->httpRequest->post('album_id', 'int'));
        $this->oPictureModel->deleteAlbum($this->session->get('member_id'), $this->httpRequest->post('album_id', 'int'));
        $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->session->get('member_username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
        $this->file->deleteDir($sDir);

        /* Clean PictureModel Cache */
        (new Framework\Cache\Cache)->start(PictureModel::CACHE_GROUP, null, null)->clear();

        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('picture', 'main', 'albums'), t('Your album has been deleted!'));
    }

    public function search()
    {
        $this->sTitle = t('Search Picture - Looking a picture');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function result()
    {
        $this->iTotalPictures = $this->oPictureModel->search($this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalPictures, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oSearch = $this->oPictureModel->search($this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if (empty($oSearch))
        {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->_notFound();
        }
        else
        {
            $this->sTitle = t('Dating Social Picture - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Picture Result!', '%n% Pictures Result!', $this->iTotalPictures);
            $this->view->meta_description = t('Search - %site_name% is a Dating Social Photo Community!');
            $this->view->meta_keywords = t('search,picture,photo, photo gallery,dating,social network,community,music,movie,news,picture sharing');
            $this->view->h2_title = $this->sTitle;
            $this->view->album = $oSearch;
        }

        $this->manualTplInclude('album.tpl');
        $this->output();
    }

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @access private
     * @param boolean $b404Status For the Ajax blocks profile, we can not put HTTP error code 404, so the attribute must be set to "false". Default TRUE
     * @return void
     */
    private function _notFound($b404Status = true)
    {
        if ($b404Status === true)
            Framework\Http\Http::setHeadersByCode(404);
        $sErrMsg = ($b404Status === true) ? '<br />' . t('Please return to <a href="%1%">go the previous page</a> or <a href="%1%">add a new picture</a> in this album.', 'javascript:history.back();', Framework\Mvc\Router\Uri::get('picture', 'main', 'addphoto', $this->httpRequest->get('album_id'))) : '';

        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = $this->sTitle . $sErrMsg;
    }

    public function __destruct()
    {
        // Destruction
        unset($this->oPictureModel, $this->oPage, $this->sUsername, $this->sUsernameLink, $this->iProfileId, $this->sTitle, $this->iTotalPictures);
    }

}
