<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
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
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        if (AdminCore::isRootProfileId($iProfileId)) {
            exit('You cannot delete the Root Administrator!');
        } else {
            (new AdminModel)->delete($iProfileId, $sUsername);
        }
    }
}
