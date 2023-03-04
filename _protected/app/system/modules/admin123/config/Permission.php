<?php
/**
 * Levels for admin module.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Config
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

        $bAdminAuth = AdminCore::auth();

        if (!$bAdminAuth && $this->registry->action !== 'login') {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'main', 'login'),
                $this->signInMsg(),
                Design::ERROR_TYPE
            );
        }

        if ($bAdminAuth && $this->registry->action === 'login') {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'main', 'index'),
                t('Oops! You are already logged in as administrator.'),
                Design::ERROR_TYPE
            );
        }
    }
}
