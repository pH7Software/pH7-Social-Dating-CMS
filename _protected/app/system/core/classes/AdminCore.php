<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class AdminCore extends UserCore
{
    const ROOT_PROILE_ID = 1;

    /**
     * Admins'levels.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bIsConnected = ((int)$oSession->exists('admin_id')) &&
            $oSession->get('admin_ip') === Ip::get() &&
            $oSession->get('admin_http_user_agent') === (new Browser)->getUserAgent();
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * Determines if the ID is from Root Admin user (main admin).
     *
     * @param int $iProfileId
     *
     * @return bool
     */
    public static function isRootProfileId($iProfileId)
    {
        return $iProfileId === static::ROOT_PROILE_ID;
    }

    /**
     * @param AdminCoreModel $oAdminModel
     *
     * @return bool TRUE if the IP is the one the site was installed, FALSE otherwise.
     */
    public static function isAdminIp(AdminCoreModel $oAdminModel)
    {
        return $oAdminModel->getRootIp() === Ip::get();
    }

    /**
     * Set an admin authentication.
     *
     * @param stdClass $oAdminData User database object.
     * @param UserCoreModel $oAdminModel
     * @param Session $oSession
     * @param SecurityModel $oSecurityModel
     *
     * @return void
     */
    public function setAuth(stdClass $oAdminData, UserCoreModel $oAdminModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Remove the session if the admin is logged in as "user" or "affiliate".
        if (UserCore::auth() || AffiliateCore::auth()) {
            $oSession->destroy();
        }

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
        $oSecurityModel->addLoginLog(
            $oAdminData->email,
            $oAdminData->username,
            '*****',
            'Logged in!',
            DbTableName::ADMIN
        );
        $oAdminModel->setLastActivity($oAdminData->profileId, DbTableName::ADMIN);
    }
}
