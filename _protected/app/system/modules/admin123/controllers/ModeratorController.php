<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Url\Header;

class ModeratorController extends Controller
{
    private const STR_APPROVE_STATUS = '1';
    private const STR_DISAPPROVE_STATUS = '0';
    private const INT_APPROVE_STATUS = 1;
    private const INT_DISAPPROVE_STATUS = 0;

    private const ITEMS_PER_PAGE = 20;

    private Page $oPage;

    private ModeratorModel $oModeratorModel;

    private UserNotifier $oUserNotifier;

    private string $sMsg;

    private string $sMsgType;

    public function __construct()
    {
        parent::__construct();

        $this->oPage = new Page;
        $this->oUserNotifier = new UserNotifier(new Mail, $this->view);
        $this->oModeratorModel = new ModeratorModel;
    }

    public function index(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Moderation Panel');

        $this->output();
    }

    public function pictureAlbum(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Photo Albums Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalPictureAlbums(),
            self::ITEMS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->albums = $this->oModeratorModel->getAlbumsPicture(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->output();
    }

    public function picture(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Pictures Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalPictures(),
            self::ITEMS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->pictures = $this->oModeratorModel->getPictures(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->output();
    }

    public function videoAlbum(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Video Albums Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalVideoAlbums(),
            self::ITEMS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->albums = $this->oModeratorModel->getAlbumsVideo(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->output();
    }

    public function video(): void
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . 'video/' . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'common.css'
        );

        $this->view->page_title = $this->view->h2_title = t('Videos Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalVideos(),
            self::ITEMS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->videos = $this->oModeratorModel->getVideos(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->output();
    }

    public function avatar(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Profile Photos Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalAvatars(),
            self::ITEMS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->avatars = $this->oModeratorModel->getAvatars(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        $this->output();
    }

    public function background(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Profile Backgrounds Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oModeratorModel->totalBackgrounds(),
            self::ITEMS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->backgrounds = $this->oModeratorModel->getBackgrounds(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->output();
    }

    public function approvedPictureAlbum(): void
    {
        if ($this->oModeratorModel->approvedPictureAlbum(
            $this->httpRequest->post('album_id'),
            self::STR_APPROVE_STATUS
        )) {
            PictureCore::clearCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The photo album has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The photo album could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picturealbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function approvedPhoto(): void
    {
        if ($this->oModeratorModel->approvedPicture(
            $this->httpRequest->post('picture_id'),
            self::STR_APPROVE_STATUS
        )) {
            PictureCore::clearCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The picture has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The picture could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picture'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function approvedVideoAlbum(): void
    {
        if ($this->oModeratorModel->approvedVideoAlbum(
            $this->httpRequest->post('album_id'),
            self::STR_APPROVE_STATUS
        )) {
            VideoCore::clearCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The video album has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video album could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'videoalbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function approvedVideo(): void
    {
        if ($this->oModeratorModel->approvedVideo(
            $this->httpRequest->post('video_id'),
            self::STR_APPROVE_STATUS
        )) {
            VideoCore::clearCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The video has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'video'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function approvedAvatar(): void
    {
        if ($this->oModeratorModel->approvedAvatar(
            $this->httpRequest->post('id'),
            self::INT_APPROVE_STATUS
        )) {
            $this->clearAvatarCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The profile photo has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The profile photo could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'avatar'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function approvedBackground(): void
    {
        if ($this->oModeratorModel->approvedBackground(
            $this->httpRequest->post('id'),
            self::INT_APPROVE_STATUS
        )) {
            $this->clearUserBgCache();
            $this->notifyUserForApprovedContent();

            $this->sMsg = t('The wallpaper has been approved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The wallpaper could not be approved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'background'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedPictureAlbum(): void
    {
        if ($this->oModeratorModel->approvedPictureAlbum(
            $this->httpRequest->post('album_id'),
            self::STR_DISAPPROVE_STATUS
        )) {
            PictureCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The photo album has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The photo album could not be disapproved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picturealbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedPhoto(): void
    {
        if ($this->oModeratorModel->approvedPicture(
            $this->httpRequest->post('picture_id'),
            self::STR_DISAPPROVE_STATUS
        )) {
            PictureCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The picture has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The picture could not be disapproved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picture'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedVideoAlbum(): void
    {
        if ($this->oModeratorModel->approvedVideoAlbum(
            $this->httpRequest->post('album_id'),
            self::STR_DISAPPROVE_STATUS
        )) {
            VideoCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The video album has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video album could not be disapproved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'videoalbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedVideo(): void
    {
        if ($this->oModeratorModel->approvedVideo(
            $this->httpRequest->post('video_id'),
            self::STR_DISAPPROVE_STATUS
        )) {
            VideoCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The video has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video could not be disapproved!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'video'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedAvatar(): void
    {
        if ($this->oModeratorModel->approvedAvatar(
            $this->httpRequest->post('id'),
            self::INT_DISAPPROVE_STATUS
        )) {
            $this->clearAvatarCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The profile photo has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The profile photo could not be disapprove!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'avatar'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function disapprovedBackground(): void
    {
        if ($this->oModeratorModel->approvedBackground(
            $this->httpRequest->post('id'),
            self::INT_DISAPPROVE_STATUS
        )) {
            $this->clearUserBgCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The wallpaper has been disapproved!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The wallpaper could not be disapprove!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'background'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function deletePictureAlbum(): void
    {
        if (
            (new PictureCoreModel)->deletePhoto($this->httpRequest->post('id'), $this->httpRequest->post('album_id')) &&
            $this->oModeratorModel->deletePictureAlbum($this->httpRequest->post('album_id'))
        ) {
            $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->httpRequest->post('username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
            $this->file->deleteDir($sDir);
            PictureCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The photo album has been deleted!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The photo album could not be deleted');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picturealbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function deletePhoto(): void
    {
        $bPicture = (new PictureCoreModel)->deletePhoto(
            $this->httpRequest->post('id'),
            $this->httpRequest->post('album_id'),
            $this->httpRequest->post('picture_id')
        );

        if ($bPicture) {
            (new PictureCore)->deletePhoto(
                $this->httpRequest->post('album_id'),
                $this->httpRequest->post('username'),
                $this->httpRequest->post('picture_link')
            );
            PictureCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The picture has been deleted!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The picture could not be deleted!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'picture'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function deleteVideoAlbum(): void
    {
        if (
            (new VideoCoreModel)->deleteVideo($this->httpRequest->post('id'), $this->httpRequest->post('album_id')) &&
            $this->oModeratorModel->deleteVideoAlbum($this->httpRequest->post('album_id'))
        ) {
            $sDir = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $this->httpRequest->post('username') . PH7_DS . $this->httpRequest->post('album_id') . PH7_DS;
            $this->file->deleteDir($sDir);
            VideoCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The video album has been deleted!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video album could not be deleted');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'videoalbum'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function deleteVideo(): void
    {
        $bVideo = (new VideoCoreModel)->deleteVideo(
            $this->httpRequest->post('id'),
            $this->httpRequest->post('album_id'),
            $this->httpRequest->post('video_id')
        );

        if ($bVideo) {
            (new VideoCore)->deleteVideo(
                $this->httpRequest->post('album_id'),
                $this->httpRequest->post('username'),
                $this->httpRequest->post('video_link')
            );
            VideoCore::clearCache();
            $this->notifyUserForDisapprovedContent();

            $this->sMsg = t('The video has been deleted!');
            $this->sMsgType = Design::SUCCESS_TYPE;
        } else {
            $this->sMsg = t('Oops! The video could not be deleted!');
            $this->sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'video'
            ),
            $this->sMsg,
            $this->sMsgType
        );
    }

    public function deleteAvatar(): void
    {
        (new Admin)->deleteAvatar(
            $this->httpRequest->post('id'),
            $this->httpRequest->post('username')
        );
        $this->notifyUserForDisapprovedContent();

        $this->sMsg = t('Profile photo removed.');

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'avatar'
            ),
            $this->sMsg
        );
    }

    public function deleteBackground(): void
    {
        (new Admin)->deleteBackground(
            $this->httpRequest->post('id'),
            $this->httpRequest->post('username')
        );
        $this->notifyUserForDisapprovedContent();

        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'moderator',
                'background'
            ),
            $this->sMsg
        );
    }

    private function notifyUserForApprovedContent(): void
    {
        try {
            $iProfileId = $this->httpRequest->post('id', 'int');
            $sUserEmail = $this->oModeratorModel->getEmail($iProfileId);

            $this->oUserNotifier
                ->setUserEmail($sUserEmail)
                ->approvedContent()
                ->send();
        } catch (InvalidEmailException $oExcept) {
            $this->design->setFlashMsg(
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }

    private function notifyUserForDisapprovedContent(): void
    {
        try {
            $iProfileId = $this->httpRequest->post('id', 'int');
            $sUserEmail = $this->oModeratorModel->getEmail($iProfileId);

            $this->oUserNotifier
                ->setUserEmail($sUserEmail)
                ->disapprovedContent()
                ->send();
        } catch (InvalidEmailException $oExcept) {
            $this->design->setFlashMsg(
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * Clear "Design Avatar" & "UserCoreModel Avatar" Cache
     */
    private function clearAvatarCache(): void
    {
        (new Cache)
            ->start(Design::CACHE_AVATAR_GROUP . $this->httpRequest->post('username'), null, null)->clear()
            ->start(UserCoreModel::CACHE_GROUP, 'avatar' . $this->httpRequest->post('id'), null)->clear();
    }

    /**
     * Clear UserCoreModel Background Cache
     */
    private function clearUserBgCache(): void
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            'background' . $this->httpRequest->post('id'),
            null
        )->clear();
    }
}
