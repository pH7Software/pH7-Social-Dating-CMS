<?php
/**
 * @title          Twitter Authentication Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @author         Steve Guidetti <http://net.tutsplus.com/tutorials/php/creating-a-twitter-oauth-application/>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;
use Teapot\StatusCode;
use tmhOAuth;
use tmhUtilities;

class Twitter extends Api implements IApi
{
    /** @var tmhOAuth */
    private $oTwOAuth;

    /** @var string */
    private $sAvatarFile;

    /** @var string */
    private $sUsername;

    /** @var int */
    private $iState;

    /** @var int */
    private $iProfileId;

    /** @var array */
    private $aUserInfo;

    public function __construct()
    {
        parent::__construct();

        // Import the library
        Import::lib('Service.Twitter.tmhOAuth');
        Import::lib('Service.Twitter.tmhUtilities');

        $this->oTwOAuth = new tmhOAuth(
            Config::getInstance()->values['module.api']['twitter.consumer_key'],
            Config::getInstance()->values['module.api']['twitter.consumer_secret_key']
        );

        // determine the authentication status
        // default to 0
        $this->iState = 0;

        if (isset($_COOKIE['access_token'], $_COOKIE['access_token_secret'])) {
            // 2 (authenticated) if the cookies are set
            $this->iState = 2;
        } elseif (isset($_SESSION['authstate'])) {
            // otherwise use value stored in session
            $this->iState = (int)$_SESSION['authstate'];
        }

        if ($this->iState === 1) {
            // if we are in the process of authentication we continue
            $this->auth();
        } elseif ($this->iState === 2 && !$this->auth()) {
            // verify authentication, clearing cookies if it fails
            $this->endSession();
        }

        if ($this->auth()) {
            $aProfile = $this->oTwOAuth->extract_params($this->oTwOAuth->response['response']);

            if (empty($aProfile['error'])) {
                // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
                $oUserModel = new UserCoreModel;

                if (!$iId = $oUserModel->getId($aProfile['email'])) {
                    // Add User if it does not exist in our database
                    $this->add(escape($aProfile, true), $oUserModel);

                    // Add User Avatar
                    $this->setAvatar($aProfile);

                    $this->oDesign->setFlashMsg(
                        t('You have now been registered! %0%', (new Registration($this->oView))->sendMail($this->aUserInfo, true)->getMsg())
                    );
                    $this->sUrl = Uri::get('connect', 'main', 'register');
                } else {
                    // Login
                    $this->setLogin($iId, $oUserModel);
                    $this->sUrl = Uri::get('connect', 'main', 'home');
                }

                unset($oUserModel);
            } else {
                // For testing purposes, if there was an error, let's kill the script
                $this->oDesign->setFlashMsg(
                    t('Oops! An error has occurred. Please try again later.'),
                    Design::ERROR_TYPE
                );
                $this->sUrl = Uri::get('connect', 'main', 'index');
            }
        } else {
            $this->sUrl = Uri::get('connect', 'main', 'index');
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
        if ($this->iState === 1 && !isset($_GET['oauth_verifier'])) {
            $this->iState = 0;
        }

        // Step 1: Get a request token
        if ($this->iState === 0) {
            return $this->getRequestToken();
        } // Step 2: Get an access token
        elseif ($this->iState === 1) {
            return $this->getAccessToken();
        }

        // Step 3: Verify the access token
        return $this->verifyAccessToken();
    }

    /**
     * Check the current state of authentication.
     *
     * @return bool Returns TRUE if state is 2 (authenticated).
     */
    public function isAuthed()
    {
        return $this->iState === 2;
    }

    /**
     * Remove user's access token cookies.
     *
     * @return void
     */
    public function endSession()
    {
        $this->iState = 0;
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
        $this->oTwOAuth->request('POST', $this->oTwOAuth->url('1/statuses/update'), [
            'status' => $sText
        ]);

        return $this->oTwOAuth->response['code'] == StatusCode::OK;
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
        $sBirthDate = !empty($aProfile['birthday']) ? $aProfile['birthday'] : $this->getDefaultUserBirthDate();
        $sSex = $this->checkGender($aProfile['gender']);
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->sUsername = $oUser->findUsername($aProfile['given_name'], $aProfile['name'], $aProfile['family_name']);
        unset($oUser);

        $this->aUserInfo = [
            'email' => $aProfile['email'],
            'username' => $this->sUsername,
            'password' => Various::genRndWord(Registration::DEFAULT_PASSWORD_LENGTH),
            'first_name' => !empty($aProfile['given_name']) ? $aProfile['given_name'] : '',
            'last_name' => !empty($aProfile['family_name']) ? $aProfile['family_name'] : '',
            'sex' => $sSex,
            'match_sex' => [$sMatchSex],
            'birth_date' => (new CDateTime)->get($sBirthDate)->date(static::BIRTH_DATE_FORMAT),
            'country' => Geo::getCountryCode(),
            'city' => Geo::getCity(),
            'state' => Geo::getState(),
            'zip_code' => Geo::getZipCode(),
            'description' => !empty($aProfile['bio']) ? $aProfile['bio'] : '',
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
            'is_active' => DbConfig::getSetting('userActivationType')
        ];

        $this->iProfileId = $oUserModel->add($this->aUserInfo);
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
        $this->oTwOAuth->request(
            'GET',
            $this->oTwOAuth->url('1/users/profile_image/' . $aUserData['screen_name']),
            [
                'screen_name' => $aUserData['screen_name'],
                'size' => 'bigger'
            ]);

        // Try to get the URL for the avatar size standard
        if ($this->oTwOAuth->response['code'] == StatusCode::FOUND) {
            // the direct URL is in the Location header
            $this->sAvatarFile = $this->getAvatar($this->oTwOAuth->response['headers']['location']);
        } else {
            // If this does not work, we try to recover the URL for the original image in full size
            $this->sAvatarFile = $this->getAvatar($aUserData['profile_image_url']);
        }

        if ($this->sAvatarFile) {
            $iApproved = DbConfig::getSetting('avatarManualApproval') == 0 ? 1 : 0;
            (new UserCore)->setAvatar($this->iProfileId, $this->sUsername, $this->sAvatarFile, $iApproved);
        }

        // Remove the temporary avatar
        (new File)->deleteFile($this->sAvatarFile);
    }

    /**
     * Obtain a request token from Twitter.
     *
     * @return bool Returns FALSE if request failed.
     */
    private function getRequestToken()
    {
        // send request for a request token
        $this->oTwOAuth->request('POST', $this->oTwOAuth->url('oauth/request_token', ''), [
            // pass a variable to set the callback
            'oauth_callback' => tmhUtilities::php_self()
        ]);

        if ($this->oTwOAuth->response['code'] == StatusCode::OK) {
            // get and store the request token
            $aResponse = $this->oTwOAuth->extract_params($this->oTwOAuth->response['response']);
            $_SESSION['authtoken'] = $aResponse['oauth_token'];
            $_SESSION['authsecret'] = $aResponse['oauth_token_secret'];

            // state is now 1
            $_SESSION['authstate'] = 1;

            // redirect the user to Twitter to authorize
            $sUrl = $this->oTwOAuth->url('oauth/authorize', '') . '?oauth_token=' . $aResponse['oauth_token'];
            Header::redirect($sUrl);
        }

        return false;
    }

    /**
     * Obtain an access token from Twitter.
     *
     * @return bool Returns FALSE if request failed.
     */
    private function getAccessToken()
    {
        // set the request token and secret we have stored
        $this->oTwOAuth->config['user_token'] = $_SESSION['authtoken'];
        $this->oTwOAuth->config['user_secret'] = $_SESSION['authsecret'];

        // send request for an access token
        $this->oTwOAuth->request('POST', $this->oTwOAuth->url('oauth/access_token', ''), [
            // pass the oauth_verifier received from Twitter
            'oauth_verifier' => $_GET['oauth_verifier']
        ]);

        if ($this->oTwOAuth->response['code'] == StatusCode::OK) {
            // get the access token and store it in a cookie
            $aResponse = $this->oTwOAuth->extract_params($this->oTwOAuth->response['response']);
            setcookie('access_token', $aResponse['oauth_token'], time() + 3600 * 24 * 30);
            setcookie('access_token_secret', $aResponse['oauth_token_secret'], time() + 3600 * 24 * 30);

            // state is now 2
            $_SESSION['authstate'] = 2;

            // redirect user to clear leftover GET variables
            $this->sUrl = tmhUtilities::php_self();
            exit;
        }

        return false;
    }

    /**
     * Verify the validity of our access token.
     *
     * @return bool Access token verified.
     */
    private function verifyAccessToken()
    {
        $this->oTwOAuth->config['user_token'] = $_COOKIE['access_token'];
        $this->oTwOAuth->config['user_secret'] = $_COOKIE['access_token_secret'];

        // send verification request to test access key
        $this->oTwOAuth->request('GET', $this->oTwOAuth->url('1/account/verify_credentials'));

        // store the user data returned from the API
        $this->userdata = json_decode($this->oTwOAuth->response['response']);

        // HTTP 200 means we were successful
        return $this->oTwOAuth->response['code'] == StatusCode::OK;
    }
}
