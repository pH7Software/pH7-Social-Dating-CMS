<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Inc / Class
 */

namespace PH7;

use PH7\Framework\Session\Session;

class User extends UserCore
{
    /**
     * Logout function for users.
     *
     * @param Session $oSession
     *
     * @return void
     */
    public function logout(Session $oSession)
    {
        $oSession->destroy();
        UserCore::revokeRememberMeSession();
    }
}
