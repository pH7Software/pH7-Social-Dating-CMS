<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        // Level for Notes
        $bAdminAuth = AdminCore::auth();

        if (!UserCore::auth() && ($this->registry->action === 'add' || $this->registry->action === 'edit' || $this->registry->action === 'delete')) {
            $this->signUpRedirect();
        }

        if (!$bAdminAuth || UserCore::isAdminLoggedAs()) {
            if (!$this->checkMembership() || ($this->registry->action === 'read' && !$this->group->read_notes)) {
                $this->paymentRedirect();
            } elseif ($this->registry->action === 'add' && !$this->group->write_notes) {
                $this->paymentRedirect();
            }
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController') {
            // For security reasons, we don't redirect the user to the admin panel URL
            Header::redirect(
                Uri::get('note', 'main', 'index'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
