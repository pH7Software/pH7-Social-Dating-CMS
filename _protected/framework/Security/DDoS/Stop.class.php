<?php
/**
 * @title          Stop DDoS attack.
 * @desc           Trying to protect against Distributed Denial-Of-Service attack.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / DDoS
 * @version        0.2
 */

namespace PH7\Framework\Security\DDoS;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Session\Session;

/**
 * This class securing the server against DDoS attack only! (not for DoS attacks)
 */
final class Stop
{
    // SSS = Stop for Server Security
    const COOKIE_NAME = 'sss';

    const COOKIE_LIFETIME = 60 * 60 * 48;

    /**
     * @return bool Return TRUE if it believes that we get too many requests from that session, FALSE otherwise.
     */
    public function cookie()
    {
        $oCookie = new Cookie;

        if (!$oCookie->exists(static::COOKIE_NAME)) {
            $oCookie->set(static::COOKIE_NAME, 1, self::COOKIE_LIFETIME);
        } else {
            $oCookie->set(static::COOKIE_NAME, ($oCookie->get(static::COOKIE_NAME) + 1));
        }

        if ($oCookie->get(static::COOKIE_NAME) > PH7_DDOS_MAX_COOKIE_PAGE_LOAD) {
            $oCookie->remove(static::COOKIE_NAME); // Remove Cookie
            $bStatus = true;
        } else {
            $bStatus = false;
        }

        unset($oCookie);

        return $bStatus;
    }

    /**
     * @return bool Return TRUE if it believes that we get too many requests from that session, FALSE otherwise.
     */
    public function session()
    {
        $oSession = new Session;

        if (!$oSession->exists(static::COOKIE_NAME)) {
            $oSession->set(static::COOKIE_NAME, 1);
        } else {
            $oSession->set(static::COOKIE_NAME, ($oSession->get(static::COOKIE_NAME) + 1));
        }

        if ($oSession->get(static::COOKIE_NAME) > PH7_DDOS_MAX_SESSION_PAGE_LOAD) {
            $oSession->remove(static::COOKIE_NAME); // Remove Session
            $bStatus = true;
        } else {
            $bStatus = false;
        }

        unset($oSession);

        return $bStatus;
    }

    /**
     * Set delay in the script execution.
     *
     * @return void
     */
    public function wait()
    {
        sleep(PH7_DDOS_DELAY_SLEEP);
    }

    private function __clone()
    {
    }
}
