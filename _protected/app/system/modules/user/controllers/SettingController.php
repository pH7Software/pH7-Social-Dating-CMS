<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SettingController extends Controller
{
    private $_sUsername, $_sFirstName, $_sSex, $_sTitle, $_iProfileId, $_bAdminLogged;

    public function __construct()
    {
        parent::__construct();

        $this->_bAdminLogged = (AdminCore::auth() && !User::auth());
        $this->_iProfileId = (int) ($this->_bAdminLogged && $this->httpRequest->getExists('profile_id')) ? $this->httpRequest->get('profile_id') : $this->session->get('member_id');
        $this->_sUsername = ($this->_bAdminLogged && $this->httpRequest->getExists('username')) ? $this->httpRequest->get('username') : $this->session->get('member_username');
        $this->_sFirstName = ($this->_bAdminLogged && $this->httpRequest->getExists('first_name')) ? $this->httpRequest->get('first_name') : $this->session->get('member_first_name');
        $this->_sSex = ($this->_bAdminLogged && $this->httpRequest->getExists('sex')) ? $this->httpRequest->get('sex') : $this->session->get('member_sex');

        /** For the avatar on the index and avatar page **/
        $this->view->username = $this->_sUsername;
        $this->view->first_name = $this->_sFirstName;
        $this->view->sex = $this->_sSex;
        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        /** For the wallpaper on the index and design page **/
        $this->view->path_img_background = $this->_getWallpaper();

        /** For the 'display_status' function on the index and privacy page **/
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'common.js');
    }

    public function index()
    {
        // Add Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tabs.css');

        $this->_sTitle = t('Account Settings');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function edit()
    {
        $this->_sTitle = t('Edit Your Profile');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function avatar()
    {
        $this->view->page_title = t('Profile Photo');
        $this->view->h2_title = t('Change your Profile Photo');

        if ($this->httpRequest->postExists('del'))
            $this->_removeAvatar();

        $this->output();
    }

    public function design()
    {
        $this->_sTitle = t('Your Wallpaper');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;

        if ($this->httpRequest->postExists('del'))
            $this->_removeWallpaper();

        $this->output();
    }

    public function notification()
    {
        $this->_sTitle = t('Notifications');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function privacy()
    {
        $this->_sTitle = t('Privacy Settings');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function password()
    {
        $this->_sTitle = t('Change Password');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function delete()
    {
        $this->_sTitle = t('Delete Account');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;

        if ($this->httpRequest->get('delete_status') == 'yesdelete')
        {
            $this->session->set('yes_delete', 1);
            Header::redirect(Uri::get('user', 'setting', 'yesdelete'));
        }
        elseif ($this->httpRequest->get('delete_status') == 'nodelete')
        {
            $this->view->delete_status = false;
            $this->design->setRedirect(Uri::get('user', 'main', 'index'), null, null, 4);
        }
        else
        {
            $this->view->delete_status = true;
        }

        $this->output();
    }

    public function yesDelete()
    {
        if (!$this->session->exists('yes_delete'))
            Header::redirect(Uri::get('user', 'setting', 'delete'));
        else
            $this->output();
    }


    private function _removeAvatar()
    {
        (new UserCore)->deleteAvatar($this->_iProfileId, $this->_sUsername);
        Header::redirect(null, t('Profile photo successfully deleted'));
    }

    private function _getWallpaper()
    {
        $sBackground = (new UserModel)->getBackground($this->_iProfileId, 1);
        return (!empty($sBackground)) ?
            PH7_URL_DATA_SYS_MOD . 'user/background/img/' . $this->_sUsername . PH7_SH . $sBackground :
            PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG . 'icon/none.jpg';
    }

    private function _removeWallpaper()
    {
        (new UserCore)->deleteBackground($this->_iProfileId, $this->_sUsername);
        Header::redirect(null, t('Your wallpaper has been deleted successfully!'));
    }
}
