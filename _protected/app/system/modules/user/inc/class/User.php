<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class User extends UserCore
{
    /**
     * Logout function for users.
     *
     * @return void
     *
     * @throws Framework\File\Exception
     */
    public function logout()
    {
        (new Session)->destroy();

        $oCookie = new Cookie; // If "Remember Me" checkbox has been checked
        $aRememberMeCookies = ['member_remember', 'member_id'];
        if ($oCookie->exists($aRememberMeCookies)) {
            $oCookie->remove($aRememberMeCookies);
        }

        Header::redirect(
            Uri::get('user', 'main', 'soon'),
            t('You are now logged out. Hope to see you again very soon!')
        );
    }
}
