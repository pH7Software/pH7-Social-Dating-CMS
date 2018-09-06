<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cookie\Cookie;
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
        self::revokeRememberMeSession();
    }

    /**
     * Revoke the "Remember Me" cookies (if exist) in order to completely logout the user.
     *
     * @return void
     */
    public static function revokeRememberMeSession()
    {
        $oCookie = new Cookie;
        $aRememberMeCookieNames = ['member_remember', 'member_id'];

        // If "Remember Me" checkbox has been checked
        if ($oCookie->exists($aRememberMeCookieNames)) {
            $oCookie->remove($aRememberMeCookieNames);
        }
        unset($oCookie);
    }
}
