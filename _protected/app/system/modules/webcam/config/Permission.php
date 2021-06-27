<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
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

        if ($this->isNotAdmin()) {
            if (!$this->checkMembership() || !$this->group->webcam_access) {
                $this->paymentRedirect();
            }
        }
    }

    /**
     * @return bool TRUE if the admin is not logged in (TRUE as well if the admin use "login as user").
     */
    private function isNotAdmin()
    {
        return !AdminCore::auth() || UserCore::isAdminLoggedAs();
    }
}
