<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Webcam / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!AdminCore::auth() || UserCore::isAdminLoggedAs()) {// If the admin is not logged (but can be if the admin use "login as user" feature)
            if (!$this->checkMembership() || !$this->group->webcam_access) {
                $this->paymentRedirect();
            }
        }
    }
}
