<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7cms.com
 * @link           http://github.com/pH7Software/pH7CMS-HTTP-REST-Push-Data
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;

class UserController extends MainController
{

    protected $oUser, $oUserModel, $oValidate;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore;
        $this->oUserModel = new UserCoreModel;
        $this->oValidate = new Validate;
    }

    public function createAccount()
    {
        if ($this->oRest->getRequestMethod() != 'POST') {
            $this->oRest->response('', 406);
        } else {
            $aReqs = $this->oRest->getRequest();

            // Set the User Setting variables
            $iMinUsr = DbConfig::getSetting('minUsernameLength');
            $iMaxUsr = DbConfig::getSetting('maxUsernameLength');
            $iMinPwd = DbConfig::getSetting('minPasswordLength');
            $iMaxPwd = DbConfig::getSetting('maxPasswordLength');
            $iMinAge = DbConfig::getSetting('minAgeRegistration');
            $iMaxAge = DbConfig::getSetting('maxAgeRegistration');

            if (empty($aReqs['email']) || empty($aReqs['username']) || empty($aReqs['password']) || empty($aReqs['first_name']) ||
            empty($aReqs['last_name']) || empty($aReqs['sex']) || empty($aReqs['match_sex']) || empty($aReqs['birth_date']) || empty($aReqs['country']) ||
            empty($aReqs['city']) || empty($aReqs['state']) || empty($aReqs['zip_code']) || empty($aReqs['description']))
            {
                $this->oRest->response($this->set(array('status' => 'failed', 'msg' => t('One or several profile fields are empty.'))), 400);
            }
            elseif (!$this->oValidate->email($aReqs['email']))
            {
                $this->oRest->response($this->set(array('status' => 'form_error', 'msg' => t('The Email is not valid.'))), 400);
            }
            elseif (!$this->oValidate->username($aReqs['username'], $iMinUsr, $iMaxUsr))
            {
                $this->oRest->response($this->set(array('status' => 'form_error', 'msg' => t('The Username must contain from %0% to %1% characters, the Username is not available or it is already used by other member.', $iMinUsr, $iMaxUsr))), 400);
            }
            elseif (!$this->oValidate->password($aReqs['password'], $iMinPwd, $iMaxPwd))
            {
                $this->oRest->response($this->set(array('status' => 'form_error', 'msg' => t('The Password must contain from %0% to %1% characters.', $iMinPwd, $iMaxPwd))), 400);
            }
            elseif (!$this->oValidate->birthDate($aReqs['birth_date'], $iMinAge, $iMaxAge))
            {
                $this->oRest->response($this->set(array('status' => 'form_error', 'msg' => t('You must be %0% to %1% years to register on the site.', $iMinAge, $iMinAge))), 400);
            }
            else
            {
                $aData = [
                    'email' => $aReqs['email'],
                    'username' => $aReqs['username'],
                    'password' => $aReqs['password'],
                    'first_name' => $aReqs['first_name'],
                    'last_name' =>  $aReqs['last_name'],
                    'sex' => $aReqs['sex'],
                    'match_sex' => is_array($aReqs['match_sex']) ?: array($aReqs['match_sex']), // PHP 5.3 short ternary operator "?:"
                    'birth_date' => $this->dateTime->get($aReqs['birth_date'])->date('Y-m-d'),
                    'country' => $aReqs['country'],
                    'city' => $aReqs['city'],
                    'state' => $aReqs['state'],
                    'zip_code' => $aReqs['zip_code'],
                    'description' => $aReqs['description'],
                    'ip' => Framework\Ip\Ip::get(),
                ];

                // Add 'profile_id' key into the array
                $aData['profile_id'] = $this->oUserModel->add($aData);

                // Display the new user's details and ID
                $this->oRest->response($this->set($aData));
            }
        }
    }

    public function login()
    {
        if ($this->oRest->getRequestMethod() != 'POST') {
            $this->oRest->response('', 406);
        } else {
            $aReqs = $this->oRest->getRequest();

            if (empty($aReqs['email']) || empty($aReqs['password'])) {
                $this->oRest->response($this->set(array('status' => 'failed', 'msg' => t('The Email and/or the password is empty.'))), 400);
            } // Check Login
            elseif ($this->oUserModel->login($aReqs['email'], $aReqs['password']) === true) {
                $iId = $this->oUserModel->getId($aReqs['email']);
                $oUserData = $this->oUserModel->readProfile($iId);
                $this->oUser->setAuth($oUserData, $this->oUserModel, $this->session, new Framework\Mvc\Model\Security);

                $this->oRest->response($this->set($aReqs));
            } else {
                $this->oRest->response($this->set(array('status' => 'failed', 'msg' => t('The Password or Email was incorrected'))), 400);
            }
        }
    }

    /**
     * Get User Data.
     *
     * @param int $iId Profile ID (ID has to end with a trailing slash "/")
     * @return void
     */
    public function user($iId = null)
    {
        if ($this->oRest->getRequestMethod() != 'GET') {
            $this->oRest->response('', 406);
        } else {
            if (empty($iId)) {
                $this->oRest->response($this->set(array('status' => 'failed', 'msg' => t('Profile ID Empty'))), 400);
            } else {
                $oUser = $this->oUserModel->readProfile($iId);
                if (!empty($oUser->profileId) && $iId === $oUser->profileId) {
                    $this->oRest->response($this->set([$oUser]));
                } else {
                    $this->oRest->response($this->set(array('status' => 'failed', 'msg' => t('Profile Not Found'))), 404);
                }
            }
        }
    }

    public function users()
    {

    }

}
