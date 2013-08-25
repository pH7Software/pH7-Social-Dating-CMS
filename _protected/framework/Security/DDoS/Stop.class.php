<?php
/**
 * @title          Stop DDoS attack.
 * @desc           Trying to protect against Distributed Denial-Of-Service attack.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
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

    /**
     * @return boolean Return "true" If we believe that this person takes too much request otherwise "false"
     */
    public function cookie()
    {
        $oCookie = new Cookie;
        // SFC = Stop for Server Security
        if(!$oCookie->exists('sfss')) {
            $oCookie->set('sfss', 1, 60*60*48);
        } else {
             $oCookie->set('sfss', ($oCookie->get('sfss')+1));
        }
        if($oCookie->get('sfss') > PH7_DDOS_MAX_COOKIE_PAGE_LOAD) {
            $oCookie->remove('sfss'); // Remove Cookie
            $bStatus = true;
        } else {
            $bStatus = false;
        }

        unset($oCookie);
        return $bStatus;
    }

    /**
     * @return boolean Return "true" If we believe that this person takes too much request otherwise "false"
     */
    public function session()
    {
        $oSession = new Session;
        // SFC = Stop for Server Security
        if(!$oSession->exists('sfss')) {
            $oSession->set('sfss', 1);
        } else {
             $oSession->set('sfss', ($oSession->get('sfss')+1));
        }
        if($oSession->get('sfss') > PH7_DDOS_MAX_SESSION_PAGE_LOAD) {
            $oSession->remove('sfss'); // Remove Session
            $bStatus = true;
        } else {
            $bStatus = false;
        }

        unset($oSession);
        return $bStatus;
    }

    private function __clone() {}

}
