<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Mvc\Router\Uri, PH7\Framework\Url\HeaderUrl;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if(!UserCore::auth() && !AdminCore::auth())
        {
            HeaderUrl::redirect(Uri::get('user','main','login'), $this->signInMsg(), 'error');
        }

        if(!AdminCore::auth() && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            HeaderUrl::redirect(Uri::get('user','main','login'), $this->adminSignInMsg(), 'error');
        }
    }

}
