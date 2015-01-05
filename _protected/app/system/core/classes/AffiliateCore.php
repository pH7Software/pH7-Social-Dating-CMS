<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;
use PH7\Framework\Config\Config, PH7\Framework\Registry\Registry;

// Abstract Class
class AffiliateCore extends UserCore
{

    const COOKIE_NAME = 'pHSAff';

    /**
     * Affiliates'levels.
     *
     * @return boolean
     */
    public static function auth()
    {
        $oSession = new Framework\Session\Session;
        $oBrowser = new Framework\Navigation\Browser;

        $bIsConnect = (((int)$oSession->exists('affiliate_id')) && $oSession->get('affiliate_ip') === Framework\Ip\Ip::get() && $oSession->get('affiliate_http_user_agent') === $oBrowser->getUserAgent());

        /** Destruction of the object and minimize CPU resources **/
        unset($oSession, $oBrowser);

        return $bIsConnect;
    }

    /**
     * Update the Affiliate Commission.
     *
     * @param integer $iAffId Affiliate ID
     * @param object \PH7\Framework\Config\Config $oConfig
     * @param object \PH7\Framework\Registry\Registry $oRegistry
     * @return void
     */
    public static function updateJoinCom($iAffId, Config $oConfig, Registry $oRegistry)
    {
        if ($iAffId < 1) return; // If there is no valid ID, we stop the method.

        // Load the Affiliate config file
        $oConfig->load(PH7_PATH_SYS_MOD . 'affiliate' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        $sType = ($oRegistry->module == 'newsletter' ? 'newsletter' : ($oRegistry->module == 'affiliate' ? 'affiliate' : 'user'));
        $iAffCom = $oConfig->values['module.setting']['commission.join_' . $sType . '_money'];

        if ($iAffCom > 0)
            (new AffiliateCoreModel)->updateUserJoinCom($iAffId, $iAffCom);
    }

    /**
     * Delete Affiliate.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        (new AffiliateCoreModel)->delete($iProfileId, $sUsername);
    }

}
