<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        $bAdminAuth = AdminCore::auth();

        if ((!UserCore::auth() && !$bAdminAuth) && ($this->registry->action === 'add' || $this->registry->action === 'delete')) {
            $this->signInRedirect();
        }

        if (!$bAdminAuth || UserCore::isAdminLoggedAs()) {
            if (!$this->checkMembership() || !$this->group->view_comments) {
                $this->paymentRedirect();
            } elseif ($this->registry->action === 'add' && !$this->group->write_comments) {
                $this->paymentRedirect();
            }
        }
    }
}
