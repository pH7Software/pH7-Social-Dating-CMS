<?php
/**
 * @title          Token CSRF (Cross-site request forgery)
 * @desc           Protects against Cross-site request forgery attack.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / CSRF
 * @version        1.2
 */

namespace PH7\Framework\Security\CSRF;

defined('PH7') or exit('Restricted access');

use PH7\AdminCore;
use PH7\AffiliateCore;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use PH7\UserCore;

/**
 * This class provides functions of numbers against XSS (Cross-site scripting) vulnerability.
 * PH Security Token (PHST)
 */
final class Token
{
    /**
     * @internal We have commented on "security_token_http_referer_*" because it causes bugs and it doesn't
     * play a big role for safety because this variable can be changed by users (and the web browser).
     */

    const VAR_NAME = 'pHST';

    /** @var Session */
    private $oSession;

    /** @var null|string */
    private $sHttpReferer;

    /** @var null|string */
    private $sUserAgent;

    public function __construct()
    {
        $this->oSession = new Session;

        $oBrowser = new Browser;
        $this->sHttpReferer = $oBrowser->getHttpReferer();
        $this->sUserAgent = $oBrowser->getUserAgent();
        unset($oBrowser);
    }

    /**
     * Generate a random token.
     *
     * @param string $sName
     *
     * @return string The Token generated random.
     */
    public function generate($sName)
    {
        // If the token is still valid, it returns the correct token
        if ($this->oSession->exists('security_token_' . $sName)) {
            return $this->oSession->get('security_token_' . $sName);
        }

        $sToken = Various::genRnd($sName);

        $aSessionData = [
            'security_token_' . $sName => $sToken,
            'security_token_time_' . $sName => time(),
            //'security_token_http_referer_' . $sName => $this->sHttpReferer,
            'security_token_ip_' . $sName => Ip::get(),
            'security_token_http_user_agent_' . $sName => $this->sUserAgent
        ];

        $this->oSession->set($aSessionData);

        return $sToken;
    }

    /**
     * @param string $sName Name of the Token.
     *
     * @param string $sInputToken The name of the token inserted in the hidden tag of the form.
     * (e.g. for a from with method "post" and the field "<input type="hidden" name="my_token" />" the name of the token is "$_POST['my_token']"
     *
     * @param int $iTime Lifetime of token in seconds (value specified in the database settings).
     *
     * @return bool Returns TRUE if the token is validated, FALSE otherwise.
     */
    public function check($sName, $sInputToken = null, $iTime = null)
    {
        $iTime = $iTime === null ? DbConfig::getSetting('securityTokenLifetime') : $iTime;

        // The default tag name for the security token
        $sInputToken = empty($sInputToken) ? (new HttpRequest)->post('security_token') : $sInputToken;

        $aCheckSession = [
            'security_token_' . $sName,
            'security_token_time_' . $sName,
            //'security_token_http_referer_' . $sName,
            'security_token_ip_' . $sName,
            'security_token_http_user_agent_' . $sName
        ];

        if ($this->oSession->exists($aCheckSession) && !empty($sInputToken))
            if ($this->oSession->get('security_token_' . $sName) === $sInputToken)
                if ($this->oSession->get('security_token_time_' . $sName) >= (time() - $iTime))
                    //if ($this->sHttpReferer === $this->oSession->get('security_token_http_referer_' . $sName))
                        if (Ip::get() === $this->oSession->get('security_token_ip_' . $sName))
                            if ($this->sUserAgent === $this->oSession->get('security_token_http_user_agent_' . $sName)) {
                                // Delete the token and data sessions expired
                                $this->oSession->remove($aCheckSession);
                                return true;
                            }

        // Delete the token and data sessions expired
        $this->oSession->remove($aCheckSession);

        return false;
    }

    /**
     * The Get Token parameter for the URL if someone is logged (User, Admin or Affiliate), nothing otherwise.
     *
     * @return string
     */
    public function url()
    {
        return $this->currentSess() !== true ? '?' . static::VAR_NAME . '=' . $this->currentSess() : '';
    }

    /**
     * Checks the URL Token.
     *
     * @return bool
     */
    public function checkUrl()
    {
        $oHttpRequest = new HttpRequest;
        $bRet = (($this->currentSess() === true) || $oHttpRequest->currentUrl() === PH7_URL_ROOT ||
            ($oHttpRequest->get(static::VAR_NAME) === $this->currentSess()));
        unset($oHttpRequest);

        return $bRet;
    }

    /**
     * Gets The Current Session Token.
     *
     * @return string|bool The "token" if a user is logged or "true" if no user is logged.
     */
    private function currentSess()
    {
        if (UserCore::auth()) {
            return $this->oSession->get('member_token');
        }

        if (AdminCore::auth()) {
            return $this->oSession->get('admin_token');
        }

        if (AffiliateCore::auth()) {
            return $this->oSession->get('affiliate_token');
        }

        // If nobody is logged on, we did not need to do this test, so let's return TRUE
        return true;
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
