<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use
PH7\Framework\Session\Session,
PH7\Framework\Ip\Ip,
PH7\Framework\Util\Various,
PH7\Framework\Navigation\Browser,
PH7\Framework\Mvc\Model\Security as SecurityModel;

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
        $oSession = new Session;
        $bIsConnected = (((int)$oSession->exists('admin_id')) && $oSession->get('admin_ip') === Ip::get() && $oSession->get('admin_http_user_agent') === (new Browser)->getUserAgent());
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * Set an admin authentication.
     *
     * @param integer object $oAdminData User database object.
     * @param object \PH7\UserCoreModel $oAdminModel
     * @param object \PH7\Framework\Session\Session $oSession
     * @param object \PH7\Framework\Mvc\Model\Security $oSecurityModel
     * @return void
     */
    public function setAuth($oAdminData, UserCoreModel $oAdminModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Remove the session if the admin is logged in as "user" or "affiliate".
        if (UserCore::auth() || AffiliateCore::auth())
            $oSession->destroy();

        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'admin_id' => $oAdminData->profileId,
            'admin_email' => $oAdminData->email,
            'admin_username' => $oAdminData->username,
            'admin_first_name' => $oAdminData->firstName,
            'admin_ip' => Ip::get(),
            'admin_http_user_agent' => (new Browser)->getUserAgent(),
            'admin_token' => Various::genRnd($oAdminData->email),
        ];
        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog($oAdminData->email, $oAdminData->username, '*****', 'Logged in!', 'Admins');
        $oAdminModel->setLastActivity($oAdminData->profileId, 'Admins');
    }

}
