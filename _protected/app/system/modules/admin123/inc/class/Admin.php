<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Session\Session;

class Admin extends AdminCore
{
    /**
     * Logout an admin.
     */
    public function logout(Session $oSession): void
    {
        $oSession->destroy();
    }

    /**
     * Delete Admin.
     *
     * @param int $iProfileId
     * @param string $sUsername
     * @param UserCoreModel $oAdminModel
     *
     * @throws ForbiddenActionException
     */
    public function delete($iProfileId, string $sUsername, UserCoreModel $oAdminModel): void
    {
        $iProfileId = (int)$iProfileId;

        if (AdminCore::isRootProfileId($iProfileId)) {
            throw new ForbiddenActionException('You cannot delete the Root Administrator!');
        }

        $oAdminModel->delete($iProfileId, $sUsername);
    }
}
