<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\UriRoute;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        /***** Levels for members *****/

        // Overall levels
        if(!User::auth() && (($this->registry->controller === 'AccountController' && $this->registry->action !== 'activate') || $this->registry->controller === 'SearchController'
        || $this->registry->action === 'mutual' || $this->registry->action === 'logout'))
        {
            HeaderUrl::redirect(UriRoute::get('user','signup','step1'), $this->signUpMsg(), 'error');
        }

        if((!User::auth() && !AdminCore::auth()) && ($this->registry->controller === 'SettingController'))
        {
            HeaderUrl::redirect(UriRoute::get('user','signup','step1'), $this->signUpMsg(), 'error');
        }

        if(User::auth() && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate' || $this->registry->action === 'resendactivation' || $this->registry->action === 'login'))
        {
            HeaderUrl::redirect(UriRoute::get('user','account','index'), $this->alreadyConnectedMsg(), 'error');
        }

        // Options and Memberships ...
        if($this->registry->controller === 'SearchController')
        {
            if(!$this->checkMembership() || !$this->group->quick_search_profiles || !$this->group->advanced_search_profiles) {
                HeaderUrl::redirect(UriRoute::get('payment','main','index'), t('Please update your membership!'), 'warning');
            }
        }
    }

}
