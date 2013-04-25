<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

// Abstract Class
class AffiliateCore extends UserCore
{

    const COOKIE_PREFIX = 'pHSAff_';

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
     * Delete Affiliated.
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
