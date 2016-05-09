<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        $bAdminAuth = AdminCore::auth();

        if (!UserCore::auth() && !$bAdminAuth)
        {
            $this->signInRedirect();
        }

        if (!$bAdminAuth || UserCore::isAdminLoggedAs())
        {
            if (!$this->checkMembership() || ($this->registry->action === 'inbox' && !$this->group->read_mails))
            {
                $this->paymentRedirect();
            }
            elseif ($this->registry->action === 'compose' && !$this->group->send_mails)
            {
                $this->paymentRedirect();
            }
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('user','main','login'), $this->adminSignInMsg(), 'error');
        }
    }

}
