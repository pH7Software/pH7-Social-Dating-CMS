<?php
/**
 * @title          Permission File
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License.
 * @package        PH7 / App / Module / Fake Admin Panel / Config
 * @version        1.1.0
 */

namespace PH7;
use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\UriRoute;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

         // Level for Admins
        if (!AdminCore::auth() && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            HeaderUrl::redirect(UriRoute::get('fake-admin-panel','main','login'), $this->adminSignInMsg(), 'error');
        }
    }

}
