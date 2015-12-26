<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        $bAdminAuth = AdminCore::auth();

        /***** Levels for admin module *****/

        // Overall levels

        if (!$bAdminAuth && $this->registry->action !== 'login')
        {
            Header::redirect(Uri::get(PH7_ADMIN_MOD, 'main', 'login'), $this->signInMsg(), 'error');
        }

        if ($bAdminAuth && $this->registry->action === 'login')
        {
            Header::redirect(Uri::get(PH7_ADMIN_MOD, 'main', 'index'), t('Oops! You are already logged in as administrator.'), 'error');
        }

        // Options ...
    }

}
