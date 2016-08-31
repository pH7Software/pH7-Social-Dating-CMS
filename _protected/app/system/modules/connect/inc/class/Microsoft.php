<?php
/**
 * @title          Microsoft OAuth (Windows Live) Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        0.6
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\Import,
PH7\Framework\Date\CDateTime,
PH7\Framework\Config\Config,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Ip\Ip,
PH7\Framework\Util\Various,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Mvc\Router\Uri;

class Microsoft extends Api
{

    private $_oClient, $_sUsername, $_iProfileId, $_aUserInfo;

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Import the library
        Import::lib('Service.Microsoft.Live.oauth_client');
        Import::lib('Service.Microsoft.Live.http');

        $this->_oClient = new \oauth_client_class;

        $this->_setConfig();

        /* API permissions */
        $this->_oClient->scope = 'wl.basic wl.emails wl.birthday';

        if(($bSuccess = $this->_oClient->Initialize()))
        {
            if(($bSuccess = $this->_oClient->Process()))
            {
                if(strlen($this->_oClient->authorization_error))
                {
                    $this->_oClient->error = $this->_oClient->authorization_error;
                    $bSuccess = false;
                }
                elseif(strlen($this->_oClient->access_token))
                {
                    $bSuccess = $this->_oClient->CallAPI(
                        'https://apis.live.net/v5.0/me',
                        'GET',
                        array(),
                        array('FailOnAccessError' => true),
                        $oUserData
                    );
                }
            }

            $bSuccess = $this->_oClient->Finalize($bSuccess);
        }

        if($this->_oClient->exit)
            exit(1);

        if($bSuccess)
        {
            // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
            $oUserModel = new UserCoreModel;

            if(!$iId = $oUserModel->getId($oUserData->emails->account))
            {
                // Add User if it does not exist in our database
                $this->add(escape($oUserData, true), $oUserModel);

                $this->oDesign->setFlashMsg( t('You have now been registered! %0%', (new Registration)->sendMail($this->_aUserInfo, true)->getMsg()) );
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

    /**
     * @param object $oProfile
     * @param object \PH7\UserCoreModel $oUserModel
     * @return void
     */
    public function add($oProfile, UserCoreModel $oUserModel)
    {
        $oUser = new UserCore;
        $sBirthDate = (isset($oProfile->birth_month, $oProfile->birth_day, $oProfile->birth_year)) ? $oProfile->birth_month . '/' . $oProfile->birth_day . '/' . $oProfile->birth_year : date('m/d/Y', strtotime('-30 year'));
        $sSex = $this->checkGender($oProfile->gender);
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->_sUsername = $oUser->findUsername($oProfile->name, $oProfile->first_name, $oProfile->last_name);
        unset($oUser);

        $this->_aUserInfo = [
            'email' => $oProfile->emails->account,
            'username' => $this->_sUsername,
            'password' => Various::genRndWord(8,30),
            'first_name' => (!empty($oProfile->first_name)) ? $oProfile->first_name : '',
            'last_name' => (!empty($oProfile->last_name)) ? $oProfile->last_name : '',
            'sex' => $sSex,
            'match_sex' => array($sMatchSex),
            'birth_date' => (new CDateTime)->get($sBirthDate)->date('Y-m-d'),
            'country' => Geo::getCountryCode(),
            'city' => Geo::getCity(),
            'state' => Geo::getState(),
            'zip_code' => Geo::getZipCode(),
            'description' => '',
            'website' => '',
            'social_network_site' => '',
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(),
            'is_active' => DbConfig::getSetting('userActivationType')
        ];

        $this->_iProfileId = $oUserModel->add($this->_aUserInfo);
    }

    /**
     * Set Configuration of Microsoft OAuth API.
     *
     * @return void
     */
    private function _setConfig()
    {
        $this->_oClient->server = 'Microsoft';
        $this->_oClient->redirect_uri = Uri::get('connect','main','login','google');

        $this->_oClient->client_id = Config::getInstance()->values['module.api']['microsoft.client_id'];
        $this->_oClient->client_secret = Config::getInstance()->values['module.api']['microsoft.client_secret_key'];
    }

}
