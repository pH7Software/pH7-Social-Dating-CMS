<?php
/**
 * @title          Facebook Authentication Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        1.2
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
PH7\Framework\Mvc\Router\Uri;

class Facebook extends Api implements IApi
{

    const GRAPH_URL = 'https://graph.facebook.com/';

    private $_sAvatarFile, $_sUsername, $_iProfileId, $_aUserInfo;

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        Import::lib('Service.Facebook.Facebook'); // Import the library

        $oFb = new \Facebook(
            array(
              'appId' => Config::getInstance()->values['module.api']['facebook.id'],
              'secret' => Config::getInstance()->values['module.api']['facebook.secret_key']
            )
        );

        $sUserId = $oFb->getUser();
        if($sUserId)
        {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $aProfile = $oFb->api('/me');
            } catch(\FacebookApiException $oE) {
                Framework\Error\CException\PH7Exception::launch($oE);
                $sUserId = null;
            }

            if($aProfile)
            {
                // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
                $oUserModel = new UserCoreModel;

                if(!$iId = $oUserModel->getId($aProfile['email']))
                {
                    // Add User if it does not exist in our database
                    $this->add(escape($aProfile, true), $oUserModel);

                    // Add User Avatar
                    $this->setAvatar($sUserId);

                    $this->oDesign->setFlashMsg( t('You now been registered! %0%', (new Registration)->sendMail($this->_aUserInfo, true)->getMsg()) );
                    $this->sUrl = Uri::get('connect','main','register');
                }
                else
                {   // Login
                    $this->setLogin($iId, $oUserModel);
                    $this->sUrl = Uri::get('connect','main','home');
                }

                unset($oUserModel);
            }
            else
            {
                // For testing purposes, if there was an error, let's kill the script
                $this->oDesign->setFlashMsg(t('Oops! An error has occurred. Please try again later.'));
                $this->sUrl = Uri::get('connect','main','index');
            }
        }
        else
        {
            // There's no active session, let's generate one
            $this->sUrl = $oFb->getLoginUrl(array('scope' => 'email,user_birthday,user_relationships,user_relationship_details,user_hometown,user_location,user_interests,user_about_me,user_likes,user_website'));
        }

        unset($oFb);
    }

    /**
     * @param array $aProfile
     * @param object \PH7\UserCoreModel $oUserModel
     * @return void
     */
    public function add(array $aProfile, UserCoreModel $oUserModel)
    {
        $oUser = new UserCore;
        $sBirthDate = (!empty($aProfile['birthday'])) ? $aProfile['birthday'] : date('m/d/Y', strtotime('-30 year'));
        $sLocation = ((!empty($aProfile['location']['name'])) ? $aProfile['location']['name'] : (!empty($aProfile['hometown']['name']) ? $aProfile['hometown']['name'] : ''));
        $aLocation = @explode(',', $sLocation);
        $sSex = ($aProfile['gender'] != 'male' && $aProfile['gender'] != 'female' && $aProfile['gender'] != 'couple') ? 'female' : $aProfile['gender']; // Default 'female'
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->_sUsername = $oUser->findUsername($aProfile['username'], $aProfile['first_name'], $aProfile['last_name']);
        $sSite = (!empty($aProfile['link'])) ? explode(' ', $aProfile['link'])[0] : '';
        $sSocialNetworkSite = (!empty($aProfile['username'])) ? 'http://facebook.com/' . $aProfile['username'] : '';
        unset($oUser);

        $this->_aUserInfo = [
            'email' => $aProfile['email'],
            'username' => $this->_sUsername,
            'password' => Various::genRndWord(8,30),
            'first_name' => (!empty($aProfile['first_name'])) ? $aProfile['first_name'] : '',
            'last_name' => (!empty($aProfile['last_name'])) ? $aProfile['last_name'] : '',
            'middle_name' => (!empty($aProfile['middle_name'])) ? $aProfile['middle_name'] : '',
            'sex' => $sSex,
            'match_sex' => array($sMatchSex),
            'birth_date' => (new CDateTime)->get($sBirthDate)->date('Y-m-d'),
            'country' => (!empty($aLocation[1])) ? trim($aLocation[1]) : Geo::getCountryCode(),
            'city' => (!empty($aLocation[0])) ? trim($aLocation[0]) : Geo::getCity(),
            'state' => (!empty($aProfile['locale'])) ? $aProfile['locale'] : Geo::getState(),
            'zip_code' => (!empty($aProfile['hometown_location']['zip'])) ? $aProfile['hometown_location']['zip'] : Geo::getZipCode(),
            'description' => (!empty($aProfile['bio'])) ? $aProfile['bio'] : '',
            'website' => $sSite,
            'social_network_site' => $sSocialNetworkSite,
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
     * @param string $sUserId
     * @return void
     */
    public function setAvatar($sUserId)
    {
        $this->_sAvatarFile = $this->getAvatar(static::GRAPH_URL . $sUserId . '/picture?type=large');

         if($this->_sAvatarFile)
         {
             $iApproved = (DbConfig::getSetting('avatarManualApproval') == 0) ? '1' : '0';
             (new UserCore)->setAvatar($this->_iProfileId, $this->_sUsername, $this->_sAvatarFile, $iApproved);
         }

         // Remove the temporary avatar
         (new File)->deleteFile($this->_sAvatarFile);
    }

}
