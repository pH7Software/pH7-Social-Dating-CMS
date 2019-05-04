<?php
/**
 * @title          Microsoft OAuth (Windows Live) Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        0.6
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use oauth_client_class;
use PH7\Framework\Config\Config;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\File\Import;
use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Util\Various;
use stdClass;

class Microsoft extends Api
{
    const API_URL = 'https://apis.live.net/v5.0/me';

    /** @var oauth_client_class */
    private $oClient;

    /** @var string */
    private $sUsername;

    /** @var int */
    private $iProfileId;

    /** @var array */
    private $aUserInfo;

    public function __construct()
    {
        parent::__construct();

        // Import the library
        Import::lib('Service.Microsoft.Live.oauth_client');
        Import::lib('Service.Microsoft.Live.http');

        $this->oClient = new oauth_client_class;

        $this->setConfig();

        /* API permissions */
        $this->oClient->scope = 'wl.basic wl.emails wl.birthday';

        if (($bSuccess = $this->oClient->Initialize())) {
            if (($bSuccess = $this->oClient->Process())) {
                if (strlen($this->oClient->authorization_error)) {
                    $this->oClient->error = $this->oClient->authorization_error;
                    $bSuccess = false;
                } elseif (strlen($this->oClient->access_token)) {
                    $bSuccess = $this->oClient->CallAPI(
                        self::API_URL,
                        'GET',
                        [],
                        ['FailOnAccessError' => true],
                        $oUserData
                    );
                }
            }

            $bSuccess = $this->oClient->Finalize($bSuccess);
        }

        if ($this->oClient->exit) {
            exit(1);
        }

        if ($bSuccess) {
            // User info is ok? Here we will be connect the user and/or adding the login and registering routines...
            $oUserModel = new UserCoreModel;

            if (!$iId = $oUserModel->getId($oUserData->emails->account)) {
                // Add User if it does not exist in our database
                $this->add($oUserData, $oUserModel);

                $this->oDesign->setFlashMsg(
                    t('You have now been registered! %0%', (new Registration($this->oView))->sendMail($this->aUserInfo, true)->getMsg())
                );
                $this->sUrl = Uri::get('connect', 'main', 'register');
            } else {   // Login
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

    }

    /**
     * @param stdClass $oProfile
     * @param UserCoreModel $oUserModel
     *
     * @return void
     */
    public function add(stdClass $oProfile, UserCoreModel $oUserModel)
    {
        $oUser = new UserCore;
        $sBirthDate = $this->getBirthDate($oProfile);
        $sSex = $this->checkGender($oProfile->gender);
        $sMatchSex = $oUser->getMatchSex($sSex);
        $this->sUsername = $oUser->findUsername($oProfile->name, $oProfile->first_name, $oProfile->last_name);
        unset($oUser);

        $this->aUserInfo = [
            'email' => $oProfile->emails->account,
            'username' => $this->sUsername,
            'password' => Various::genRndWord(Registration::DEFAULT_PASSWORD_LENGTH),
            'first_name' => !empty($oProfile->first_name) ? $oProfile->first_name : '',
            'last_name' => !empty($oProfile->last_name) ? $oProfile->last_name : '',
            'sex' => $sSex,
            'match_sex' => [$sMatchSex],
            'birth_date' => (new CDateTime)->get($sBirthDate)->date(static::BIRTH_DATE_FORMAT),
            'country' => Geo::getCountryCode(),
            'city' => Geo::getCity(),
            'state' => Geo::getState(),
            'zip_code' => Geo::getZipCode(),
            'description' => '',
            'ip' => Ip::get(),
            'prefix_salt' => Various::genRnd(),
            'suffix_salt' => Various::genRnd(),
            'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
            'is_active' => DbConfig::getSetting('userActivationType')
        ];

        $this->iProfileId = $oUserModel->add($this->aUserInfo);
    }

    /**
     * Set Configuration of Microsoft OAuth API.
     *
     * @return void
     */
    private function setConfig()
    {
        $this->oClient->server = 'Microsoft';
        $this->oClient->redirect_uri = Uri::get('connect', 'main', 'login', 'google');

        $this->oClient->client_id = Config::getInstance()->values['module.api']['microsoft.client_id'];
        $this->oClient->client_secret = Config::getInstance()->values['module.api']['microsoft.client_secret_key'];
    }

    /**
     * @param stdClass $oProfile
     *
     * @return bool
     */
    private function getBirthDate(stdClass $oProfile)
    {
        return isset($oProfile->birth_month, $oProfile->birth_day, $oProfile->birth_year) ?
            $oProfile->birth_month . '/' . $oProfile->birth_day . '/' . $oProfile->birth_year :
            $this->getDefaultUserBirthDate();
    }
}
