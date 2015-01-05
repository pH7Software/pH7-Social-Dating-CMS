<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

// Abstract Class
class AdminCore extends UserCore
{

    /**
     * Admins'levels.
     *
     * @return boolean
     */
    public static function auth()
    {
        $oSession = new Framework\Session\Session;
        $oBrowser = new Framework\Navigation\Browser;

        $bIsConnect = (((int)$oSession->exists('admin_id')) && $oSession->get('admin_ip') === Framework\Ip\Ip::get() && $oSession->get('admin_http_user_agent') === $oBrowser->getUserAgent());

        /** Destruction of the object and minimize CPU resources **/
        unset($oSession, $oBrowser);

        return $bIsConnect;
    }

}
