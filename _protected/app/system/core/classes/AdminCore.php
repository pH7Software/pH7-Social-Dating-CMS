<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class AdminCore extends UserCore
{
    public const ROOT_PROFILE_ID = 1;

    /**
     * Check if an admin is authenticated.
     */
    public static function auth(): bool
    {
        $oSession = new Session;
        $bSessionIpCheck = ((bool)DbConfig::getSetting('isAdminSessionIpCheck')) ? $oSession->get('admin_ip') === Ip::get() : true;

        $bIsLogged = $oSession->exists('admin_id') &&
            $bSessionIpCheck &&
            $oSession->get('admin_http_user_agent') === (new Browser)->getUserAgent();
        unset($oSession);

        return $bIsLogged;
    }

    /**
     * @return bool TRUE if the user is on the "admin" module, FALSE otherwise.
     */
    public static function isAdminPanel(): bool
    {
        return Registry::getInstance()->module === PH7_ADMIN_MOD;
    }

    /**
     * Determines if the ID is from Root Admin user (main admin).
     *
     * @param int $iProfileId
     */
    public static function isRootProfileId($iProfileId): bool
    {
        return $iProfileId === static::ROOT_PROFILE_ID;
    }

    /**
     * @param AdminCoreModel $oAdminModel
     *
     * @return bool TRUE if the IP is the one the site was installed, FALSE otherwise.
     */
    public static function isAdminIp(AdminCoreModel $oAdminModel): bool
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
     */
    public function setAuth(
        stdClass $oAdminData,
        UserCoreModel $oAdminModel,
        Session $oSession,
        SecurityModel $oSecurityModel
    ): void {
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
            DbTableName::ADMIN_LOG_LOGIN
        );
        $oSecurityModel->addSessionLog(
            $oAdminData->profileId,
            $oAdminData->email,
            $oAdminData->firstName,
            DbTableName::ADMIN_LOG_SESS
        );
        $oAdminModel->setLastActivity($oAdminData->profileId, DbTableName::ADMIN);
    }
}
