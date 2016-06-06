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
        $bIsConnected = (((int)$oSession->exists('admin_id')) && $oSession->get('admin_ip') === Framework\Ip\Ip::get() && $oSession->get('admin_http_user_agent') === (new Browser)->getUserAgent());
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * Set a user authentication.
     *
     * @param integer $iId Admin profile ID.
     * @param object \PH7\AdminCoreModel $oAdminModel
     * @param object \PH7\Framework\Session\Session $oSession
     * @param object \PH7\Framework\Mvc\Model\Security $oSecurityModel
     * @return void
     */
    public function setAuth($iId, AdminCoreModel $oAdminModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Remove the session if the admin is logged on as "user" or "affiliate".
        if (UserCore::auth() || AffiliateCore::auth())
            $oSession->destroy();

        $oAdminData = $oAdminModel->readProfile($iId, 'Admins');

        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'admin_id' => $oAdminData->profileId,
            'admin_email' => $oAdminData->email,
            'admin_username' => $oAdminData->username,
            'admin_first_name' => $oAdminData->firstName,
            'admin_ip' => $sIp,
            'admin_http_user_agent' => (new Browser)->getUserAgent(),
            'admin_token' => Various::genRnd($oAdminData->email),
        ];
        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog($sEmail, $sUsername, '*****', 'Logged in!', 'Admins');
        $oAdminModel->setLastActivity($oAdminData->profileId, 'Admins');
    }

}
