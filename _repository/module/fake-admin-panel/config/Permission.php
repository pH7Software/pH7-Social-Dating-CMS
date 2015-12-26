<?php
/**
 * @title          Permission File
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Config
 * @version        1.1.0
 */

namespace PH7;

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

         // Level for Admins
        if (!AdminCore::auth() && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Header::redirect(Uri::get('fake-admin-panel','main','login'), $this->adminSignInMsg(), 'error');
        }
    }

}
