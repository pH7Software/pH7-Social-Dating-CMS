<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        $bAffAuth = AffiliateCore::auth();
        $bAdminAuth = AdminCore::auth();

        if (!$bAffAuth && ($this->registry->controller === 'AdsController' || $this->registry->action === 'logout'))
        {
            Header::redirect(Uri::get('affiliate','signup','step1'), $this->signUpMsg(), 'error');
        }

        if ((!$bAffAuth && !$bAdminAuth) && ($this->registry->controller === 'AccountController'
        && $this->registry->action !== 'activate'))
        {
            Header::redirect(Uri::get('affiliate','signup','step1'), $this->signUpMsg(), 'error');
        }

        if ($bAffAuth && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate'
        || $this->registry->action === 'resendactivation' || $this->registry->action === 'login'))
        {
            Header::redirect(Uri::get('affiliate','account','index'), $this->alreadyConnectedMsg(), 'error');
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Header::redirect(Uri::get('affiliate','home','index'), $this->adminSignInMsg(), 'error');
        }
    }

}
