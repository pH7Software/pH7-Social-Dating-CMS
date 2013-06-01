<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute;

class SettingController extends Controller
{

    private $sTitle;

    public function index()
    {
        // Add Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_CSS, 'tabs.css');
        // Add JS file for the 'display_status' function
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_JS, 'common.js');

        // Get the profile background
        $this->view->path_img_background = $this->_getWallpaper();

        $this->sTitle = t('Account Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit Your Profile');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function avatar()
    {
        $this->view->page_title = t('Photo of profile');
        $this->view->h2_title = t('Change your Avatar');
        $this->view->username = (AdminCore::auth() && !User::auth() && $this->httpRequest->getExists('username')) ? $this->httpRequest->get('username') : $this->session->get('member_username');
        $this->view->first_name = (AdminCore::auth() && !User::auth() && $this->httpRequest->getExists('first_name')) ? $this->httpRequest->get('first_name') : $this->session->get('member_first_name');
        $this->view->sex = (AdminCore::auth() && !User::auth() && $this->httpRequest->getExists('sex')) ? $this->httpRequest->get('sex') : $this->session->get('member_sex');

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        if ($this->httpRequest->postExists('del'))
            $this->_removeAvatar();

        $this->output();
    }

    public function design()
    {
        $this->sTitle = t('Your Wallpaper');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        // Get the profile background
        $this->view->path_img_background = $this->_getWallpaper();

        if ($this->httpRequest->postExists('del'))
            $this->_removeWallpaper();

        $this->output();
    }

    public function notification()
    {
        $this->sTitle = t('Notifications');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function privacy()
    {
        // Add JS file for the 'display_status' function
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_JS, 'common.js');

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

        if ($this->httpRequest->get('delete_status') == 'yesdelete')
        {
            $this->session->set('yes_delete', 1);
            Framework\Url\HeaderUrl::redirect(UriRoute::get('user', 'setting', 'yesdelete'));
        }
        elseif ($this->httpRequest->get('delete_status') == 'nodelete')
        {
            $this->view->content = t('<span class="bold green1">Great, you stay with us!<br />
            You see, you will not regret it!<br />We will do our best to you our %site_name%!</span>');
            $this->design->setRedirect(UriRoute::get('user', 'main', 'index'), null, null, 3);
        }
        else
        {
            $this->view->content = '<span class="bold red">' . t('Are you really sure you want to delete your account?') . '</span><br /><br />
                <a class="bold" href="' . UriRoute::get('user', 'setting', 'delete', 'nodelete') . '">' . t('No I changed my mind and I stay with you!') .
                '</a> &nbsp; ' . t('OR') . ' &nbsp; <a href="' . UriRoute::get('user',
                'setting', 'delete', 'yesdelete') . '">' . t('Yes I really want to delete my account') . '</a>';
        }

        $this->output();
    }

    public function yesDelete()
    {
        if (!$this->session->exists('yes_delete'))
            Framework\Url\HeaderUrl::redirect(UriRoute::get('user', 'setting', 'delete'));
        else
            $this->output();
    }


    private function _removeAvatar()
    {
        (new UserCore)->deleteAvatar($this->session->get('member_id'), $this->session->get('member_username'));
        Framework\Url\HeaderUrl::redirect(Framework\Mvc\Router\UriRoute::get('user', 'account', 'avatar'), t('Your avatar has been deleted successfully!'));
    }

    private function _getWallpaper()
    {
        $sBackground = (new UserModel)->getBackground($this->session->get('member_id'), 1);
        return (!empty($sBackground)) ? PH7_URL_DATA_SYS_MOD . 'user/background/img/' . $this->session->get('member_username') . PH7_DS . $sBackground : PH7_URL_TPL .
            PH7_TPL_NAME . PH7_DS . PH7_IMG . 'icon/none.jpg';
    }

    private function _removeWallpaper()
    {
        (new UserCore)->deleteBackground($this->session->get('member_id'), $this->session->get('member_username'));
        Framework\Url\HeaderUrl::redirect(Framework\Mvc\Router\UriRoute::get('user','setting', 'design'), t('Your wallpaper has been deleted successfully!'));
    }

}
