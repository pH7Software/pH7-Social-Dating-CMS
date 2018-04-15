<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SettingController extends Controller
{
    /** @var string */
    private $sUsername;

    /** @var string */
    private $sFirstName;

    /** @var string */
    private $sSex;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iProfileId;

    /** @var bool */
    private $bAdminLogged;

    public function __construct()
    {
        parent::__construct();

        $this->bAdminLogged = (AdminCore::auth() && !User::auth());
        $this->iProfileId = $this->getProfileId();
        $this->sUsername = ($this->bAdminLogged && $this->httpRequest->getExists('username')) ? $this->httpRequest->get('username') : $this->session->get('member_username');
        $this->sFirstName = ($this->bAdminLogged && $this->httpRequest->getExists('first_name')) ? $this->httpRequest->get('first_name') : $this->session->get('member_first_name');
        $this->sSex = ($this->bAdminLogged && $this->httpRequest->getExists('sex')) ? $this->httpRequest->get('sex') : $this->session->get('member_sex');

        /** For the avatar on the index and avatar page **/
        $this->view->username = $this->sUsername;
        $this->view->first_name = $this->sFirstName;
        $this->view->sex = $this->sSex;
        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        /** For the wallpaper on the index and design page **/
        $this->view->path_img_background = $this->getWallpaper();

        /** For the 'display_status' function on the index and privacy page **/
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'common.js');
    }

    public function index()
    {
        // Add Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tabs.css');

        $this->sTitle = t('Account Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit Profile');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

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
        $this->sTitle = t('Profile Wallpaper');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->postExists('del')) {
            $this->removeWallpaper();
        }

        $this->output();
    }

    public function notification()
    {
        $this->sTitle = t('Email Notifications');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function privacy()
    {
        $this->sTitle = t('Privacy Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function password()
    {
        $this->sTitle = t('Change Password');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function delete()
    {
        $this->sTitle = t('Delete Account');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->get('delete_status') === 'yesdelete') {
            $this->session->set('yes_delete', 1);
            Header::redirect(Uri::get('user', 'setting', 'yesdelete'));
        } elseif ($this->httpRequest->get('delete_status') === 'nodelete') {
            $this->view->delete_status = false;
            $this->design->setRedirect(
                Uri::get('user', 'main', 'index'),
                null,
                null,
                4
            );
        } else {
            $this->view->delete_status = true;
        }

        $this->output();
    }

    public function yesDelete()
    {
        if (!$this->session->exists('yes_delete')) {
            Header::redirect(Uri::get('user', 'setting', 'delete'));
        } else {
            $this->output();
        }
    }

    private function removeAvatar()
    {
        (new UserCore)->deleteAvatar($this->iProfileId, $this->sUsername);

        Header::redirect(null, t('Profile photo successfully deleted'));
    }

    /**
     * @return string The user wallpaper.
     */
    private function getWallpaper()
    {
        $sBackground = (new UserModel)->getBackground($this->iProfileId, 1);

        if (!empty($sBackground)) {
            $sBgFullPath = PH7_URL_DATA_SYS_MOD . 'user/background/img/' . $this->sUsername . PH7_SH . $sBackground;
        } else {
            $sBgFullPath = PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'icon/' . UserDesignCore::NONE_IMG_FILENAME;
        }

        return $sBgFullPath;
    }

    private function removeWallpaper()
    {
        (new UserCore)->deleteBackground($this->iProfileId, $this->sUsername);

        Header::redirect(null, t('Your wallpaper has been successfully deleted!'));
    }

    /**
     * @return int
     */
    private function getProfileId()
    {
        if ($this->bAdminLogged && $this->httpRequest->getExists('profile_id')) {
            $iId = $this->httpRequest->get('profile_id');
        } else {
            $iId = $this->session->get('member_id');
        }

        return (int)$iId;
    }
}
