<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */
namespace PH7;

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri, PH7\Framework\Navigation\Page;

class ModeratorController extends Controller
{

    private $oModeratorModel, $sPage, $sMsg;

    public function __construct()
    {
        parent::__construct();
        $this->oPage = new Page();
        $this->oModeratorModel = new ModeratorModel;
        $this->view->oUser = new UserCore;
    }

    public function index()
    {
        $this->view->page_title = t('Moderation Panel');
        $this->view->h2_title = t('Moderation Panel');
        $this->output();
    }

    public function albumPicture()
    {
        $this->view->page_title = t('Albums Picture Moderation');
        $this->view->h2_title = t('Albums Picture Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->totalAlbumsPicture(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->albums = $this->oModeratorModel->getAlbumsPicture($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->output();
    }

    public function picture()
    {
        $this->view->page_title = t('Pictures Moderation');
        $this->view->h2_title = t('Pictures Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->totalPictures(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->pictures = $this->oModeratorModel->getPictures($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->output();
    }

    public function albumVideo()
    {
        $this->view->page_title = t('Albums Video Moderation');
        $this->view->h2_title = t('Albums Video Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->totalAlbumsVideo(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->albums = $this->oModeratorModel->getAlbumsVideo($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->output();
    }

    public function video()
    {
        $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . 'video/' . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'common.css');

        $this->view->page_title = t('Videos Moderation');
        $this->view->h2_title = t('Videos Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->totalVideos(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->videos = $this->oModeratorModel->getVideos($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->output();
    }

    public function avatar()
    {
        $this->view->page_title = t('Avatar Moderation');
        $this->view->h2_title = t('Avatar Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->
            totalAvatars(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->avatars = $this->oModeratorModel->getAvatars($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->view->avatarDesign = new AvatarDesignCore(); // Avatar Design Class
        $this->output();
    }

    public function background()
    {
        $this->view->page_title = t('Profile Background Moderation');
        $this->view->h2_title = t('Profile Background Moderation');
        $this->view->total_pages = $this->oPage->getTotalPages($this->oModeratorModel->
            totalBackgrounds(), 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $this->view->backgrounds = $this->oModeratorModel->getBackgrounds($this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());
        $this->output();
    }

    public function pictureWebcam()
    {
        Header::redirect(Uri::get('webcam', 'webcam', 'picture'), t('Welcome to the Picture Webcam in "administrator mode"'));
    }

    public function approvedAlbumPicture()
    {
        if ($this->oModeratorModel->approvedAlbumPicture($this->httpRequest->post('album_id')))
        {
            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture album has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture album could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumpicture'), $this->sMsg);
    }

    public function approvedPhoto()
    {
        if ($this->oModeratorModel->approvedPicture($this->httpRequest->post('picture_id')))
        {
            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'picture'), $this->sMsg);
    }

    public function approvedAlbumVideo()
    {
        if ($this->oModeratorModel->approvedAlbumVideo($this->httpRequest->post('album_id')))
        {
            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->
                clear();

            $this->sMsg = t('The video album has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The video album could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumvideo'), $this->sMsg);
    }

    public function approvedVideo()
    {
        if ($this->oModeratorModel->approvedVideo($this->httpRequest->post('video_id')))
        {
            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The video has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The video could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'video'), $this->sMsg);
    }

    public function approvedAvatar()
    {
        if ($this->oModeratorModel->approvedAvatar($this->httpRequest->post('id')))
        {
            /* Clean User Avatar Cache */
            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'avatar' . $this->httpRequest->post('id'), null)->clear();

            $this->sMsg = t('The avatar has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The avatar could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'avatar'), $this->sMsg);
    }

    public function approvedBackground()
    {
        if ($this->oModeratorModel->approvedBackground($this->httpRequest->post('id')))
        {
            /* Clean User Background Cache */
            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'background' . $this->httpRequest->post('id'), null)->clear();

            $this->sMsg = t('The wallpaper has been approved!');
        }
        else
        {
            $this->sMsg = t('Oops! The wallpaper could not be approved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'background'), $this->sMsg);
    }

    public function disapprovedAlbumPicture()
    {
        if ($this->oModeratorModel->approvedAlbumPicture($this->httpRequest->post('album_id'), '0'))
        {
            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture album has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture album could not be disapproved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumpicture'), $this->sMsg);
    }

    public function disapprovedPhoto()
    {
        if ($this->oModeratorModel->approvedPicture($this->httpRequest->post('picture_id'), '0'))
        {
            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture could not be disapproved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'picture'), $this->sMsg);
    }

    public function disapprovedAlbumVideo()
    {
        if ($this->oModeratorModel->approvedAlbumVideo($this->httpRequest->post('album_id'), '0'))
        {
            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->
                clear();

            $this->sMsg = t('The video album has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The video album could not be disapproved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumvideo'), $this->sMsg);
    }

    public function disapprovedVideo()
    {
        if ($this->oModeratorModel->approvedVideo($this->httpRequest->post('video_id'), '0'))
        {
            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The video has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The video could not be disapproved!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'video'), $this->sMsg);
    }

    public function disapprovedAvatar()
    {
        if ($this->oModeratorModel->approvedAvatar($this->httpRequest->post('id'), '0'))
        {
            /* Clean User Avatar Cache */
            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'avatar' . $this->httpRequest->post('id'), null)->clear();

            $this->sMsg = t('The avatar has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The avatar could not be disapprove!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'avatar'), $this->sMsg);
    }

    public function disapprovedBackground()
    {
        if ($this->oModeratorModel->approvedBackground($this->httpRequest->post('id'), '0'))
        {
            /* Clean User Background Cache */
            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'background' . $this->httpRequest->post('id'), null)->clear();

            $this->sMsg = t('The wallpaper has been disapproved!');
        }
        else
        {
            $this->sMsg = t('Oops! The wallpaper could not be disapprove!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'background'), $this->sMsg);
    }

    public function deleteAlbumPicture()
    {
        if ($this->oModeratorModel->deleteAlbumPicture($this->httpRequest->post('album_id')) && (new PictureCoreModel)->deletePhoto($this->httpRequest->post('id'), $this->httpRequest->post('album_id')))
        {
            $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->httpRequest->post('username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
            $this->file->deleteDir($sDir);

            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture album has been deleted!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture album could not be deleted');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumpicture'), $this->sMsg);
    }

    public function deletePhoto()
    {
        $bPicture = (new PictureCoreModel)->deletePhoto($this->httpRequest->post('id'), $this->httpRequest->post('album_id'), $this->httpRequest->post('picture_id'));

        if ($bPicture)
        {
            (new PictureCore)->deletePhoto($this->httpRequest->post('album_id'), $this->httpRequest->post('username'), $this->httpRequest->post('picture_link'));

            /* Clean PictureCoreModel Cache */
            (new Framework\Cache\Cache)->start(PictureCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The picture has been deleted!');
        }
        else
        {
            $this->sMsg = t('Oops! The picture could not be deleted!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'picture'), $this->sMsg);
    }

    public function deleteAlbumVideo()
    {
        if ($this->oModeratorModel->deleteAlbumVideo($this->httpRequest->post('album_id')) && (new VideoCoreModel)->deleteVideo($this->httpRequest->post('id'), $this->httpRequest->post('album_id')))
        {
            $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $this->httpRequest->post('username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
            $this->file->deleteDir($sDir);

            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The video album has been deleted!');
        }
        else
        {
            $this->sMsg = t('Oops! The video album could not be deleted');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'albumvideo'), $this->sMsg);
    }

    public function deleteVideo()
    {
        $bVideo = (new VideoCoreModel)->deleteVideo($this->httpRequest->post('id'), $this->httpRequest->post('album_id'), $this->httpRequest->post('video_id'));

        if ($bVideo)
        {
            (new VideoCore)->deleteVideo($this->httpRequest->post('album_id'), $this->httpRequest->post('username'), $this->httpRequest->post('video_link'));

            /* Clean VideoCoreModel Cache */
            (new Framework\Cache\Cache)->start(VideoCoreModel::CACHE_GROUP, null, null)->clear();

            $this->sMsg = t('The video has been deleted!');
        }
        else
        {
            $this->sMsg = t('Oops! The video could not be deleted!');
        }

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'video'), $this->sMsg);
    }

    public function deleteAvatar()
    {
        (new Admin)->deleteAvatar($this->httpRequest->post('id'), $this->httpRequest->post('username'));

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'avatar'), $this->sMsg);
    }

    public function deleteBackground()
    {
        (new Admin)->deleteBackground($this->httpRequest->post('id'), $this->httpRequest->post('username'));

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'moderator', 'background'), $this->sMsg);
    }

    public function __destruct()
    {
        unset($this->oPage, $this->oModeratorModel, $this->sMsg);
    }

}
