<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Session\Session;

class Admin extends AdminCore
{
    /**
     * Logout function for admins.
     *
     * @param Session $oSession
     *
     * @return void
     */
    public function logout(Session $oSession)
    {
        $oSession->destroy();
    }

    /**
     * Delete Admin.
     *
     * @param int $iProfileId
     * @param string $sUsername
     * @param AdminCoreModel $oAdminModel
     *
     * @return void
     *
     * @throws ForbiddenActionException
     */
    public function delete($iProfileId, $sUsername, AdminCoreModel $oAdminModel)
    {
        $iProfileId = (int)$iProfileId;

        if (AdminCore::isRootProfileId($iProfileId)) {
            throw new ForbiddenActionException('You cannot delete the Root Administrator!');
        }

        (new $oAdminModel)->delete($iProfileId, $sUsername);
    }
}
