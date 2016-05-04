<?php
/**
 * @title          Add Fake Profiles; Process Class
 * @desc           Generate Fake Profiles from Web API.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Security\Validate\Validate, PH7\Framework\Ip\Ip;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class AddFakeProfilesFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $oExistsModel = new ExistsCoreModel;
        $oValidate = new Validate;

        $iUserNum = $this->httpRequest->post('num');
        $sSex = $this->httpRequest->post('sex');
        $sNationality = $this->httpRequest->post('nat');
        $aUserData = json_decode($this->file->getFile('http://api.randomuser.me/1.0/?results=' . $iUserNum . '&gender=' . $sSex . '&nat=' . $sNationality . '&noinfo=1'), true);

        foreach ($aUserData['results'] as $aUser)
        {
            $sEmail = trim($aUser['email']);
            $sUsername = trim($aUser['login']['username']);
            if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail) && $oValidate->username($sUsername))
            {
                $aData['username'] = $sUsername;
                $aData['email'] = $sEmail;
                $aData['first_name'] = $aUser['name']['first'];
                $aData['last_name'] = $aUser['name']['last'];
                $aData['password'] = $aUser['login']['password'];
                $aData['sex'] = $aUser['gender'];
                $aData['match_sex'] = array($oUser->getMatchSex($aData['sex']));
                $aData['country'] = $aUser['nat'];
                $aData['city'] = $aUser['location']['city'];
                $aData['state'] = $aUser['location']['state'];
                $aData['zip_code'] = $aUser['location']['postcode'];
                $aData['birth_date'] = $this->dateTime->get($aUser['dob'])->date('Y-m-d');
                $aData['avatar'] = $aUser['picture']['large'];
                $aData['ip'] = Ip::get();

                $aData['profile_id'] = $oUserModel->add(escape($aData, true));

                $this->_addAvatar($aData, $oUser);
            }
        }

        unset($oUser, $oUserModel, $oExistsModel, $oValidate, $aUser, $aData, $aUserData);

        \PFBC\Form::setSuccess('form_add_fake_profiles', nt('%n% user has successfully been added.', '%n% users have successfully been added.', $iUserNum));
    }

    /**
     * Add User's Avatar.
     *
     * @param array $aData
     * @param object \PH7\UserCore $oUser
     *
     * @return void
     */
    private function _addAvatar(array $aData, UserCore $oUser)
    {
        // Sometime, cURL returns FALSE and doesn't work at all under Windowns server or some other specific server config, so use file_get_contents() instead as it will work.
        if (!$rFile = $this->file->getUrlContents($aData['avatar'])) {
            $rFile = $this->file->getFile($aData['avatar']);
        }

        // Create a temporary file before creating the avatar images
        $sTmpFile = PH7_PATH_TMP . PH7_DS . uniqid() . sha1($aData['avatar']) . '.tmp';
        $this->file->putFile($sTmpFile, $rFile);

        $oUser->setAvatar($aData['profile_id'], $aData['username'], $sTmpFile, 1); // Create the different avatar sizes and set the avatar
        $this->file->deleteFile($sTmpFile);// Remove the temporary file as we don't need it anymore
    }

}
