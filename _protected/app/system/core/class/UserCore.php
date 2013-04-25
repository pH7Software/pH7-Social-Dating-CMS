<?php
/**
 * @title          User Core Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 * @version        1.1
 */
namespace PH7;

use
PH7\Framework\Session\Session,
PH7\Framework\Navigation\Browser,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Util\Various;

// Abstract Class
class UserCore
{

    /**
     * Users'levels.
     *
     * @return boolean
     */
    public static function auth()
    {
        $oSession = new Session;
        $oBrowser = new Browser;

        $bIsConnect = (((int)$oSession->exists('member_id')) && $oSession->get('member_ip') === Framework\Ip\Ip::get() && $oSession->get('member_http_user_agent') === $oBrowser->getUserAgent());

        /** Destruction of the object and minimize CPU resources **/
        unset($oSession, $oBrowser);

        return $bIsConnect;
    }

    /**
     * Delete User.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        if($sUsername == PH7_GHOST_USERNAME) exit('You cannot delete this profile!');

        $oFile = new Framework\File\File;
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'note/' . PH7_IMG . $sUsername);
        unset($oFile);

        (new UserCoreModel)->delete($iProfileId, $sUsername);

        /* Clean UserCoreModel and Avatar Cache */
        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, null, null)->clear()
        ->start(Framework\Layout\Html\Design::CACHE_AVATAR_GROUP . $sUsername, null, null)->clear();
    }

    /**
     * Set the avatar file and add it to the database.
     *
     * @param integer $iProfileId
     * @param integer $sUsername
     * @param string $sFile
     * @param integer $iApproved (1 = approved 0 = pending) Default 1
     * @return boolean TRUE if succes, FALSE if the extension is wrong.
     */
    public function setAvatar($iProfileId, $sUsername, $sFile, $iApproved = 1)
    {
        /**
         * This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if(!isDebug()) error_reporting(0);

        $oAvatar1 = new Framework\Image\Image($sFile, 600, 800);

        if(!$oAvatar1->validate()) return false; // File type incompatible.

        // We removes the old avatar if it exists and we delete the cache at the same time.
        $this->deleteAvatar($iProfileId, $sUsername);

        $oAvatar2 = clone $oAvatar1;
        $oAvatar3 = clone $oAvatar1;
        $oAvatar4 = clone $oAvatar1;
        $oAvatar5 = clone $oAvatar1;
        $oAvatar6 = clone $oAvatar1;
        $oAvatar7 = clone $oAvatar1;
        $oAvatar2->square(32);
        $oAvatar3->square(64);
        $oAvatar4->square(100);
        $oAvatar5->square(150);
        $oAvatar6->square(200);
        $oAvatar7->resize(400);

        /* Set watermark text on large avatars */
        $sWatermarkText = Framework\Mvc\Model\DbConfig::getSetting('watermarkTextImage');
        $iSizeWatermarkText = Framework\Mvc\Model\DbConfig::getSetting('sizeWatermarkTextImage');
        $oAvatar4->watermarkText($sWatermarkText, $iSizeWatermarkText);
        $oAvatar5->watermarkText($sWatermarkText, $iSizeWatermarkText);
        $oAvatar6->watermarkText($sWatermarkText, $iSizeWatermarkText);
        $oAvatar7->watermarkText($sWatermarkText, $iSizeWatermarkText);

        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/img/' . $sUsername . '/';
        (new Framework\File\File)->createDir($sPath);

        $sFileName = Various::genRnd($oAvatar1->getFileName(), 1);

        $sFile1 = $sFileName . '.' . $oAvatar1->getExt();  // Original, four characters
        $sFile2 = $sFileName . '-32.' . $oAvatar2->getExt();
        $sFile3 = $sFileName . '-64.' . $oAvatar3->getExt();
        $sFile4 = $sFileName . '-100.' . $oAvatar4->getExt();
        $sFile5 = $sFileName . '-150.' . $oAvatar5->getExt();
        $sFile6 = $sFileName . '-200.' . $oAvatar6->getExt();
        $sFile7 = $sFileName . '-400.' . $oAvatar7->getExt();

        // Add the avatar
        (new UserCoreModel)->setAvatar($iProfileId, $sFile1, $iApproved);

        /* Saved the new avatars */
        $oAvatar1->save($sPath . $sFile1);
        $oAvatar2->save($sPath . $sFile2);
        $oAvatar3->save($sPath . $sFile3);
        $oAvatar4->save($sPath . $sFile4);
        $oAvatar5->save($sPath . $sFile5);
        $oAvatar6->save($sPath . $sFile6);
        $oAvatar7->save($sPath . $sFile7);

        unset($oAvatar1, $oAvatar2, $oAvatar3, $oAvatar4, $oAvatar5, $oAvatar6, $oAvatar7);

        return true;
    }

    /**
     * Delete the avatar (image) and track database.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function deleteAvatar($iProfileId, $sUsername)
    {
        // We start to delete the file before the data in the database if we could not delete the file since we would have lost the link to the file found in the database.
        $sGetAvatar = (new UserCoreModel)->getAvatar($iProfileId, null);
        $sFile = $sGetAvatar->pic;

        $oFile = new Framework\File\File;
        $sExt = PH7_DOT . $oFile->getFileExt($sFile);

        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/img/' . $sUsername . '/';

        /** Array to the new format (>= PHP5.4) **/
        $aFiles = [
            $sPath . $sFile,
            $sPath . str_replace($sExt, '-32' . $sExt, $sFile),
            $sPath . str_replace($sExt, '-64' . $sExt, $sFile),
            $sPath . str_replace($sExt, '-100' . $sExt, $sFile),
            $sPath . str_replace($sExt, '-150' . $sExt, $sFile),
            $sPath . str_replace($sExt, '-200' . $sExt, $sFile),
            $sPath . str_replace($sExt, '-400' . $sExt, $sFile),
        ];

        $oFile->deleteFile($aFiles);
        unset($oFile);

        (new UserCoreModel)->deleteAvatar($iProfileId);

        /* Clean User Avatar Cache */
        (new Framework\Cache\Cache)->start(Framework\Layout\Html\Design::CACHE_AVATAR_GROUP . $sUsername, null, null)->clear()
        ->start(UserCoreModel::CACHE_GROUP, 'avatar' . $iProfileId, null)->clear();
    }

    /**
     * Set a background on user profile.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @param string $sFile
     * @param integer $iApproved (1 = approved 0 = pending) Default 1
     * @return boolean TRUE if succes, FALSE if the extension is wrong.
     */
    public function setBackground($iProfileId, $sUsername, $sFile, $iApproved = 1)
    {
        /**
         * This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if(!isDebug()) error_reporting(0);

        $oWallpaper = new Framework\Image\Image($sFile, 600, 800);

        if(!$oWallpaper->validate()) return false;

        // We removes the old background if it exists and we delete the cache at the same time.
        $this->deleteBackground($iProfileId, $sUsername);


        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/img/' . $sUsername . '/';
        (new Framework\File\File)->createDir($sPath);

        $sFileName = Various::genRnd($oWallpaper->getFileName(), 1);
        $sFile = $sFileName . '.' . $oWallpaper->getExt();

        // Add the profile background
        (new UserCoreModel)->addBackground($iProfileId, $sFile, $iApproved);

         // Saved the new background
         $oWallpaper->save($sPath . $sFile);

         unset($oWallpaper);

         return true;
    }

    /**
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function deleteBackground($iProfileId, $sUsername)
    {
         // We start to delete the file before the data in the database if we could not delete the file since we would have lost the link to the file found in the database.
        $sFile = (new UserCoreModel)->getBackground($iProfileId, null);

        (new Framework\File\File)->deleteFile(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/img/' . $sUsername . '/' . $sFile);
        (new UserCoreModel)->deleteBackground($iProfileId);

        /* Clean User Background Cache */
        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'background' . $iProfileId, null)->clear();
    }

    /**
     * Get the Profile Link.
     *
     * @param string $sUsername
     * @return string The Absolute Profile Link
     */
    public function getProfileLink($sUsername)
    {
        $oStr = new Framework\Str\Str();
        $sUsername = $oStr->lower($sUsername);
        unset($oStr);
        //return (strlen($sUsername) >1) ? PH7_URL_ROOT . $sUsername . PH7_PAGE_EXT : '#';
        return PH7_URL_ROOT . $sUsername . PH7_PAGE_EXT;
    }

    /**
     * Get Profile Link with the link to the registration form if the user is not connected.
     *
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     * @return string The link
     */
    public function getProfileSignupLink($sUsername, $sFirstName, $sSex)
    {
        if(!self::auth() && !AdminCore::auth())
        {
            $aHttpParams = [
                'ref' => (new Framework\Mvc\Request\HttpRequest)->currentController(),
                'a' => Framework\Registry\Registry::getInstance()->action,
                'u' => $sUsername,
                'f_n' => $sFirstName,
                's' => $sSex
            ];

            $sLink = UriRoute::get('user','signup','step1', '?' . Framework\Url\Url::httpBuildQuery($aHttpParams), false);
        }
        else
        {
            $sLink = $this->getProfileLink($sUsername);
        }

       return $sLink;
    }

    /**
     * Set a user authentication.
     *
     * @param \PH7\UserCoreModel $oUserModel
     * @param \PH7\Framework\Session\Session $oSession
     * @param object $oUserData User database object.
     * @return void
     */
    public function setAuth(UserCoreModel $oUserModel, Session $oSession, $oUserData)
    {
        // Is disconnected if the user is logged on as "affiliated" or "administrator".
        if(AffiliateCore::auth() || AdminCore::auth()) $oSession->destroy();

        // Regenerate the session ID to prevent the session fixation
        $oSession->regenerateId();

        // Now we connect the member
        $aSessionData = [
            'member_id' => $oUserData->profileId,
            'member_email' => $oUserData->email,
            'member_username' => $oUserData->username,
            'member_first_name' => $oUserData->firstName,
            'member_sex' => $oUserData->sex,
            'member_group_id' => $oUserData->groupId,
            'member_ip' => Framework\Ip\Ip::get(),
            'member_http_user_agent' => (new Browser)->getUserAgent(),
            'member_token' => Various::genRnd($oUserData->email)
        ];

        $oSession->set($aSessionData);

        (new Framework\Mvc\Model\Security)->addLoginLog($oUserData->email, $oUserData->username, '*****', 'Logged in!');
        $oUserModel->setLastActivity($oUserData->profileId);

        unset($oUserModel, $oUserData);
    }

    /**
     * Finds a free username in our database to use for Facebook connect.
     *
     * @param string $sNickname
     * @param string $sFirstName
     * @param string $sLastName
     * @return string Username
     */
    public function findUsername($sNickname, $sFirstName, $sLastName)
    {
        $sRnd = Various::genRnd('pH_Soria_Sanz', 4); // Random String
        $iMinLen = Framework\Mvc\Model\DbConfig::getSetting('minUsernameLength'); // Minimum Length
        $iMaxLen = PH7_MAX_USERNAME_LENGTH; // Maximum Length

        $aUsernameList = [
            $sNickname,
            $sFirstName,
            $sLastName,
            $sNickname . $sRnd,
            $sFirstName . $sRnd,
            $sLastName . $sRnd,
            Various::genRndWord($iMinLen, $iMaxLen),
            $sFirstName . '-' . $sLastName,
            $sLastName . '-' . $sFirstName,
            $sFirstName . '-' . $sLastName . $sRnd,
            $sLastName . '-' . $sFirstName . $sRnd
        ];

        foreach($aUsernameList as $sUsername)
        {
            $sUsername = substr($sUsername, 0, $iMaxLen);

            if((new Framework\Security\Validate\Validate)->username($sUsername))
                break;
            else
                $sUsername = Various::genRnd('pHO_Soria_Sanz', $iMaxLen); // Default value

        }

        return $sUsername;
    }

    /**
     * Check account status of profile.
     *
     * @param object $oDbProfileData User database object.
     * @return mixed (boolean | string) Returns a boolean TRUE if the account status is correct, otherwise returns an error message.
     */
    public function checkAccountStatus($oDbProfileData)
    {
        $mRet = true; // Default value

        if($oDbProfileData->active != 1)
        {
            if($oDbProfileData->active == 2)
            {
                $mRet = t('Sorry, your account has not yet been activated. Please activate it by clicking the activation link that was emailed.');
            }
            elseif($oDbProfileData->active == 3)
            {
                $mRet = t('Sorry, your account has not yet been activated. An administrator must validate your account.');
            }
            else
            {
                $mRet = t('Your account does not have a valid activation status. Please contact the database administrator so that it solves this problem.');
            }
        }
        elseif($oDbProfileData->ban == 1)
        {
            $mRet = t('Sorry, Your account has been banned.');
        }

        return $mRet;
    }

    /**
     * Message and Redirection for Activate Account.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sMod (user, affiliate, newsletter). Default user
     * @return void
     */
    public function activateAccount($sEmail, $sHash, $sMod = 'user')
    {
        $sTableName = Framework\Mvc\Model\Engine\Util\Various::convertModToTable($sMod);
        $sRedirectLoginUrl = ($sMod == 'newsletter' ? PH7_URL_ROOT : ($sMod == 'affiliate' ? UriRoute::get('affiliate', 'home', 'login') : UriRoute::get('user', 'main', 'login')));
        $sRedirectIndexUrl = ($sMod == 'newsletter' ? PH7_URL_ROOT : ($sMod == 'affiliate' ? UriRoute::get('affiliate', 'home', 'index') : UriRoute::get('user', 'main', 'index')));
        $sSuccessMsg = ($sMod == 'newsletter' ? t('Your subscription to our newsletters has been successfully validated!') : t('Your account has been successfully validated. You can now login!'));

        if (isset($sEmail, $sHash))
        {
            $oUserModel = new UserCoreModel;
            if ($oUserModel->validateAccount($sEmail, $sHash, $sTableName))
            {
                $iId = $oUserModel->getId($sEmail, null, $sTableName);
                if($sMod != 'newsletter') $this->clearReadProfileCache($iId, $sTableName);

                Framework\Url\HeaderUrl::redirect($sRedirectLoginUrl, $sSuccessMsg);
            }
            else
            {
                Framework\Url\HeaderUrl::redirect($sRedirectLoginUrl, t('Oops! The url is either invalid or you already have activated your account.'), 'error');
            }
            unset($oUserModel);
        }
        else
        {
            Framework\Url\HeaderUrl::redirect($sRedirectIndexUrl, t('Invalid approach, please use the link that has been send to your email.'), 'error');
        }
    }

    /**
     * This method is a wrapper for the cache of the profile of users.
     * Clean UserCoreModel / readProfile Cache
     *
     * @param integer $iId Profile ID.
     * @param string $sTable Default value "Members"
     * @return void
     */
    public function clearReadProfileCache($iId, $sTable = 'Members')
    {
        Framework\Mvc\Model\Engine\Util\Various::checkModelTable($sTable);

        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'readProfile' . $iId . $sTable, null)->clear();
    }

    /**
     * Clone is set to private to stop cloning.
     * @clone
     * @access private
     */
    private function __clone() {}

}
