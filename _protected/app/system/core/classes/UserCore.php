<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Config\Config;
use PH7\Framework\Cookie\Cookie;
use PH7\Framework\File\File;
use PH7\Framework\Image\Image;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Util\Various as VariousModel;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;
use PH7\Framework\Url\Url;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class UserCore
{
    /** The prefix of the profile page URI path (eg https://mysite.com/@<USERNAME>) */
    const PROFILE_PAGE_PREFIX = '@';

    const BAN_STATUS = 1;

    const MAX_WIDTH_AVATAR = 600;
    const MAX_HEIGHT_AVATAR = 800;

    const MAX_WIDTH_BACKGROUND_IMAGE = 600;
    const MAX_HEIGHT_BACKGROUND_IMAGE = 800;

    const AVATAR2_SIZE = 32;
    const AVATAR3_SIZE = 64;
    const AVATAR4_SIZE = 100;
    const AVATAR5_SIZE = 150;
    const AVATAR6_SIZE = 200;
    const AVATAR7_SIZE = 400;

    /**
     * Check if a user is authenticated.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bSessionIpCheck = ((bool)DbConfig::getSetting('isUserSessionIpCheck')) ? $oSession->get('member_ip') === Ip::get() : true;

        $bIsLogged = $oSession->exists('member_id') &&
            $bSessionIpCheck &&
            $oSession->get('member_http_user_agent') === (new Browser)->getUserAgent();

        /** Destroy the object to minimize the CPU resources **/
        unset($oSession);

        return $bIsLogged;
    }

    /**
     * Check if an admin is logged as a user.
     *
     * @return bool
     */
    public static function isAdminLoggedAs()
    {
        return (new Session)->exists('login_user_as');
    }

    /**
     * Delete User.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     *
     * @return void
     *
     * @throws ForbiddenActionException
     */
    public function delete($iProfileId, $sUsername)
    {
        if ($this->isGhost($sUsername)) {
            throw new ForbiddenActionException('You cannot delete this profile!');
        }

        $oFile = new File;
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/' . PH7_IMG . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $sUsername);
        $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'note/' . PH7_IMG . $sUsername);
        unset($oFile);

        (new UserCoreModel)->delete($iProfileId, $sUsername);

        /* Clean UserCoreModel and Avatar Cache */
        (new Cache)
            ->start(UserCoreModel::CACHE_GROUP, null, null)->clear()
            ->start(Design::CACHE_AVATAR_GROUP . $sUsername, null, null)->clear();
    }

    /**
     * Set the avatar file and add it to the database.
     *
     * @param int $iProfileId
     * @param string $sUsername
     * @param string $sFile
     * @param int $iApproved (1 = approved | 0 = pending)
     *
     * @return bool TRUE if success, FALSE if the extension is wrong.
     *
     * @throws Framework\File\Permission\PermissionException
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function setAvatar($iProfileId, $sUsername, $sFile, $iApproved = 1)
    {
        /**
         * This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        $oAvatar1 = new Image(
            $sFile,
            self::MAX_WIDTH_AVATAR,
            self::MAX_HEIGHT_AVATAR
        );

        if (!$oAvatar1->validate()) {
            return false; // File type incompatible!
        }

        // We removes the old avatar if it exists and we delete the cache at the same time.
        $this->deleteAvatar($iProfileId, $sUsername);

        $oAvatar2 = clone $oAvatar1;
        $oAvatar3 = clone $oAvatar1;
        $oAvatar4 = clone $oAvatar1;
        $oAvatar5 = clone $oAvatar1;
        $oAvatar6 = clone $oAvatar1;
        $oAvatar7 = clone $oAvatar1;
        $oAvatar2->square(self::AVATAR2_SIZE);
        $oAvatar3->square(self::AVATAR3_SIZE);
        $oAvatar4->square(self::AVATAR4_SIZE);
        $oAvatar5->square(self::AVATAR5_SIZE);
        $oAvatar6->square(self::AVATAR6_SIZE);
        $oAvatar7->resize(self::AVATAR7_SIZE);

        /* Set watermark text on large avatars */
        $sWatermarkText = DbConfig::getSetting('watermarkTextImage');
        if (!empty(trim($sWatermarkText))) {
            $iSizeWatermarkText = DbConfig::getSetting('sizeWatermarkTextImage');
            $oAvatar4->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oAvatar5->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oAvatar6->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oAvatar7->watermarkText($sWatermarkText, $iSizeWatermarkText);
        }

        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/img/' . $sUsername . PH7_SH;
        (new File)->createDir($sPath);

        $sFileName = Various::genRnd($oAvatar1->getFileName(), 1); // Avatar filename is always 1 char-length

        $sFile1 = $sFileName . PH7_DOT . $oAvatar1->getExt();  // Original, four characters
        $sFile2 = $sFileName . '-' . self::AVATAR2_SIZE . PH7_DOT . $oAvatar2->getExt();
        $sFile3 = $sFileName . '-' . self::AVATAR3_SIZE . PH7_DOT . $oAvatar3->getExt();
        $sFile4 = $sFileName . '-' . self::AVATAR4_SIZE . PH7_DOT . $oAvatar4->getExt();
        $sFile5 = $sFileName . '-' . self::AVATAR5_SIZE . PH7_DOT . $oAvatar5->getExt();
        $sFile6 = $sFileName . '-' . self::AVATAR6_SIZE . PH7_DOT . $oAvatar6->getExt();
        $sFile7 = $sFileName . '-' . self::AVATAR7_SIZE . PH7_DOT . $oAvatar7->getExt();

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
     *
     * @return void
     */
    public function deleteAvatar($iProfileId, $sUsername)
    {
        // We start to delete the file before the data in the database if we could not delete the file since we would have lost the link to the file found in the database.
        $sGetAvatar = (new UserCoreModel)->getAvatar($iProfileId, null);
        $sFile = $sGetAvatar->pic;

        $oFile = new File;
        $sExt = PH7_DOT . $oFile->getFileExt($sFile);

        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/avatar/img/' . $sUsername . PH7_SH;

        /** Array to the new format (>= PHP5.4) **/
        $aFiles = [
            $sPath . $sFile,
            $sPath . str_replace($sExt, '-' . self::AVATAR2_SIZE . $sExt, $sFile),
            $sPath . str_replace($sExt, '-' . self::AVATAR3_SIZE . $sExt, $sFile),
            $sPath . str_replace($sExt, '-' . self::AVATAR4_SIZE . $sExt, $sFile),
            $sPath . str_replace($sExt, '-' . self::AVATAR5_SIZE . $sExt, $sFile),
            $sPath . str_replace($sExt, '-' . self::AVATAR6_SIZE . $sExt, $sFile),
            $sPath . str_replace($sExt, '-' . self::AVATAR7_SIZE . $sExt, $sFile),
        ];

        $oFile->deleteFile($aFiles);
        unset($oFile);

        (new UserCoreModel)->deleteAvatar($iProfileId);

        /* Clean User Avatar Cache */
        (new Cache)
            ->start(Design::CACHE_AVATAR_GROUP . $sUsername, null, null)->clear()
            ->start(UserCoreModel::CACHE_GROUP, 'avatar' . $iProfileId, null)->clear();
    }

    /**
     * Set a background on user profile.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @param string $sFile
     * @param int $iApproved (1 = approved | 0 = pending)
     *
     * @return bool TRUE if success, FALSE if the extension is wrong.
     *
     * @throws Framework\File\Permission\PermissionException
     */
    public function setBackground($iProfileId, $sUsername, $sFile, $iApproved = 1)
    {
        /**
         * This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        $oWallpaper = new Image(
            $sFile,
            self::MAX_WIDTH_BACKGROUND_IMAGE,
            self::MAX_HEIGHT_BACKGROUND_IMAGE
        );

        if (!$oWallpaper->validate()) {
            return false;
        }

        // We removes the old background if it exists and we delete the cache at the same time.
        $this->deleteBackground($iProfileId, $sUsername);


        $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/img/' . $sUsername . PH7_SH;
        (new File)->createDir($sPath);

        $sFileName = Various::genRnd($oWallpaper->getFileName(), 1);
        $sFile = $sFileName . PH7_DOT . $oWallpaper->getExt();

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
     *
     * @return void
     */
    public function deleteBackground($iProfileId, $sUsername)
    {
        /**
         * @internal First, we need to delete the file,
         * Second, Remove it in the database,
         * With the opposite order, we won't have the file path from the database to be able to delete the actual file.
         */
        $sFile = (new UserCoreModel)->getBackground($iProfileId, null);
        (new File)->deleteFile(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'user/background/img/' . $sUsername . PH7_SH . $sFile);
        (new UserCoreModel)->deleteBackground($iProfileId);

        /* Clean User Background Cache */
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            'background' . $iProfileId,
            null
        )->clear();
    }

    /**
     * Get the Profile Link.
     *
     * @param string $sUsername
     *
     * @return string The Absolute Profile Link
     */
    public function getProfileLink($sUsername)
    {
        $sUsername = strlen($sUsername) > 1 ? (new Str)->lower($sUsername) : PH7_GHOST_USERNAME;

        return PH7_URL_ROOT . self::PROFILE_PAGE_PREFIX . $sUsername;
    }

    /**
     * Get Profile Link with the link to the registration form if the user is not connected.
     *
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sSex
     *
     * @return string The link
     *
     * @throws Framework\File\IOException
     */
    public function getProfileSignupLink($sUsername, $sFirstName, $sSex)
    {
        if (!self::auth() && !AdminCore::auth()) {
            $aHttpParams = [
                'ref' => (new HttpRequest)->currentController(),
                'a' => Registry::getInstance()->action,
                'u' => $sUsername,
                'f_n' => $sFirstName,
                's' => $sSex
            ];

            $sLink = Uri::get(
                'user',
                'signup',
                'step1',
                '?' . Url::httpBuildQuery($aHttpParams),
                false
            );
        } else {
            $sLink = $this->getProfileLink($sUsername);
        }

        return $sLink;
    }

    /**
     * Set a user authentication.
     *
     * @param stdClass $oUserData User database object.
     * @param UserCoreModel $oUserModel
     * @param Session $oSession
     * @param SecurityModel $oSecurityModel
     *
     * @return void
     */
    public function setAuth(
        stdClass $oUserData,
        UserCoreModel $oUserModel,
        Session $oSession,
        SecurityModel $oSecurityModel)
    {
        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        // Now we connect the member
        $aSessionData = [
            'member_id' => $oUserData->profileId,
            'member_email' => $oUserData->email,
            'member_username' => $oUserData->username,
            'member_first_name' => $oUserData->firstName,
            'member_sex' => $oUserData->sex,
            'member_group_id' => $oUserData->groupId,
            'member_ip' => Ip::get(),
            'member_http_user_agent' => (new Browser)->getUserAgent(),
            'member_token' => Various::genRnd($oUserData->email)
        ];
        $oSession->set($aSessionData);

        $oSecurityModel->addLoginLog(
            $oUserData->email,
            $oUserData->username,
            '*****',
            'Logged in!'
        );
        $oSecurityModel->addSessionLog(
            $oUserData->profileId,
            $oUserData->email,
            $oUserData->firstName
        );
        $oUserModel->setLastActivity($oUserData->profileId);
    }

    /**
     * Finds a free username in our database to use for Facebook connect.
     *
     * @param string $sNickname
     * @param string $sFirstName
     * @param string $sLastName
     *
     * @return string Username
     */
    public function findUsername($sNickname, $sFirstName, $sLastName)
    {
        $iMaxLen = DbConfig::getSetting('maxUsernameLength');
        $sRnd = Various::genRnd('pH_Pierre-Henry_Soria_Sanz_González', 4); // Random String

        $aUsernameList = [
            $sNickname,
            $sFirstName,
            $sLastName,
            $sNickname . $sRnd,
            $sFirstName . $sRnd,
            $sLastName . $sRnd,
            $sFirstName . '-' . $sLastName,
            $sLastName . '-' . $sFirstName,
            $sFirstName . '-' . $sLastName . $sRnd,
            $sLastName . '-' . $sFirstName . $sRnd
        ];

        foreach ($aUsernameList as $sUsername) {
            $sUsername = substr($sUsername, 0, $iMaxLen);

            if ((new Validate)->username($sUsername)) {
                return $sUsername;
            }
        }

        // If all other usernames aren't valid, return the default below one
        return Various::genRnd('pOH_Pierre-Henry_Soria_Béghin_Rollier', $iMaxLen);
    }

    /**
     * Check account status of profile.
     *
     * @param stdClass $oDbProfileData User database object.
     *
     * @return bool|string Returns a boolean TRUE if the account status is correct, otherwise returns an error message.
     */
    public function checkAccountStatus(stdClass $oDbProfileData)
    {
        $mStatus = true; // Default value

        if ($oDbProfileData->active != RegistrationCore::NO_ACTIVATION) {
            if ($oDbProfileData->active == RegistrationCore::EMAIL_ACTIVATION) {
                $mStatus = t('Sorry, your account has not been activated yet. Please activate it by clicking on the activation link you received by email.');
            } elseif ($oDbProfileData->active == RegistrationCore::MANUAL_ACTIVATION) {
                $mStatus = t('Sorry, your account has not been activated yet. An administrator has to review it manually.');
            } else {
                $mStatus = t('Your account does not have a valid activation status. Please <a href="%0%">contact the database administrator</a> in order to fix the issue with your account.', Uri::get('contact', 'contact', 'index'));
            }
        } elseif ($oDbProfileData->ban == self::BAN_STATUS) {
            $mStatus = t('Sorry, Your account has been banned.');
        }

        return $mStatus;
    }

    /**
     * Message and Redirection for Activate Account.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param Config $oConfig
     * @param Registry $oRegistry
     * @param string $sMod (user, affiliate, newsletter).
     *
     * @return void
     *
     * @throws Framework\File\IOException
     */
    public function activateAccount($sEmail, $sHash, Config $oConfig, Registry $oRegistry, $sMod = 'user')
    {
        $sTable = VariousModel::convertModToTable($sMod);
        $sRedirectLoginUrl = ($sMod === 'newsletter' ? PH7_URL_ROOT : ($sMod === 'affiliate' ? Uri::get('affiliate', 'home', 'login') : Uri::get('user', 'main', 'login')));
        $sRedirectIndexUrl = ($sMod === 'newsletter' ? PH7_URL_ROOT : ($sMod === 'affiliate' ? Uri::get('affiliate', 'home', 'index') : Uri::get('user', 'main', 'index')));
        $sSuccessMsg = ($sMod === 'newsletter' ? t('Your subscription to our newsletters has been successfully validated!') : t('Your account has been successfully validated. You can now login!'));

        if (isset($sEmail, $sHash)) {
            $oUserModel = new AffiliateCoreModel;
            if ($oUserModel->validateAccount($sEmail, $sHash, $sTable)) {
                $iId = $oUserModel->getId($sEmail, null, $sTable);
                if ($sMod !== 'newsletter') {
                    $this->clearReadProfileCache($iId, $sTable);
                }

                /** Update the Affiliate Commission **/
                $iAffId = $oUserModel->getAffiliatedId($iId);
                AffiliateCore::updateJoinCom($iAffId, $oConfig, $oRegistry);

                Header::redirect($sRedirectLoginUrl, $sSuccessMsg);
            } else {
                Header::redirect(
                    $sRedirectLoginUrl,
                    t('Oops! The URL is either invalid or you already have activated your account.'),
                    Design::ERROR_TYPE
                );
            }
            unset($oUserModel);
        } else {
            Header::redirect(
                $sRedirectIndexUrl,
                t('Invalid approach, please use the link that has been send to your email.'),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * Get the correct matching sex.
     *
     * @param string $sSex
     *
     * @return string The Match Sex.
     */
    public function getMatchSex($sSex)
    {
        return ($sSex === GenderTypeUserCore::MALE ? GenderTypeUserCore::FEMALE : ($sSex === GenderTypeUserCore::FEMALE ? GenderTypeUserCore::MALE : GenderTypeUserCore::COUPLE));
    }

    /**
     * Logout a user.
     *
     * @param Session $oSession
     *
     * @return void
     */
    public function logout(Session $oSession)
    {
        $oSession->destroy();
        $this->revokeRememberMeSession();
    }

    /**
     * Revoke the "Remember Me" cookies (if exist) in order to completely logout the user.
     *
     * @return void
     */
    public function revokeRememberMeSession()
    {
        $oCookie = new Cookie;
        $aRememberMeCookieNames = ['member_remember', 'member_id'];

        // When "Remember Me" checkbox has been checked
        if ($oCookie->exists($aRememberMeCookieNames)) {
            $oCookie->remove($aRememberMeCookieNames);
        }
        unset($oCookie);
    }

    /**
     * This method is a wrapper for the cache of the profile of users.
     * Clean UserCoreModel / readProfile Cache
     *
     * @param integer $iId Profile ID.
     * @param string $sTable Default DbTableName::MEMBER
     *
     * @return void
     */
    public function clearReadProfileCache($iId, $sTable = DbTableName::MEMBER)
    {
        $this->clearCache('readProfile', $iId, $sTable);
    }

    /**
     * This method is a wrapper for the Info Fields cache.
     * Clean UserCoreModel / infoFields Cache
     *
     * @param integer $iId Profile ID.
     * @param string $sTable Default DbTableName::MEMBER_INFO
     *
     * @return void
     */
    public function clearInfoFieldCache($iId, $sTable = DbTableName::MEMBER_INFO)
    {
        $this->clearCache('infoFields', $iId, $sTable);
    }

    /**
     * @param string $sUsername
     *
     * @return bool
     */
    private function isGhost($sUsername)
    {
        return $sUsername === PH7_GHOST_USERNAME;
    }

    /**
     * Generic method to clear the user cache.
     *
     * @param string $sId Cache ID.
     * @param integer $iId User ID.
     * @param string $sTable Table name.
     *
     * @return void
     */
    private function clearCache($sId, $iId, $sTable)
    {
        VariousModel::checkModelTable($sTable);

        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            $sId . $iId . $sTable,
            null
        )->clear();
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
