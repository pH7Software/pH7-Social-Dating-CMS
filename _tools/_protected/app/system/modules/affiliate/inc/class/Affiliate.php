<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Session\Session;

class Affiliate extends AffiliateCore
{
    const COOKIE_LIFETIME = 3600 * 24 * 7;

    /**
     * Logout function for affiliate.
     *
     * @param Session $oSession
     *
     * @return void
     */
    public function logout(Session $oSession)
    {
        $oSession->destroy();
    }

    /**
     * Add Refer Link.
     *
     * @param string $sUsername The Affiliate Username.
     *
     * @return void
     *
     * @internal Today's IP address is also easier to change than delete a cookie, so we have chosen the Cookie instead save the IP address in the database.
     */
    public function addRefer($sUsername)
    {
        $oAffModel = new AffiliateModel;
        $oCookie = new Cookie;

        $iAffId = $oAffModel->getId(null, $sUsername, DbTableName::AFFILIATE);

        if (!$oCookie->exists(static::COOKIE_NAME)) {
            $this->setCookie($iAffId, $oCookie); // Set a week
            $oAffModel->addRefer($iAffId); // Add a reference only for new clicks (if the cookie does not exist)
        } else {
            $this->setCookie($iAffId, $oCookie); // Add an extra week
        }

        unset($oAffModel, $oCookie);
    }

    /**
     * Set an Affiliate Cookie.
     *
     * @param integer $iAffId
     * @param Cookie $oCookie
     *
     * @return void
     */
    private function setCookie($iAffId, Cookie $oCookie)
    {
        $oCookie->set(
            static::COOKIE_NAME,
            $iAffId,
            self::COOKIE_LIFETIME
        );
    }
}
