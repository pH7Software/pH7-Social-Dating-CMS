<?php
/**
 * @title          Facebook Authentication Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        2.0
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\Import,
PH7\Framework\Date\CDateTime,
PH7\Framework\Config\Config,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Ip\Ip,
PH7\Framework\File\File,
PH7\Framework\Util\Various,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Error\CException\PH7Exception,
Facebook\Facebook as FB,
Facebook\FacebookResponse,
Facebook\Helpers\FacebookRedirectLoginHelper,
Facebook\GraphNodes\GraphUser,
Facebook\GraphNodes\GraphLocation,
Facebook\Exceptions\FacebookSDKException,
Facebook\Exceptions\FacebookResponseException,
PH7\Framework\Mvc\Router\Uri;

class Facebook extends Api implements IApi
{

    const GRAPH_URL = 'https://graph.facebook.com/';

    private $oProfile, $oLocation, $sAvatarFile, $sUsername, $iProfileId, $aUserInfo;

    private $aPermissions = [
        'email',
        'user_birthday',
        'user_relationships',
        'user_relationship_details',
        'user_hometown',
        'user_location',
        'user_about_me',
        'user_likes',
        'user_website'
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $oFb = new FB([
            'app_id' => Config::getInstance()->values['module.api']['facebook.id'],
            'app_secret' => Config::getInstance()->values['module.api']['facebook.secret_key'],
            'default_graph_version' => 'v2.7',
        ]);

        $oHelper = $oFb->getRedirectLoginHelper();

        try {
            $sAccessToken = $oHelper->getAccessToken();
        } catch(FacebookSDKException $oE) {
            PH7Exception::launch($oE);
        }

        if (empty($sAccessToken)) {
            // First off, set the login URL
            $this->setLoginUrl($oHelper);
            return; // Stop method
        }

        // Set the FB access token for the app
        $oFb->setDefaultAccessToken($sAccessToken);

        try {
            $oResponse = $oFb->get('/me');
            $this->initClassAttrs($oResponse);
        } catch(FacebookResponseException $oE) {
            PH7Exception::launch($oE);
        }


        // If we have GraphUser object
        if (!empty($this->oProfile)) {
            // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
            $oUserModel = new UserCoreModel;

            if (!$iId = $oUserModel->getId($this->oProfile->getEmail())) {
                // Add User if it does not exist in our database
                $this->add($oUserModel);

                // Add User Avatar
                $this->setAvatar($this->oProfile->getId());

                $this->oDesign->setFlashMsg( t('You have now been registered! %0%', (new Registration)->sendMail($this->aUserInfo, true)->getMsg()) );
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

        unset($oFb);
    }

    /**
     * @param \PH7\UserCoreModel $oUserModel
     * @return void
     */
    public function add(UserCoreModel $oUserModel)
    {
        $oUser = new UserCore;
        $sBirthDate = !empty($this->oProfile->getBirthday()) ? $this->oProfile->getBirthday() : date('m/d/Y', strtotime('-30 year'));
        $sSex = $this->checkGender($this->oProfile->getGender());
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->sUsername = $oUser->findUsername($this->oProfile->getId(), $this->oProfile->getFirstName(), $this->oProfile->getLastName());
        unset($oUser);

        $this->aUserInfo = [
            'email' => $this->oProfile->getEmail(),
            'username' => $this->sUsername,
            'password' => Various::genRndWord(8,30),
            'first_name' => $this->oProfile->getFirstName(),
            'last_name' => $this->oProfile->getLastName(),
            'middle_name' => $this->oProfile->getMiddleName(),
            'sex' => $sSex,
            'match_sex' => array($sMatchSex),
            'birth_date' => (new CDateTime)->get($sBirthDate)->date('Y-m-d'),
            'country' => Geo::getCountryCode(),
            'city' =>  !empty($this->oLocation->getCity()) ? $this->oLocation->getCity() : Geo::getCity(),
            'state' => !empty($this->oLocation->getState()) ? $this->oLocation->getState() : Geo::getState(),
            'zip_code' => !empty($this->oLocation->getZip()) ? $this->oLocation->getZip() : Geo::getZipCode(),
            'description' => $this->oProfile->getDescription(),
            'social_network_site' => $oProfie->getLink(),
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(),
            'is_active' => DbConfig::getSetting('userActivationType')
        ];

        $this->iProfileId = $oUserModel->add($this->aUserInfo);
    }

    /**
     * Set Avatar.
     *
     * @param string $sUserId FB user ID.
     * @return void
     */
    public function setAvatar($sUserId)
    {
        $this->sAvatarFile = $this->getAvatar(static::GRAPH_URL . $sUserId . '/picture?type=large');

         if ($this->sAvatarFile) {
             $iApproved = (DbConfig::getSetting('avatarManualApproval') == 0) ? '1' : '0';
             (new UserCore)->setAvatar($this->iProfileId, $this->sUsername, $this->sAvatarFile, $iApproved);
         }

         // Remove the temporary avatar
         (new File)->deleteFile($this->sAvatarFile);
    }

    /**
     * Set the FB Login URL.
     *
     * @param \Facebook\Helpers\FacebookRedirectLoginHelper $oHelper
     * @return void
     */
    protected function setLoginUrl(FacebookRedirectLoginHelper $oHelper)
    {

        $this->sUrl = $oHelper->getLoginUrl(Uri::get('connect','main','home'), $this->aPermissions);
    }

    private function initClassAttrs(FacebookResponse $oResponse)
    {
        $this->oProfile = $oResponse->getGraphObject(GraphUser::className());
        $this->oLocation = $oResponse->getGraphObject(GraphLocation::className());
    }
}
