<?php
/**
 * @title          Token CSRF (Cross-site request forgery)
 * @desc           Protects against Cross-site request forgery attack.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / CSRF
 * @version        1.2
 */

namespace PH7\Framework\Security\CSRF;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Session\Session,
PH7\Framework\Navigation\Browser,
PH7\Framework\Util\Various,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Ip\Ip;

/**
 * This class provides functions of numbers against the XSS (Cross-site scripting) vulnerability.
 * PH Security Token (PHST)
 */
final class Token
{

    /**
     * Note: We have commented on "security_token_http_referer_*" because it causes bugs and it doesn't
     * play a big role for safety because this variable can be changed by users (and the web browser).
     */

    private $_oSession, $_sHttpReferer, $_sUserAgent;

    const VAR_NAME = 'pHST';

    public function __construct()
    {
        $this->_oSession = new Session;

        $oBrowser = new Browser;
        $this->_sHttpReferer = $oBrowser->getHttpReferer();
        $this->_sUserAgent = $oBrowser->getUserAgent();
        unset($oBrowser);
    }

    /**
     * Generate a random token.
     *
     * @param string $sName
     * @return string The Token generated random.
     */
    public function generate($sName)
    {
        // If the token is still valid, it returns the correct token
        if ($this->_oSession->exists('security_token_' . $sName))
        {
            return $this->_oSession->get('security_token_' . $sName);
        }
        else
        {
            $sToken = Various::genRnd($sName);

            $aSessionData = [
                'security_token_' . $sName => $sToken,
                'security_token_time_' . $sName => time(),
                //'security_token_http_referer_' . $sName => $this->_sHttpReferer,
                'security_token_ip_' . $sName => Ip::get(),
                'security_token_http_user_agent_' . $sName => $this->_sUserAgent
            ];

            $this->_oSession->set($aSessionData);
            return $sToken;
        }
    }

    /**
     * @param string $sName Name of the Token.
     *
     * @param string $sInputToken The name of the token inserted in the hidden tag of the form.
     * (e.g. for a from with method "post" and the field "<input type="hidden" name="my_token" />" the name of the token is "$_POST['my_token']" Default NULL
     *
     * @param integer $iTime Lifetime of token in seconds. Default NULL (value specified in the database settings).
     *
     * @return boolean Returns TRUE if the token is validated, FALSE otherwise.
     */
    public function check($sName, $sInputToken = null, $iTime = null)
    {
        $iTime = (empty($iTime)) ? DbConfig::getSetting('securityTokenLifetime') : $iTime;

        // The default tag name for the security token
        $sInputToken = (empty($sInputToken)) ? (new Http)->post('security_token') : $sInputToken;

        $aCheckSession = [
            'security_token_' . $sName,
            'security_token_time_' . $sName,
            //'security_token_http_referer_' . $sName,
            'security_token_ip_' . $sName,
            'security_token_http_user_agent_' . $sName
        ];

        if ($this->_oSession->exists($aCheckSession) && !empty($sInputToken))
            if ($this->_oSession->get('security_token_' . $sName) === $sInputToken)
                if ($this->_oSession->get('security_token_time_' . $sName) >= (time() - $iTime))
                    //if ($this->_sHttpReferer === $this->_oSession->get('security_token_http_referer_' . $sName))
                        if (Ip::get() === $this->_oSession->get('security_token_ip_' . $sName))
                            if ($this->_sUserAgent === $this->_oSession->get('security_token_http_user_agent_' . $sName))
                            {
                                // Delete the token and data sessions expired
                                $this->_oSession->remove($aCheckSession);
                                return true;
                            }

        // Delete the token and data sessions expired
        $this->_oSession->remove($aCheckSession);
        return false;
    }

    /**
     * The Get Token parameter for the URL if someone is logged (User, Admin or Affiliate), nothing otherwise.
     *
     * @return string
     */
    public function url()
    {
        return ($this->currentSess() !== true) ? '?' . static::VAR_NAME . '=' . $this->currentSess() : '';
    }

    /**
     * Checks the URL Token.
     *
     * @return boolean
     */
    public function checkUrl()
    {
        $oHttpRequest = new Http;
        $bRet = ( ($this->currentSess() === true) || $oHttpRequest->currentUrl() === PH7_URL_ROOT || ($oHttpRequest->get(static::VAR_NAME) === $this->currentSess()) );
        unset($oHttpRequest);

        return $bRet;
    }

    /**
     * Gets The Current Session Token.
     *
     * @access protected
     * @return mixed (string | boolean) The "token" if a user is logged or "true" if no user is logged.
     */
    protected function currentSess()
    {
        if (\PH7\UserCore::auth())
            $sToken = $this->_oSession->get('member_token');
        elseif (\PH7\AdminCore::auth())
            $sToken = $this->_oSession->get('admin_token');
        elseif (\PH7\AffiliateCore::auth())
            $sToken = $this->_oSession->get('affiliate_token');
        else $sToken = true; // If nobody is logged on, we did not need to do this test, so it returns true

        return $sToken;
    }

    public function __destruct()
    {
        unset($this->_oSession, $this->_sHttpReferer, $this->_sUserAgent);
    }

    /**
     * Clone is set to private to stop cloning.
     *
     * @access private
     */
    private function __clone() {}

}
