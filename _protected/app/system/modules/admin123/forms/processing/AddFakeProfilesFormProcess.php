<?php
/**
 * @title          Add Fake Profiles; Process Class
 * @desc           Generate Fake Profiles from Web API.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Util\Various,
PH7\Framework\Security\Validate\Validate,
PH7\Framework\Ip\Ip,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class AddFakeProfilesFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $oExistsModel = new ExistsCoreModel;
        $oValidate = new Validate;

        $aUserData = json_decode($this->file->getFile('http://api.randomuser.me/?results=' . $this->httpRequest->post('num')), true);

        foreach($aUserData['results'] as $aUser)
        {
            $aUser = $aUser['user'];

            $sEmail = trim($aUser['email']);
            $sUsername = trim($aUser['username']);
            if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail) && $oValidate->username($sUsername))
            {
                $aData['username'] = $sUsername;
                $aData['email'] = $sEmail;
                $aData['first_name'] = $aUser['name']['first'];
                $aData['last_name'] = $aUser['name']['last'];
                $aData['password'] = $aUser['password'];
                $aData['sex'] = $aUser['gender'];
                $aData['match_sex'] = array($oUser->getMatchSex($aData['sex']));
                $aData['country'] = 'US';
                $aData['city'] = $aUser['location']['city'];
                $aData['state'] = $aUser['location']['state'];
                $aData['zip_code'] = $aUser['location']['zip'];
                $aData['birth_date'] = $this->dateTime->get($aUser['dob'])->date('Y-m-d');
                $aData['avatar'] = $aUser['picture']['large'];
                $aData['ip'] = Ip::get();

                $aData['profile_id'] = $oUserModel->add(escape($aData, true));

                $this->_addAvatar($aData, $oUser);
            }
        }

        unset($oUser, $oUserModel, $oExistsModel, $oValidate, $aUser, $aData, $aUserData);

        \PFBC\Form::setSuccess('form_add_fake_profiles', t('Users has been successfully added.'));
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
        if ($rFile = $this->file->getUrlContents($aData['avatar']))
        {
            $sTmpFile = PH7_PATH_TMP . PH7_DS . uniqid() . sha1($aData['avatar']) . '.tmp';
            $this->file->putFile($sTmpFile, $rFile);
            $oUser->setAvatar($aData['profile_id'], $aData['username'], $sTmpFile, 1);
            $this->file->deleteFile($sTmpFile);
        }
    }

}
