<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Url\Header;
use stdClass;
use Teapot\StatusCode;

class MainController extends Controller
{
    use ImageTaggable;

    const ALBUMS_PER_PAGE = 16;
    const PHOTOS_PER_PAGE = 10;

    /** @var PictureModel */
    private $oPictureModel;

    /** @var Page */
    private $oPage;

    /** @var string */
    private $sUsername;

    /** @var bool|int */
    private $iProfileId;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iTotalPictures;

    public function __construct()
    {
        parent::__construct();

        $this->oPictureModel = new PictureModel;
        $this->oPage = new Page;

        $this->sUsername = $this->httpRequest->get('username');

        $this->view->member_id = $this->session->get('member_id');
        $this->iProfileId = (new UserCoreModel)->getId(null, $this->sUsername);

        // Predefined meta_keywords tags
        $this->view->meta_keywords = t('picture,photo,pictures,photos,album,albums,photo album,picture album,gallery,picture dating');
    }

    public function index()
    {
        // Header::redirect(Uri::get('picture','main','albums'));
        $this->albums();
    }

    public function addAlbum()
    {
        $this->view->page_title = $this->view->h2_title = t('Add a new Album');
        $this->output();
    }

    public function addPhoto()
    {
        $this->view->page_title = $this->view->h2_title = t('Add some new Photos');
        $this->output();
    }

    public function editAlbum()
    {
        $this->view->page_title = $this->view->h2_title = t('Edit Album');
        $this->output();
    }

    public function editPhoto()
    {
        $this->view->page_title = $this->view->h2_title = t('Edit Photo');
        $this->output();
    }

    public function albums()
    {
        $iProfileId = $this->httpRequest->getExists('username') ? $this->iProfileId : null;
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oPictureModel->totalAlbums($iProfileId), self::ALBUMS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oAlbums = $this->oPictureModel->album(
            $iProfileId,
            null,
            '1',
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->view->is_add_album_btn_shown = $this->httpRequest->get('show_add_album_btn', 'bool');

        if (empty($oAlbums)) {
            $this->sTitle = t('Oops! 😵 Nothing here for the moment.');
            $this->notFound(false); // Because the Ajax blocks profile, we cannot put HTTP error code 404, so the attribute is FALSE
        } else {
            // We can include HTML tags in the title since the template will erase them before displaying
            $this->sTitle = !empty($iProfileId) ? t("The %0%'s photo album", $this->design->getProfileLink($this->sUsername, false)) : t('Photo Gallery Community');
            $this->view->page_title = $this->view->h2_title = $this->sTitle;
            $this->view->meta_description = t("%0%'s Albums | Photo Albums of the Dating Social Community - %site_name%", $this->sUsername);

            $this->view->albums = $oAlbums;
        }

        if (empty($iProfileId)) {
            $this->manualTplInclude('index.tpl');
        }

        $this->output();
    }

    public function album()
    {
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oPictureModel->totalPhotos($this->iProfileId), self::ALBUMS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oAlbum = $this->oPictureModel->photo(
            $this->iProfileId,
            $this->httpRequest->get('album_id', 'int'),
            null,
            1,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oAlbum)) {
            $this->sTitle = t('Album not found or still in pending approval.');
            $this->notFound();
        } else {
            $this->sTitle = t("%0%'s photo album", $this->design->getProfileLink($this->sUsername, false));
            $this->view->page_title = $this->view->h2_title = $this->sTitle; // We can include HTML tags in the title since the template will erase them before displaying
            $this->view->meta_description = t("Browse %0%'s Photos | Photo Album Social Community - %site_name%", $this->sUsername);
            $this->view->album = $oAlbum;

            /**
             * @internal FYI, we don't call `Statistic::setView()`, because it needs a foreach loop,
             * and it is unnecessary to do both, that's why it is located in the album.tpl view instead.
             */
        }

        $this->output();
    }

    public function photo()
    {
        $oPicture = $this->oPictureModel->photo(
            $this->iProfileId,
            $this->httpRequest->get('album_id', 'int'),
            $this->httpRequest->get('picture_id', 'int'),
            1,
            0,
            1
        );

        if (empty($oPicture)) {
            $this->sTitle = t('Photo not found or still in pending approval.');
            $this->notFound();
        } else {
            $this->sTitle = t("%0%'s photo", $this->design->getProfileLink($this->sUsername, false));

            $sTitle = Ban::filterWord($oPicture->title, false);
            $this->view->page_title = t("%0%'s photo, %1%", $oPicture->firstName, $sTitle);
            $this->view->meta_description = t("%0%'s photo, %1%, %2%", $oPicture->firstName, $sTitle, substr(Ban::filterWord($oPicture->description, false), 0, 100));
            $this->view->meta_keywords = t('picture,photo,pictures,photos,album,albums,photo album,picture album,gallery,%0%,%1%,%2%', str_replace(' ', ',', $sTitle), $oPicture->firstName, $oPicture->username);
            $this->view->h1_title = $this->sTitle;
            $this->view->picture = $oPicture;
            $this->imageToSocialMetaTags($oPicture);

            Statistic::setView($oPicture->pictureId, DbTableName::PICTURE);
        }

        $this->output();
    }

    public function deletePhoto()
    {
        $iPictureId = $this->httpRequest->post('picture_id', 'int');

        CommentCoreModel::deleteRecipient($iPictureId, 'picture');

        $this->oPictureModel->deletePhoto(
            $this->session->get('member_id'),
            $this->httpRequest->post('album_id', 'int'),
            $iPictureId
        );

        (new Picture)->deletePhoto(
            $this->httpRequest->post('album_id'),
            $this->session->get('member_username'),
            $this->httpRequest->post('picture_link')
        );

        Picture::clearCache();

        Header::redirect(
            Uri::get(
                'picture',
                'main',
                'album',
                $this->session->get('member_username') . ',' . $this->httpRequest->post('album_title') . ',' . $this->httpRequest->post('album_id')
            ),
            t('Your photo has been removed.')
        );
    }

    public function deleteAlbum()
    {
        $this->oPictureModel->deletePhoto($this->session->get('member_id'), $this->httpRequest->post('album_id', 'int'));
        $this->oPictureModel->deleteAlbum($this->session->get('member_id'), $this->httpRequest->post('album_id', 'int'));
        $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->session->get('member_username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
        $this->file->deleteDir($sDir);

        Picture::clearCache();

        Header::redirect(
            Uri::get(
                'picture',
                'main',
                'albums'
            ),
            t('Your album has been removed.')
        );
    }

    public function search()
    {
        $this->view->page_title = $this->view->h2_title = t('Photo Search - Looking for a photo');
        $this->output();
    }

    public function result()
    {
        $this->iTotalPictures = $this->oPictureModel->search(
            $this->httpRequest->get('looking'),
            true,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalPictures, self::PHOTOS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oSearch = $this->oPictureModel->search(
            $this->httpRequest->get('looking'),
            false,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oSearch)) {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->notFound();
        } else {
            $this->sTitle = t('Dating Social Picture - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h3_title = nt('%n% photo found!', '%n% photos found!', $this->iTotalPictures);
            $this->view->meta_description = t('Search - %site_name% is a Dating Social Photo Community!');
            $this->view->meta_keywords = t('search,picture,photo, photo gallery,dating,social network,community,music,movie,news,picture sharing');
            $this->view->h2_title = $this->sTitle;
            $this->view->album = $oSearch;
        }

        $this->manualTplInclude('album.tpl');
        $this->output();
    }

    protected function imageToSocialMetaTags(stdClass $oPicture)
    {
        $sFilename = str_replace('original', '600', $oPicture->file);
        $sImageUrl = PH7_URL_DATA_SYS_MOD . 'picture/img/' . $oPicture->username . '/' . $oPicture->albumId . '/' . $sFilename;
        $this->view->image_social_meta_tag = $sImageUrl;
    }

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @param bool $b404Status For the Ajax blocks profile, we can not put HTTP error code 404, so the attribute must be set to "false". Default TRUE
     *
     * @return void
     */
    private function notFound($b404Status = true)
    {
        if ($b404Status === true) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        $sErrMsg = '';
        if ($b404Status === true) {
            $sErrMsg = '<br />' . t('Please return to <a href="%0%">the previous page</a> or <a href="%1%">add new photos</a> in this album.', 'javascript:history.back();', Uri::get('picture', 'main', 'addphoto', $this->httpRequest->get('album_id')));
        }

        $this->view->page_title = $this->view->h2_title = $this->sTitle;
        $this->view->error = $this->sTitle . $sErrMsg;
    }
}
