<?php
/**
 * @title          Twitter Authentication Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @author         Steve Guidetti <http://net.tutsplus.com/tutorials/php/creating-a-twitter-oauth-application/>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        0.9
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\File\File;
use PH7\Framework\File\Import;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Util\Various;

class Twitter extends Api implements IApi
{
    private $_oTwOAuth, $_sAvatarFile, $_sUsername, $_iState, $_iProfileId, $_aUserInfo;

    public function __construct()
    {
        parent::__construct();

        // Import the library
        Import::lib('Service.Twitter.tmhOAuth');
        Import::lib('Service.Twitter.tmhUtilities');

        $this->_oTwOAuth = new \tmhOAuth(
            Config::getInstance()->values['module.api']['twitter.consumer_key'],
            Config::getInstance()->values['module.api']['twitter.consumer_secret_key']
        );

        // determine the authentication status
        // default to 0
        $this->_iState = 0;

        if (isset($_COOKIE['access_token'], $_COOKIE['access_token_secret'])) {
            // 2 (authenticated) if the cookies are set
            $this->_iState = 2;
        } elseif (isset($_SESSION['authstate'])) {
            // otherwise use value stored in session
            $this->_iState = (int)$_SESSION['authstate'];
        }

        if ($this->_iState == 1) {
            // if we are in the process of authentication we continue
            $this->auth();
        } elseif ($this->_iState == 2 && !$this->auth()) {
            // verify authentication, clearing cookies if it fails
            $this->endSession();
        }

        if ($this->auth()) {
            $aProfile = $this->_oTwOAuth->extract_params($this->_oTwOAuth->response['response']);

            if (empty($aProfile['error'])) {
                // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
                $oUserModel = new UserCoreModel;

                if (!$iId = $oUserModel->getId($aProfile['email'])) {
                    // Add User if it does not exist in our database
                    $this->add(escape($aProfile, true), $oUserModel);

                    // Add User Avatar
                    $this->setAvatar($aProfile);

                    $this->oDesign->setFlashMsg( t('You have now been registered! %0%', (new Registration)->sendMail($this->_aUserInfo, true)->getMsg()) );
                    $this->sUrl = Uri::get('connect','main','register');
                } else {
                    // Login
                    $this->setLogin($iId, $oUserModel);
                    $this->sUrl = Uri::get('connect','main','home');
                }

                unset($oUserModel);
            } else {
                // For testing purposes, if there was an error, let's kill the script
                $this->oDesign->setFlashMsg(t('Oops! An error has occurred. Please try again later.'));
                $this->sUrl = Uri::get('connect','main','index');
            }
        } else {
            $this->sUrl = Uri::get('connect','main','index');
        }
    }

    /**
     * Authenticate user with Twitter.
     *
     * @return bool Authentication successful.
     */
    public function auth()
    {
        // state 1 requires a GET variable to exist
        if ($this->_iState == 1 && !isset($_GET['oauth_verifier'])) {
            $this->_iState = 0;
        }

        // Step 1: Get a request token
        if ($this->_iState == 0) {
            return $this->_getRequestToken();
        } // Step 2: Get an access token
        elseif ($this->_iState == 1) {
            return $this->_getAccessToken();
        }

        // Step 3: Verify the access token
        return $this->_verifyAccessToken();
    }

    /**
     * Check the current state of authentication.
     *
     * @return bool Returns TRUE if state is 2 (authenticated).
     */
    public function isAuthed()
    {
        return $this->_iState == 2;
    }

    /**
     * Remove user's access token cookies.
     *
     * @return void
     */
    public function endSession()
    {
        $this->_iState = 0;
        $_SESSION['authstate'] = 0;
        setcookie('access_token', '', 0);
        setcookie('access_token_secret', '', 0);
    }

    /**
     * Send a tweet on the user's behalf.
     *
     * @param string $sText Text to tweet.
     *
     * @return bool Tweet successfully sent.
     */
    public function sendTweet($sText)
    {
        // limit the string to 140 characters
        $sText = substr($sText, 0, 140);

        // POST the text to the statuses/update method
        $this->_oTwOAuth->request('POST', $this->_oTwOAuth->url('1/statuses/update'), array(
            'status' => $sText
        ));

        return $this->_oTwOAuth->response['code'] == 200;
    }

    /**
     * @param array $aProfile
     * @param UserCoreModel $oUserModel
     *
     * @return void
     */
    public function add(array $aProfile, UserCoreModel $oUserModel)
    {
        $oUser = new UserCore;
        $sBirthDate = (!empty($aProfile['birthday'])) ? $aProfile['birthday'] : date('m/d/Y', strtotime('-30 year'));
        $sSex = $this->checkGender($aProfile['gender']);
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->_sUsername = $oUser->findUsername($aProfile['given_name'], $aProfile['name'], $aProfile['family_name']);
        unset($oUser);

        $this->_aUserInfo = [
            'email' => $aProfile['email'],
            'username' => $this->_sUsername,
            'password' => Various::genRndWord(Registration::DEFAULT_PASSWORD_LENGTH),
            'first_name' => (!empty($aProfile['given_name'])) ? $aProfile['given_name'] : '',
            'last_name' => (!empty($aProfile['family_name'])) ? $aProfile['family_name'] : '',
            'sex' => $sSex,
            'match_sex' => array($sMatchSex),
            'birth_date' => (new CDateTime)->get($sBirthDate)->date('Y-m-d'),
            'country' => Geo::getCountryCode(),
            'city' => Geo::getCity(),
            'state' => Geo::getState(),
            'zip_code' => Geo::getZipCode(),
            'description' => (!empty($aProfile['bio'])) ? $aProfile['bio'] : '',
            'website' => '',
            'social_network_site' => $aProfile['link'],
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(),
            'is_active' => DbConfig::getSetting('userActivationType')
        ];

        $this->_iProfileId = $oUserModel->add($this->_aUserInfo);
    }

    /**
     * Set Avatar.
     *
     * @param string $aUserData
     *
     * @return void
     */
    public function setAvatar($aUserData)
    {
        // Request user's 'bigger' profile image
        $this->_oTwOAuth->request('GET', $this->_oTwOAuth->url('1/users/profile_image/' . $aUserData['screen_name']), array(
            'screen_name' => $aUserData['screen_name'],
            'size' => 'bigger'
        ));

        // Try to get the URL for the avatar size standard
        if ($this->_oTwOAuth->response['code'] == 302) {
            // the direct URL is in the Location header
            $this->_sAvatarFile = $this->getAvatar($this->_oTwOAuth->response['headers']['location']);
        } else {
            // If this does not work, we try to recover the URL for the original image in full size
            $this->_sAvatarFile = $this->getAvatar($aUserData['profile_image_url']);
        }

        if ($this->_sAvatarFile) {
            $iApproved = (DbConfig::getSetting('avatarManualApproval') == 0) ? '1' : '0';
            (new UserCore)->setAvatar($this->_iProfileId, $this->_sUsername, $this->_sAvatarFile, $iApproved);
        }

        // Remove the temporary avatar
        (new File)->deleteFile($this->_sAvatarFile);
    }

    /**
     * Obtain a request token from Twitter.
     *
     * @return bool Returns FALSE if request failed.
     */
    private function _getRequestToken()
    {
        // send request for a request token
        $this->_oTwOAuth->request('POST', $this->_oTwOAuth->url('oauth/request_token', ''), array(
            // pass a variable to set the callback
            'oauth_callback' => \tmhUtilities::php_self()
        ));

        if ($this->_oTwOAuth->response['code'] == 200) {
            // get and store the request token
            $aResponse = $this->_oTwOAuth->extract_params($this->_oTwOAuth->response['response']);
            $_SESSION['authtoken'] = $aResponse['oauth_token'];
            $_SESSION['authsecret'] = $aResponse['oauth_token_secret'];

            // state is now 1
            $_SESSION['authstate'] = 1;

            // redirect the user to Twitter to authorize
            $sUrl = $this->_oTwOAuth->url('oauth/authorize', '') . '?oauth_token=' . $aResponse['oauth_token'];
            Framework\Url\Header::redirect($sUrl);
        }

        return false;
    }

    /**
     * Obtain an access token from Twitter.
     *
     * @return bool Returns FALSE if request failed.
     */
    private function _getAccessToken()
    {
        // set the request token and secret we have stored
        $this->_oTwOAuth->config['user_token'] = $_SESSION['authtoken'];
        $this->_oTwOAuth->config['user_secret'] = $_SESSION['authsecret'];

        // send request for an access token
        $this->_oTwOAuth->request('POST', $this->_oTwOAuth->url('oauth/access_token', ''), array(
            // pass the oauth_verifier received from Twitter
            'oauth_verifier' => $_GET['oauth_verifier']
        ));

        if ($this->_oTwOAuth->response['code'] == 200) {
            // get the access token and store it in a cookie
            $aResponse = $this->_oTwOAuth->extract_params($this->_oTwOAuth->response['response']);
            setcookie('access_token', $aResponse['oauth_token'], time()+3600*24*30);
            setcookie('access_token_secret', $aResponse['oauth_token_secret'], time()+3600*24*30);

            // state is now 2
            $_SESSION['authstate'] = 2;

            // redirect user to clear leftover GET variables
            $this->sUrl = \tmhUtilities::php_self();
            exit;
        }

        return false;
    }

    /**
     * Verify the validity of our access token.
     *
     * @return bool Access token verified.
     */
    private function _verifyAccessToken()
    {
        $this->_oTwOAuth->config['user_token'] = $_COOKIE['access_token'];
        $this->_oTwOAuth->config['user_secret'] = $_COOKIE['access_token_secret'];

        // send verification request to test access key
        $this->_oTwOAuth->request('GET', $this->_oTwOAuth->url('1/account/verify_credentials'));

        // store the user data returned from the API
        $this->userdata = json_decode($this->_oTwOAuth->response['response']);

        // HTTP 200 means we were successful
        return ($this->_oTwOAuth->response['code'] == 200);
    }
}
