<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SettingController extends Controller
{
    const REDIRECTION_SEC_DELAY = 4;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sFirstName;

    /** @var string */
    private $sSex;

    /** @var int */
    private $iProfileId;

    /** @var bool */
    private $bAdminLogged;

    public function __construct()
    {
        parent::__construct();

        $this->bAdminLogged = AdminCore::auth() && !User::auth();
        $this->iProfileId = $this->getProfileId();
        $this->sUsername = $this->bAdminLogged && $this->httpRequest->getExists('username') ? $this->httpRequest->get('username') : $this->session->get('member_username');
        $this->sFirstName = $this->bAdminLogged && $this->httpRequest->getExists('first_name') ? $this->httpRequest->get('first_name') : $this->session->get('member_first_name');
        $this->sSex = $this->bAdminLogged && $this->httpRequest->getExists('sex') ? $this->httpRequest->get('sex') : $this->session->get('member_sex');

        /** For the avatar on the index and avatar page **/
        $this->view->username = $this->sUsername;
        $this->view->first_name = $this->sFirstName;
        $this->view->sex = $this->sSex;
        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        /** For the wallpaper on the index and design page **/
        $this->view->path_img_background = $this->getWallpaper();

        /** For the 'display_status' function on the index and privacy page **/
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'common.js'
        );
    }

    public function index()
    {
        // Add Css Style for Tabs
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'tabs.css'
        );

        $this->setTitle(t('Account Settings'));
        $this->output();
    }

    public function edit()
    {
        $this->setTitle(t('Edit Profile'));
        $this->output();
    }

    public function avatar()
    {
        $this->view->page_title = t('Profile Photo');
        $this->view->h2_title = t('Change your Profile Photo');

        if ($this->httpRequest->postExists('del')) {
            $this->removeAvatar();
        }

        $this->output();
    }

    public function design()
    {
        $this->setTitle(t('Profile Wallpaper'));

        if ($this->httpRequest->postExists('del')) {
            $this->removeWallpaper();
        }

        $this->output();
    }

    public function notification()
    {
        $this->setTitle(t('Email Notifications'));
        $this->output();
    }

    public function privacy()
    {
        $this->setTitle(t('Privacy Settings'));
        $this->output();
    }

    public function password()
    {
        $this->setTitle(t('Change Password'));
        $this->output();
    }

    public function delete()
    {
        $this->setTitle(t('Delete Account'));

        if ($this->httpRequest->get('delete_status') === 'yesdelete') {
            $this->session->set('yes_delete', 1);

            Header::redirect(
                Uri::get(
                    'user',
                    'setting',
                    'yesdelete'
                )
            );
        } elseif ($this->httpRequest->get('delete_status') === 'nodelete') {
            $this->view->delete_status = false;

            $this->design->setRedirect(
                Uri::get('user', 'main', 'index'),
                null,
                null,
                self::REDIRECTION_SEC_DELAY
            );
        } else {
            $this->view->delete_status = true;
        }

        $this->output();
    }

    public function yesDelete()
    {
        if ($this->session->exists('yes_delete')) {
            $this->output();
        } else {
            Header::redirect(
                Uri::get(
                    'user',
                    'setting',
                    'delete'
                )
            );
        }
    }

    private function removeAvatar()
    {
        (new UserCore)->deleteAvatar($this->iProfileId, $this->sUsername);

        Header::redirect(
            null,
            t('Profile photo successfully deleted')
        );
    }

    /**
     * @return string The user wallpaper.
     */
    private function getWallpaper()
    {
        $sBackground = (new UserModel)->getBackground($this->iProfileId, 1);
        $sBgFullPath = PH7_URL_DATA_SYS_MOD . 'user/background/img/' . $this->sUsername . PH7_SH . $sBackground;

        if (empty($sBackground)) {
            $sBgFullPath = PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'icon/' . UserDesignCore::NONE_IMG_FILENAME;
        }

        return $sBgFullPath;
    }

    private function removeWallpaper()
    {
        (new UserCore)->deleteBackground($this->iProfileId, $this->sUsername);

        Header::redirect(
            null,
            t('Your wallpaper has been successfully deleted!')
        );
    }

    /**
     * Returns the correct profile ID (depending if it's with the "login as" admin or not).
     *
     * @return int
     */
    private function getProfileId()
    {
        $iId = $this->session->get('member_id');

        if ($this->bAdminLogged && $this->httpRequest->getExists('profile_id')) {
            $iId = $this->httpRequest->get('profile_id');
        }

        return (int)$iId;
    }

    /**
     * Set title and heading.
     *
     * @param string $sTitle
     *
     * @return void
     */
    private function setTitle($sTitle)
    {
        $this->view->page_title = $this->view->h2_title = $sTitle;
    }
}
