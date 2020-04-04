<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Security\Security;
use PH7\Framework\Session\Session;
use stdClass;

class RememberMeCore
{
    const CHECKBOX_FIELD_NAME = 'remember';
    const STAY_LOGGED_IN_REQUESTED = 'stayed_logged_requested';

    /**
     * @param Session $oSession
     *
     * @return bool
     */
    public function isEligible(Session $oSession)
    {
        return $oSession->exists(self::STAY_LOGGED_IN_REQUESTED);
    }

    public function enableSession(stdClass $oUserData)
    {
        $aCookieData = [
            // Hash one more time the password for the cookie
            'member_remember' => Security::hashCookie($oUserData->password),
            'member_id' => $oUserData->profileId
        ];
        (new Cookie)->set($aCookieData);
    }
}
