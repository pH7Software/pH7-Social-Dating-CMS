<?php
/**
 * @title          Permission File
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Config
 * @version        1.1.0
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        // Level for Admins
        if (!AdminCore::auth() && $this->registry->controller === 'AdminController') {
            // For security reasons, we don't redirect the user to the admin panel URL
            Header::redirect(
                Uri::get('fake-admin-panel', 'main', 'login'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
