<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Inc / Class
 */
namespace PH7;
use
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Url\HeaderUrl;

class User extends UserCore
{

    /**
     * Logout function for users.
     *
     * @return void
     */
    public function logout()
    {
        (new Session)->destroy();

        HeaderUrl::redirect(UriRoute::get('user','main','soon'), t('You have logged out!'));
    }

}
