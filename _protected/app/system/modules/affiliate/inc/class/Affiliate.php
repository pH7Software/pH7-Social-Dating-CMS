<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class
 */
namespace PH7;
use
PH7\Framework\Session\Session,
PH7\Framework\Cookie\Cookie,
PH7\Framework\Url\HeaderUrl,
PH7\Framework\Mvc\Router\UriRoute;

class Affiliate extends AffiliateCore
{

    /**
     * Logout function for affiliate.
     *
     * @return void
     */
    public function logout()
    {
        (new Session)->destroy();

        HeaderUrl::redirect(UriRoute::get('affiliate','home','index'), t('You have logged out!'));
    }

    /**
     * Add Refer Link.
     *
     * @param string $sUsername The Affiliate Username.
     * @return void
     */
    public function addRefer($sUsername)
    {
        /* Today's IP address is also easier to change than delete a cookie, so we have chosen the Cookie instead save the IP address in the database */
        $oCookie = new Cookie;
        $sCookieName = static::COOKIE_PREFIX . $sUsername;

        $iAffId = $this->getId(null, $sUsername, 'Affiliate');

        if(!$oCookie->exists($sCookieName)) {
            $oCookie->set($sCookieName, $iAffId, 3600*24*7); // Set a week
            (new AffiliateModel)->addRefer($iAffId); // Add a reference only for new clicks (if the cookie does not exist)
        } else {
            $oCookie->set($sCookieName, $iAffId, 3600*24*7); // Add an extra week
        }

        unset($oCookie);
    }

}
