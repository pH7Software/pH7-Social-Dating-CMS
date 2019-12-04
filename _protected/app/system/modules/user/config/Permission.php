<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        $bUserAuth = User::auth();
        $bAdminAuth = AdminCore::auth();

        /***** Levels for members *****/

        // Overall levels
        if (!$bUserAuth && (($this->registry->controller === 'AccountController' && $this->registry->action !== 'activate')
            || $this->registry->action === 'logout')) {
            $this->signUpRedirect();
        }

        if ((!$bUserAuth && !$bAdminAuth) && ($this->registry->controller === 'SettingController')) {
            $this->signUpRedirect();
        }

        if ($bUserAuth && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate'
            || $this->registry->action === 'resendactivation' || $this->registry->action === 'login')) {
            $this->alreadyConnectedRedirect();
        }

        // Options and Memberships ...
        /*
         * If the admin is not logged (but can be if the admin use "login as user" feature)
         * and not redirect to payment page if the user wants to logout
        */
        if ((!$bAdminAuth || User::isAdminLoggedAs()) && $this->registry->action !== 'logout') {
            if (!$this->checkMembership() || ($bUserAuth && !$this->group->member_site_access)) {
                $this->paymentRedirect();
            } elseif ($this->registry->controller === 'SearchController') {
                if (!$this->group->quick_search_profiles || !$this->group->advanced_search_profiles) {
                    $this->paymentRedirect();
                }
            }
        }
    }
}
