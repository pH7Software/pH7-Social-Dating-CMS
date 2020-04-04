<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7cms.com
 * @link           http://github.com/pH7Software/pH7CMS-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\Validate\Validate;
use Teapot\StatusCode;

class UserController extends MainController
{
    /** @var UserCore */
    protected $oUser;

    /** @var UserCoreModel */
    protected $oUserModel;

    /** @var Validate */
    protected $oValidate;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore;
        $this->oUserModel = new UserCoreModel;
        $this->oValidate = new Validate;
    }

    public function createAccount()
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_POST) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $aData = json_decode($this->oRest->getBody(), true);

            // Set the User Setting variables
            $iMinUsr = DbConfig::getSetting('minUsernameLength');
            $iMaxUsr = DbConfig::getSetting('maxUsernameLength');
            $iMinPwd = DbConfig::getSetting('minPasswordLength');
            $iMaxPwd = DbConfig::getSetting('maxPasswordLength');
            $iMinAge = DbConfig::getSetting('minAgeRegistration');
            $iMaxAge = DbConfig::getSetting('maxAgeRegistration');

            $sBirthDate = (new CDateTime)->get($aData['birth_date'])->date('m/d/Y');

            $aRequiredFields = [
                'email',
                'username',
                'password',
                'first_name',
                'last_name',
                'sex',
                'match_sex',
                'birth_date',
                'country',
                'city',
                'state',
                'zip_code',
                'description'
            ];
            if (!$this->areFieldsExist($aData, $aRequiredFields)) {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('One or several profile fields are empty.')
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->oValidate->email($aData['email'])) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('The Email is not valid.')
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->oValidate->username($aData['username'], $iMinUsr, $iMaxUsr)) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('The Username must contain from %0% to %1% characters, the Username is not available or it is already used by other member.', $iMinUsr, $iMaxUsr)
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->oValidate->password($aData['password'], $iMinPwd, $iMaxPwd)) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('The Password must contain from %0% to %1% characters.', $iMinPwd, $iMaxPwd)
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->oValidate->birthDate($sBirthDate, $iMinAge, $iMaxAge)) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('You must be %0% to %1% years to register on the site.', $iMinAge, $iMinAge)
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } else {
                $aValidData = [
                    'email' => $aData['email'],
                    'username' => $aData['username'],
                    'password' => $aData['password'],
                    'first_name' => $aData['first_name'],
                    'last_name' => $aData['last_name'],
                    'sex' => $aData['sex'],
                    'match_sex' => (array)$aData['match_sex'],
                    'birth_date' => $this->dateTime->get($aData['birth_date'])->date('Y-m-d'),
                    'country' => $aData['country'],
                    'city' => $aData['city'],
                    'state' => $aData['state'],
                    'zip_code' => $aData['zip_code'],
                    'description' => $aData['description'],
                    'ip' => Framework\Ip\Ip::get(),
                ];
                $iUserId = $this->oUserModel->add(escape($aValidData, true));

                // Add 'profile_id' key into the array
                $aValidData['profile_id'] = $iUserId;

                // Display the new user's details and ID
                $this->oRest->response($this->set($aValidData));
            }
        }
    }

    public function login()
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_POST) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $aData = json_decode($this->oRest->getBody(), true);

            if (empty($aData['email']) || empty($aData['password'])) {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('The Email and/or the password is empty.')
                ];
                $this->oRest->response($this->set([$aResults]), StatusCode::BAD_REQUEST);
            } // Check Login
            elseif ($this->oUserModel->login($aData['email'], $aData['password']) === true) {
                $iId = $this->oUserModel->getId($aData['email']);
                $oUserData = $this->oUserModel->readProfile($iId);
                $this->oUser->setAuth($oUserData, $this->oUserModel, $this->session, new SecurityModel);

                $this->oRest->response($this->set($aData));
            } else {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('Password or Email was incorrect.')
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            }
        }
    }

    /**
     * Get profile data from their ID.
     *
     * @param int $iId Profile ID (ID has to end with a trailing slash "/" when calling this resource from the API URI)
     *
     * @return void
     */
    public function user($iId)
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_GET) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            if (empty($iId)) {
                $aResults = ['status' => 'failed', 'msg' => t('Profile ID Empty')];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } else {
                $oUser = $this->oUserModel->readProfile($iId);

                if (!empty($oUser->profileId) && $iId === $oUser->profileId) {
                    $this->oRest->response($this->set([$oUser]));
                } else {
                    $aResults = ['status' => 'failed', 'msg' => t('Profile Not Found')];
                    $this->oRest->response($this->set($aResults), StatusCode::NOT_FOUND);
                }
            }
        }
    }

    /**
     * Get all profile data.
     *
     * @param string $sOrder
     * @param int|null $iOffset
     * @param int|null $iLimit
     *
     * @return void
     */
    public function users($sOrder = SearchCoreModel::LAST_ACTIVITY, $iOffset = null, $iLimit = null)
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_GET) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $oUsers = $this->oUserModel->getProfiles($sOrder, $iOffset, $iLimit);

            if (!empty($oUsers)) {
                $this->oRest->response($this->set([$oUsers]));
            } else {
                $aResults = ['status' => 'failed', 'msg' => t('No Profiles Found')];
                $this->oRest->response($this->set($aResults), StatusCode::NOT_FOUND);
            }
        }
    }

    /**
     * Get profiles from geo location.
     *
     * @param string $sCountryCode The country code. e.g. US, CA, FR, ES, BE, NL
     * @param string $sCity
     * @param string $sOrder
     * @param int|null $iOffset
     * @param int|null $iLimit
     *
     * @return void
     */
    public function usersFromLocation($sCountryCode, $sCity, $sOrder = SearchCoreModel::LAST_ACTIVITY, $iOffset = null, $iLimit = null)
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_GET) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $oUsers = $this->oUserModel->getGeoProfiles(
                $sCountryCode,
                $sCity,
                false,
                $sOrder,
                $iOffset,
                $iLimit
            );

            if (!empty($oUsers)) {
                $this->oRest->response($this->set([$oUsers]));
            } else {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('No profiles found in %1%, %0%', $sCity, $sCountryCode)
                ];
                $this->oRest->response($this->set($aResults), StatusCode::NOT_FOUND);
            }
        }
    }

    /**
     * @param int $iProfileId
     *
     * Delete a user
     */
    public function deleteUser($iProfileId)
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_DELETE) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $aResults = ['status' => 'failed', 'msg' => t('Endpoint Not Implemented Yet')];
            $this->oRest->response($this->set($aResults), StatusCode::NOT_IMPLEMENTED);
        }
    }

    /**
     * @param array $aData
     * @param array $aRequiredElements
     *
     * @return bool
     */
    private function areFieldsExist(array $aData, array $aRequiredElements)
    {
        foreach ($aRequiredElements as $sName) {
            if (empty($aData[$sName])) {
                return false;
            }
        }

        return true;
    }
}
