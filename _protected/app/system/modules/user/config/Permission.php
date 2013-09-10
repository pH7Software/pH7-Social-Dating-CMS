<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        $bUserAuth = User::auth();
        $bAdminAuth = AdminCore::auth();

        /***** Levels for members *****/

        // Overall levels
        if(!$bUserAuth && (($this->registry->controller === 'AccountController' && $this->registry->action !== 'activate') || ($this->registry->controller === 'FriendController' && $this->registry->action === 'mutual')
        || $this->registry->action === 'logout'))
        {
            HeaderUrl::redirect(Uri::get('user','signup','step1'), $this->signUpMsg(), 'error');
        }

        if((!$bUserAuth && !$bAdminAuth) && ($this->registry->controller === 'SettingController' || $this->registry->controller === 'SearchController'))
        {
            HeaderUrl::redirect(Uri::get('user','signup','step1'), $this->signUpMsg(), 'error');
        }

        if($bUserAuth && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate' || $this->registry->action === 'resendactivation' || $this->registry->action === 'login'))
        {
            HeaderUrl::redirect(Uri::get('user','account','index'), $this->alreadyConnectedMsg(), 'error');
        }

        // Options and Memberships ...
        if(!$bAdminAuth) // If the administrator is not logged
        {
            if($this->registry->controller === 'SearchController')
            {
                if(!$this->checkMembership() || !$this->group->quick_search_profiles || !$this->group->advanced_search_profiles)
                {
                    HeaderUrl::redirect(Uri::get('payment','main','index'), t('Please update your membership!'), 'warning');
                }
            }
        }
    }

}
