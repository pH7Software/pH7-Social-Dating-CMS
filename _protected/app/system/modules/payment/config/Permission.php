<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!UserCore::auth() && $this->registry->controller !== 'AdminController') {
            $this->signUpRedirect();
        }

        if (!AdminCore::auth() && $this->registry->controller === 'AdminController') {
            // For security reasons, we don't redirect the user to the admin panel URL
            Header::redirect(
                Uri::get('payment', 'main', 'index'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
