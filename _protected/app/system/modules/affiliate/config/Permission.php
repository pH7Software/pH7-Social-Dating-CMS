<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        $bAffAuth = AffiliateCore::auth();
        $bAdminAuth = AdminCore::auth();

        if (!$bAffAuth && ($this->registry->controller === 'AdsController' || $this->registry->action === 'logout')) {
            Header::redirect(
                Uri::get('affiliate', 'signup', 'step1'),
                $this->signUpMsg(),
                Design::ERROR_TYPE
            );
        }

        if ((!$bAffAuth && !$bAdminAuth) && ($this->registry->controller === 'AccountController'
                && $this->registry->action !== 'activate')
        ) {
            Header::redirect(
                Uri::get('affiliate', 'signup', 'step1'),
                $this->signUpMsg(),
                Design::ERROR_TYPE
            );
        }

        if ($bAffAuth && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate' ||
                $this->registry->action === 'resendactivation' || $this->registry->action === 'login')
        ) {
            Header::redirect(
                Uri::get('affiliate', 'account', 'index'),
                $this->alreadyConnectedMsg(),
                Design::ERROR_TYPE
            );
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController') {
            // For security reasons, we don't redirect user to the admin panel URL
            Header::redirect(
                Uri::get('affiliate', 'home', 'index'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
