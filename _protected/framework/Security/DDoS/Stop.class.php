<?php
/**
 * @title          Stop DDoS attack.
 * @desc           Trying to protect against Distributed Denial-Of-Service attack.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / DDoS
 * @version        0.2
 */

namespace PH7\Framework\Security\DDoS;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie, PH7\Framework\Session\Session;

/**
 * This class securing the server for DDoS attack only! (Not for the attacks DoS)
 */
final class Stop
{

    // SSS = Stop for Server Security
    const COOKIE_NAME = 'sss';

    /**
     * @return boolean Return "true" If we believe that this person takes too much request otherwise "false"
     */
    public function cookie()
    {
        $oCookie = new Cookie;

        if (!$oCookie->exists(static::COOKIE_NAME))
            $oCookie->set(static::COOKIE_NAME, 1, 60*60*48);
        else
             $oCookie->set(static::COOKIE_NAME, ($oCookie->get(static::COOKIE_NAME)+1));

        if ($oCookie->get(static::COOKIE_NAME) > PH7_DDOS_MAX_COOKIE_PAGE_LOAD)
        {
            $oCookie->remove(static::COOKIE_NAME); // Remove Cookie
            $bStatus = true;
        }
        else
            $bStatus = false;

        unset($oCookie);
        return $bStatus;
    }

    /**
     * @return boolean Return "true" If we believe that this person takes too much request otherwise "false"
     */
    public function session()
    {
        $oSession = new Session;

        if (!$oSession->exists(static::COOKIE_NAME))
            $oSession->set(static::COOKIE_NAME, 1);
        else
             $oSession->set(static::COOKIE_NAME, ($oSession->get(static::COOKIE_NAME)+1));

        if ($oSession->get(static::COOKIE_NAME) > PH7_DDOS_MAX_SESSION_PAGE_LOAD)
        {
            $oSession->remove(static::COOKIE_NAME); // Remove Session
            $bStatus = true;
        }
        else
            $bStatus = false;

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

    private function __clone() {}

}
