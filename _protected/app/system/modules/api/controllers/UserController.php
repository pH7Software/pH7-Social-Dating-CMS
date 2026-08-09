<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @see           https://ph7builder.com
 * @see           https://github.com/pH7Software/pH7Builder-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\Validate\Validate;
use PH7\JustHttp\StatusCode;

class UserController extends MainController
{
    protected UserCore $oUser;
    protected UserCoreModel $oUserModel;
    protected Validate $oValidate;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore();
        $this->oUserModel = new UserCoreModel();
        $this->oValidate = new Validate();
    }

    public function createAccount(): void
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_POST) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $mData = json_decode($this->oRest->getBody(), true);

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

            if (!$this->areFieldsExist($mData, $aRequiredFields)) {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('One or several profile fields are empty.')
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);

                return;
            }

            $aData = $this->normalizeFields($mData, $aRequiredFields);

            // Set the User Setting variables
            $iMinUsr = DbConfig::getSetting('minUsernameLength');
            $iMaxUsr = DbConfig::getSetting('maxUsernameLength');
            $iMinPwd = DbConfig::getSetting('minPasswordLength');
            $iMaxPwd = DbConfig::getSetting('maxPasswordLength');
            $iMinAge = DbConfig::getSetting('minAgeRegistration');
            $iMaxAge = DbConfig::getSetting('maxAgeRegistration');

            $sBirthDate = Validate::normalizeBirthDate($aData['birth_date']);

            if (!$this->oValidate->email($aData['email'])) {
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
            } elseif ($sBirthDate === null) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('The birth date is invalid. Use YYYY-MM-DD or MM/DD/YYYY.')
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->oValidate->birthDate($sBirthDate, $iMinAge, $iMaxAge)) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('You must be %0% to %1% years to register on the site.', $iMinAge, $iMaxAge)
                ];
                $this->oRest->response($this->set($aResults), StatusCode::BAD_REQUEST);
            } elseif (!$this->areProfileFieldsValid($aData)) {
                $aResults = [
                    'status' => 'form_error',
                    'msg' => t('Profile fields are invalid. Names: 2-20 characters; city/state: 2-150; postal code: 2-15; description: 20-4000. Use available gender and country values.')
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
                    'birth_date' => $sBirthDate,
                    'country' => $aData['country'],
                    'city' => $aData['city'],
                    'state' => $aData['state'],
                    'zip_code' => $aData['zip_code'],
                    'description' => $aData['description'],
                    'ip' => Framework\Ip\Ip::get(),
                ];
                try {
                    $iUserId = $this->oUserModel->add(escape($aValidData, true));
                } catch (\Throwable $oException) {
                    error_log(sprintf('API account creation failed: %s', $oException->getMessage()));
                    $aResults = [
                        'status' => 'failed',
                        'msg' => t('The account could not be created. Please verify the details and try again.')
                    ];
                    $this->oRest->response($this->set($aResults), StatusCode::INTERNAL_SERVER_ERROR);

                    return;
                }

                $aValidData['profile_id'] = $iUserId;

                $this->oRest->response($this->set($aValidData));
            }
        }
    }

    public function login(): void
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_POST) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $mData = json_decode($this->oRest->getBody(), true);
            $aRequiredFields = ['email', 'password'];

            if (!$this->areFieldsExist($mData, $aRequiredFields)) {
                $aResults = [
                    'status' => 'failed',
                    'msg' => t('The Email and/or the password is empty.')
                ];
                $this->oRest->response($this->set([$aResults]), StatusCode::BAD_REQUEST);

                return;
            }

            $aData = $this->normalizeFields($mData, $aRequiredFields);

            // Check Login
            if ($this->oUserModel->login($aData['email'], $aData['password']) === true) {
                $iId = $this->oUserModel->getId($aData['email']);
                $oUserData = $this->oUserModel->readProfile($iId);
                $this->oUser->setAuth($oUserData, $this->oUserModel, $this->session, new SecurityModel());

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
     */
    public function user($iId): void
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
     */
    public function users(
        string $sOrder = SearchCoreModel::LAST_ACTIVITY,
        ?int $iOffset = null,
        ?int $iLimit = null): void
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
     */
    public function usersFromLocation(
        string $sCountryCode,
        string $sCity,
        string $sOrder = SearchCoreModel::LAST_ACTIVITY,
        ?int $iOffset = null,
        ?int $iLimit = null): void
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
     * Delete a user.
     */
    public function deleteUser(int $iProfileId): void
    {
        if ($this->oRest->getRequestMethod() !== HttpRequest::METHOD_DELETE) {
            $this->oRest->response('', StatusCode::NOT_ACCEPTABLE);
        } else {
            $aResults = ['status' => 'failed', 'msg' => t('Endpoint Not Implemented Yet')];
            $this->oRest->response($this->set($aResults), StatusCode::NOT_IMPLEMENTED);
        }
    }

    private function areFieldsExist(mixed $mData, array $aRequiredElements): bool
    {
        if (!is_array($mData)) {
            return false;
        }

        foreach ($aRequiredElements as $sName) {
            if (empty($mData[$sName])) {
                return false;
            }

            if ($sName === 'match_sex') {
                foreach ((array)$mData[$sName] as $mMatchSex) {
                    if (!is_scalar($mMatchSex) || trim((string)$mMatchSex) === '') {
                        return false;
                    }
                }
            } elseif (!is_scalar($mData[$sName])) {
                return false;
            }
        }

        return true;
    }

    private function normalizeFields(array $aData, array $aFieldNames): array
    {
        foreach ($aFieldNames as $sName) {
            if ($sName === 'match_sex') {
                $aData[$sName] = array_map(
                    static function ($mValue): string {
                        return (string)$mValue;
                    },
                    (array)$aData[$sName]
                );
            } else {
                $aData[$sName] = (string)$aData[$sName];
            }
        }

        return $aData;
    }

    private function areProfileFieldsValid(array $aData): bool
    {
        $aAllowedCountryCodes = array_map(
            static function (\stdClass $oCountry): string {
                return (string)$oCountry->countryCode;
            },
            $this->oUserModel->getCountries()
        );

        return (new UserSignupInputValidator($this->oValidate))->isValid($aData, $aAllowedCountryCodes);
    }
}
