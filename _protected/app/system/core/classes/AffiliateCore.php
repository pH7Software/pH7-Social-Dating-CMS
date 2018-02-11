<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class AffiliateCore extends UserCore
{
    const COOKIE_NAME = 'pHSAff';

    /**
     * Affiliates'levels.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bIsConnected = ((int)$oSession->exists('affiliate_id')) &&
            $oSession->get('affiliate_ip') === Ip::get() &&
            $oSession->get('affiliate_http_user_agent') === (new Browser)->getUserAgent();
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * Set an affiliate authentication.
     *
     * @param stdClass $oAffData User database object.
     * @param UserCoreModel $oAffModel
     * @param Session $oSession
     * @param SecurityModel $oSecurityModel
     *
     * @return void
     */
    public function setAuth(stdClass $oAffData, UserCoreModel $oAffModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Remove the session if the affiliate is logged on as "user" or "affiliate".
        if (UserCore::auth() || AdminCore::auth()) {
            $oSession->destroy();
        }

        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'affiliate_id' => $oAffData->profileId,
            'affiliate_email' => $oAffData->email,
            'affiliate_username' => $oAffData->username,
            'affiliate_first_name' => $oAffData->firstName,
            'affiliate_sex' => $oAffData->sex,
            'affiliate_ip' => Ip::get(),
            'affiliate_http_user_agent' => (new Browser)->getUserAgent(),
            'affiliate_token' => Various::genRnd($oAffData->email)
        ];

        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog(
            $oAffData->email,
            $oAffData->username,
            '*****',
            'Logged in!',
            DbTableName::AFFILIATE
        );
        $oAffModel->setLastActivity($oAffData->profileId, DbTableName::AFFILIATE);
    }

    /**
     * Check if an admin is logged as an affiliate.
     *
     * @return bool
     */
    public static function isAdminLoggedAs()
    {
        return (new Session)->exists('login_affiliate_as');
    }

    /**
     * Update the Affiliate Commission.
     *
     * @param integer $iAffId Affiliate ID
     * @param Config $oConfig
     * @param Registry $oRegistry
     *
     * @return void
     */
    public static function updateJoinCom($iAffId, Config $oConfig, Registry $oRegistry)
    {
        if ($iAffId < 1) {
            // If there is no valid ID, we stop the method
            return;
        }

        // Load the Affiliate config file
        $oConfig->load(PH7_PATH_SYS_MOD . 'affiliate' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        $sType = ($oRegistry->module == 'newsletter' ? 'newsletter' : ($oRegistry->module === 'affiliate' ? 'affiliate' : 'user'));
        $iAffCom = $oConfig->values['module.setting']['commission.join_' . $sType . '_money'];

        if ($iAffCom > 0) {
            (new AffiliateCoreModel)->updateUserJoinCom($iAffId, $iAffCom);
        }
    }

    /**
     * Delete Affiliate.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        (new AffiliateCoreModel)->delete($iProfileId, $sUsername);
    }
}
