<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');
use PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if(!AffiliateCore::auth() && ($this->registry->controller === 'AdsController' || $this->registry->action === 'logout')) {
            Framework\Url\HeaderUrl::redirect(Uri::get('affiliate','signup','step1'), $this->signUpMsg(), 'error');
        }

        if((!AffiliateCore::auth() && !AdminCore::auth()) && ($this->registry->controller === 'AccountController' && $this->registry->action !== 'activate')) {
            Framework\Url\HeaderUrl::redirect(Uri::get('affiliate','signup','step1'), $this->signUpMsg(), 'error');
        }

        if(!AdminCore::auth() && $this->registry->controller === 'AdminController') {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Framework\Url\HeaderUrl::redirect(Uri::get('affiliate','home','index'), $this->adminSignInMsg(), 'error');
        }

        if(AffiliateCore::auth() && ($this->registry->controller === 'SignupController' || $this->registry->action === 'activate' || $this->registry->action === 'resendactivation' || $this->registry->action === 'login')) {
            Framework\Url\HeaderUrl::redirect(Uri::get('affiliate','account','index'), $this->alreadyConnectedMsg(), 'error');
        }

    }

}
