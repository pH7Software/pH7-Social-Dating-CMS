<?php
/**
 * @title          Add Fake Profiles; Process Class
 * @desc           Generate Fake Profiles from Web API.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Geo\Misc\Country;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Url\Url;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class AddFakeProfilesFormProcess extends Form
{
    const SERVICE_NAME = 'RandomUser';
    const SERVICE_URL = 'https://randomuser.me';
    const API_URL = 'https://randomuser.me/api/';
    const API_VER = '1.3';

    /** @var Validate */
    private $oValidate;

    /** @var ExistsCoreModel */
    private $oExistsModel;

    /** @var int */
    private static $iTotalGenerated = 0;

    public function __construct()
    {
        parent::__construct();

        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $this->oExistsModel = new ExistsCoreModel;
        $this->oValidate = new Validate;

        $aUserData = $this->getApiClient()['results'];
        if (!empty($aUserData) && is_array($aUserData)) {
            foreach ($aUserData as $aUser) {
                $sEmail = trim($aUser['email']);
                $sUsername = trim($aUser['login']['username']);

                if ($this->isValidProfile($sEmail, $sUsername)) {
                    self::$iTotalGenerated++;
                    $aData = $this->storeUserDataIntoArray($sUsername, $sEmail, $aUser, $oUser);
                    $aData['profile_id'] = $oUserModel->add(escape($aData, true));
                    $this->addAvatar($aData, $oUser);
                }
            }

            if (self::$iTotalGenerated > 0) {
                \PFBC\Form::setSuccess(
                    'form_add_fake_profiles',
                    nt('%n% user has successfully been added.', '%n% users have successfully been added.', self::$iTotalGenerated)
                );
            } else {
                \PFBC\Form::setError(
                    'form_add_fake_profiles',
                    t('None of the received fake profiles were valid for the system. Please try again.')
                );
            }
        } else {
            \PFBC\Form::setError(
                'form_add_fake_profiles',
                t('An error occurred when requesting user data to %0%. Maybe, you could try again later.', self::API_URL)
            );
        }

        unset($oUser, $oUserModel, $aData);
    }

    protected function getApiClient()
    {
        $sApiUrl = static::API_URL;
        $sApiParams = '?' . Url::httpBuildQuery($this->getApiParameters(), null, '&');
        $sApiVer = static::API_VER;
        $rUserData = $this->getApiResults($sApiUrl, $sApiParams, $sApiVer);

        return json_decode($rUserData, true);
    }

    private function getApiParameters()
    {
        return [
            'results' => $this->httpRequest->post('num'),
            'gender' => $this->httpRequest->post('sex'),
            'nat' => $this->httpRequest->post('nat'),
            'noinfo' => 1
        ];
    }

    /**
     * Get Data from the third-party API.
     *
     * @param string $sApiUrl API URL.
     * @param string $sApiParams Parameters to send to the API.
     * @param string $sApiVersion Version of the API it will use. If fails from the API server, it will ignore it.
     *
     * @return void
     */
    private function getApiResults($sApiUrl, $sApiParams, $sApiVersion)
    {
        if ($rData = $this->file->getFile($sApiUrl . PH7_SH . $sApiVersion . PH7_SH . $sApiParams)) {
            return $rData;
        }

        return $this->file->getFile($sApiUrl . PH7_SH . $sApiParams);
    }

    /**
     * Add User's Avatar.
     *
     * @param array $aData
     * @param UserCore $oUser
     *
     * @return void
     */
    private function addAvatar(array $aData, UserCore $oUser)
    {
        /**
         * Sometimes, cURL fails under Windows or some other specific server configs,
         * for this reason, we use `file_get_contents()` as fallback when cURL fails.
         */
        if (!$rFile = $this->file->getUrlContents($aData['avatar'])) {
            $rFile = $this->file->getFile($aData['avatar']);
        }

        // Create a temporary file before creating the avatar images
        $sUniqIdPrefix = (string)mt_rand();
        $sTmpFile = PH7_PATH_TMP . PH7_DS . uniqid($sUniqIdPrefix, true) . sha1($aData['avatar']) . '.tmp';
        $this->file->putFile($sTmpFile, $rFile);

        // Create different avatar sizes, save them and set the avatar into the DB
        $oUser->setAvatar($aData['profile_id'], $aData['username'], $sTmpFile, 1);

        // Remove the temporary file since we don't need it anymore
        $this->file->deleteFile($sTmpFile);
    }

    /**
     * @param string $sUsername
     * @param string $sEmail
     * @param array $aUser
     * @param UserCore $oUser
     *
     * @return array
     */
    private function storeUserDataIntoArray($sUsername, $sEmail, array $aUser, UserCore $oUser)
    {
        $aData = [];
        $aData['username'] = $sUsername;
        $aData['email'] = $sEmail;
        $aData['first_name'] = $this->str->upperFirst($aUser['name']['first']);
        $aData['last_name'] = $this->str->upperFirst($aUser['name']['last']);
        $aData['password'] = $aUser['login']['password'];
        $aData['sex'] = $aUser['gender'];
        $aData['match_sex'] = [$oUser->getMatchSex($aData['sex'])];
        $aData['country'] = Country::fixCode($aUser['nat']);
        $aData['city'] = $this->str->upperFirst($aUser['location']['city']);
        $aData['state'] = $this->str->upperFirst($aUser['location']['state']);
        $aData['address'] = $this->str->upperFirstWords($aUser['location']['street']);
        $aData['zip_code'] = $aUser['location']['postcode'];
        $aData['birth_date'] = $this->dateTime->get($aUser['dob']['date'])->date('Y-m-d');
        $aData['avatar'] = $aUser['picture']['large'];
        $aData['ip'] = Ip::get();

        return $aData;
    }

    /**
     * @param string $sEmail
     * @param string $sUsername
     *
     * @return bool
     */
    private function isValidProfile($sEmail, $sUsername)
    {
        return $this->oValidate->email($sEmail) &&
            !$this->oExistsModel->email($sEmail) &&
            $this->oValidate->username($sUsername);
    }
}
