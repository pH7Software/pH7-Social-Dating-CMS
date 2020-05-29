<?php
/**
 * @title          User Core Model Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\CArray\ObjArr;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Security\Security;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;
use PH7\Framework\Translate\Lang;
use stdClass;

// Abstract Class
class UserCoreModel extends Model
{
    /**
     * Cache lifetime set to 1 week.
     */
    const CACHE_TIME = 604800;

    const CACHE_GROUP = 'db/sys/mod/user';

    const HASH_VALIDATION_LENGTH = 40;

    const OFFLINE_STATUS = 0;
    const ONLINE_STATUS = 1;
    const BUSY_STATUS = 2;
    const AWAY_STATUS = 3;

    const VISITOR_GROUP = 1;
    const PENDING_GROUP = 9;

    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** @var string */
    protected $sCurrentDate;

    /** @var string */
    protected $iProfileId;

    public function __construct()
    {
        parent::__construct();

        $this->sCurrentDate = (new CDateTime)->get()->dateTime(self::DATETIME_FORMAT);
        $this->iProfileId = (new Session)->get('member_id');
    }

    /**
     * @param Session $oSession
     *
     * @return stdClass
     */
    public function checkGroup(Session $oSession)
    {
        // Set default group ID if no user is logged in (and so, 'member_group_id' session doesn't exist)
        if (!$oSession->exists('member_group_id')) {
            $oSession->regenerateId();
            $oSession->set('member_group_id', PermissionCore::VISITOR_GROUP_ID);
        }
        $iMemberGroupId = (int)$oSession->get('member_group_id');

        $this->cache->start(
            self::CACHE_GROUP,
            'membership_groups' . $iMemberGroupId,
            static::CACHE_TIME
        );

        if (!$oPermissions = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare(
                'SELECT permissions FROM' . Db::prefix(DbTableName::MEMBERSHIP) .
                'WHERE groupId = :groupId LIMIT 1'
            );
            $rStmt->bindValue(':groupId', $iMemberGroupId, PDO::PARAM_INT);
            $rStmt->execute();
            $sPermissions = $rStmt->fetchColumn();
            Db::free($rStmt);
            $oPermissions = ObjArr::toObject(unserialize($sPermissions));
            $this->cache->put($oPermissions);
        }

        return $oPermissions;
    }

    /**
     * Login method for Members and Affiliate, but not for Admins since it has another method PH7\AdminModel::adminLogin() even more secure.
     *
     * @param string $sEmail Not case sensitive since on lot of mobile devices (such as iPhone), the first letter is uppercase.
     * @param string $sPassword
     * @param string $sTable
     *
     * @return bool|string (boolean "true" or string "message")
     */
    public function login($sEmail, $sPassword, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'SELECT email, password FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1'
        );
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
        Db::free($rStmt);

        $sDbEmail = !empty($oRow->email) ? $oRow->email : '';
        $sDbPassword = !empty($oRow->password) ? $oRow->password : '';

        if (strtolower($sEmail) !== strtolower($sDbEmail)) {
            return CredentialStatusCore::INCORRECT_EMAIL_IN_DB;
        }
        if (!Security::checkPwd($sPassword, $sDbPassword)) {
            return CredentialStatusCore::INCORRECT_PASSWORD_IN_DB;
        }

        return true;
    }

    /**
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return string The latest used user's IP address.
     */
    public function getLastUsedIp($iProfileId, $sTable = DbTableName::MEMBER_LOG_SESS)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT ip FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId ORDER BY dateTime DESC LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->execute();
        $sLastUsedIp = $rStmt->fetchColumn();
        Db::free($rStmt);

        return $sLastUsedIp;
    }

    /**
     * Read Profile Data.
     *
     * @param int $iProfileId The user ID
     * @param string $sTable
     *
     * @return stdClass|bool The data of a member if exists, FALSE otherwise.
     */
    public function readProfile($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'readProfile' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Get the total number of members.
     *
     * @param string $sTable
     * @param int $iDay
     * @param string $sGender Values ​​available 'all', 'male', 'female'. 'couple' is only available to Members.
     *
     * @return int Total Users
     */
    public function total($sTable = DbTableName::MEMBER, $iDay = 0, $sGender = 'all')
    {
        Various::checkModelTable($sTable);

        $iDay = (int)$iDay;
        $bIsDay = ($iDay > 0);

        if ($sTable === DbTableName::MEMBER) {
            $bIsGender = GenderTypeUserCore::isGenderValid($sGender);
        } else {
            $bIsGender = GenderTypeUserCore::isGenderValid($sGender, GenderTypeUserCore::IGNORE_COUPLE_GENDER);
        }

        $sSqlDay = $bIsDay ? ' AND (joinDate + INTERVAL :day DAY) > NOW()' : '';
        $sSqlGender = $bIsGender ? ' AND sex = :gender' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) FROM' . Db::prefix($sTable) . 'WHERE username <> :ghostUsername' . $sSqlDay . $sSqlGender);
        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        if ($bIsDay) {
            $rStmt->bindValue(':day', $iDay, PDO::PARAM_INT);
        }
        if ($bIsGender) {
            $rStmt->bindValue(':gender', $sGender, PDO::PARAM_STR);
        }
        $rStmt->execute();

        $iTotalUsers = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalUsers;
    }

    /**
     * Update profile data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param int $iProfileId Profile ID
     * @param string $sTable
     *
     * @return void
     */
    public function updateProfile($sSection, $sValue, $iProfileId, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, $sSection, $sValue, 'profileId', $iProfileId);
    }

    /**
     * Update Privacy setting data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param int $iProfileId Profile ID
     *
     * @return void
     */
    public function updatePrivacySetting($sSection, $sValue, $iProfileId)
    {
        $this->orm->update(
            DbTableName::MEMBER_PRIVACY,
            $sSection,
            $sValue,
            'profileId',
            $iProfileId
        );
    }

    /**
     * Change password of a member.
     *
     * @param string $sEmail
     * @param string $sNewPassword
     * @param string $sTable
     *
     * @return bool
     */
    public function changePassword($sEmail, $sNewPassword, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix($sTable) . 'SET password = :newPassword WHERE email = :email LIMIT 1'
        );
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindValue(':newPassword', Security::hashPwd($sNewPassword), PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Set a new hash validation.
     *
     * @param int $iProfileId
     * @param string $sHash
     * @param string $sTable
     *
     * @return bool
     */
    public function setNewHashValidation($iProfileId, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET hashValidation = :hash WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindParam(':hash', $sHash, PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);

        return $rStmt->execute();
    }

    /**
     * Check the hash validation.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable
     *
     * @return bool
     */
    public function checkHashValidation($sEmail, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'SELECT COUNT(profileId) FROM' . Db::prefix($sTable) . 'WHERE email = :email AND hashValidation = :hash LIMIT 1'
        );
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindParam(':hash', $sHash, PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);
        $rStmt->execute();

        return $rStmt->fetchColumn() == 1;
    }

    /**
     * Search users.
     *
     * @param array $aParams
     * @param bool $bCount
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array|int Object for the users list returned or integer for the total number users returned.
     */
    public function search(array $aParams, $bCount, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $bIsMail = !empty($aParams[SearchQueryCore::EMAIL]) && Str::noSpaces($aParams[SearchQueryCore::EMAIL]);
        $bIsFirstName = !$bIsMail && !empty($aParams[SearchQueryCore::FIRST_NAME]) && Str::noSpaces($aParams[SearchQueryCore::FIRST_NAME]);
        $bIsMiddleName = !$bIsMail && !empty($aParams[SearchQueryCore::MIDDLE_NAME]) && Str::noSpaces($aParams[SearchQueryCore::MIDDLE_NAME]);
        $bIsLastName = !$bIsMail && !empty($aParams[SearchQueryCore::LAST_NAME]) && Str::noSpaces($aParams[SearchQueryCore::LAST_NAME]);
        $bIsSingleAge = !$bIsMail && !empty($aParams[SearchQueryCore::AGE]);
        $bIsAge = !$bIsMail && empty($aParams[SearchQueryCore::AGE]) && !empty($aParams[SearchQueryCore::MIN_AGE]) && !empty($aParams[SearchQueryCore::MAX_AGE]);
        $bIsHeight = !$bIsMail && !empty($aParams[SearchQueryCore::HEIGHT]);
        $bIsWeight = !$bIsMail && !empty($aParams[SearchQueryCore::WEIGHT]);
        $bIsCountry = !$bIsMail && !empty($aParams[SearchQueryCore::COUNTRY]) && Str::noSpaces($aParams[SearchQueryCore::COUNTRY]);
        $bIsCity = !$bIsMail && !empty($aParams[SearchQueryCore::CITY]) && Str::noSpaces($aParams[SearchQueryCore::CITY]);
        $bIsState = !$bIsMail && !empty($aParams[SearchQueryCore::STATE]) && Str::noSpaces($aParams[SearchQueryCore::STATE]);
        $bIsZipCode = !$bIsMail && !empty($aParams[SearchQueryCore::ZIP_CODE]) && Str::noSpaces($aParams[SearchQueryCore::ZIP_CODE]);
        $bIsSex = !$bIsMail && !empty($aParams[SearchQueryCore::SEX]) && is_array($aParams[SearchQueryCore::SEX]);
        $bIsMatchSex = !$bIsMail && !empty($aParams[SearchQueryCore::MATCH_SEX]);
        $bIsOnline = !$bIsMail && !empty($aParams[SearchQueryCore::ONLINE]);
        $bIsAvatar = !$bIsMail && !empty($aParams[SearchQueryCore::AVATAR]);
        $bHideUserLogged = !$bIsMail && !empty($this->iProfileId);

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(m.profileId)';
        $sSqlFirstName = $bIsFirstName ? ' AND LOWER(firstName) LIKE LOWER(:firstName)' : '';
        $sSqlMiddleName = $bIsMiddleName ? ' AND LOWER(middleName) LIKE LOWER(:middleName)' : '';
        $sSqlLastName = $bIsLastName ? ' AND LOWER(lastName) LIKE LOWER(:lastName)' : '';
        $sSqlSingleAge = $bIsSingleAge ? ' AND birthDate LIKE :birthDate ' : '';
        $sSqlAge = $bIsAge ? ' AND birthDate BETWEEN DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :maxAge YEAR) AND DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :minAge YEAR) ' : '';
        $sSqlHeight = $bIsHeight ? ' AND height = :height ' : '';
        $sSqlWeight = $bIsWeight ? ' AND weight = :weight ' : '';
        $sSqlCountry = $bIsCountry ? ' AND country = :country ' : '';
        $sSqlCity = $bIsCity ? ' AND LOWER(city) LIKE LOWER(:city) ' : '';
        $sSqlState = $bIsState ? ' AND LOWER(state) LIKE LOWER(:state) ' : '';
        $sSqlZipCode = $bIsZipCode ? ' AND LOWER(zipCode) LIKE LOWER(:zipCode) ' : '';
        $sSqlEmail = $bIsMail ? ' AND email LIKE :email ' : '';
        $sSqlOnline = $bIsOnline ? ' AND userStatus = :userStatus AND lastActivity > DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL ' . DbConfig::getSetting('userTimeout') . ' MINUTE) ' : '';
        $sSqlAvatar = $bIsAvatar ? $this->getUserWithAvatarOnlySql() : '';
        $sSqlHideLoggedProfile = $bHideUserLogged ? ' AND (m.profileId <> :profileId)' : '';
        $sSqlMatchSex = $bIsMatchSex ? ' AND FIND_IN_SET(:matchSex, matchSex)' : '';

        $sSqlSex = '';
        if ($bIsSex) {
            $sSqlSex = $this->getSexInClauseSql($aParams[SearchQueryCore::SEX]);
        }

        if (empty($aParams[SearchQueryCore::ORDER])) {
            $aParams[SearchQueryCore::ORDER] = SearchCoreModel::LATEST; // Default is "ORDER BY joinDate"
        }
        if (empty($aParams[SearchQueryCore::SORT])) {
            $aParams[SearchQueryCore::SORT] = SearchCoreModel::DESC; // Default is "descending"
        }
        $sSqlOrder = SearchCoreModel::order($aParams[SearchQueryCore::ORDER], $aParams[SearchQueryCore::SORT]);

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m LEFT JOIN' . Db::prefix(DbTableName::MEMBER_PRIVACY) . 'AS p USING(profileId)
            LEFT JOIN' . Db::prefix(DbTableName::MEMBER_INFO) . 'AS i USING(profileId) WHERE username <> :ghostUsername AND searchProfile = \'yes\'
            AND (groupId <> :visitorGroup) AND (groupId <> :pendingGroup) AND (ban = 0)' . $sSqlHideLoggedProfile . $sSqlFirstName . $sSqlMiddleName . $sSqlLastName . $sSqlMatchSex . $sSqlSex . $sSqlSingleAge . $sSqlAge . $sSqlCountry . $sSqlCity . $sSqlState .
            $sSqlZipCode . $sSqlHeight . $sSqlWeight . $sSqlEmail . $sSqlOnline . $sSqlAvatar . $sSqlOrder . $sSqlLimit
        );

        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        $rStmt->bindValue(':visitorGroup', self::VISITOR_GROUP, PDO::PARAM_INT);
        $rStmt->bindValue(':pendingGroup', self::PENDING_GROUP, PDO::PARAM_INT);

        if ($bIsMatchSex) {
            $rStmt->bindValue(':matchSex', $aParams[SearchQueryCore::MATCH_SEX], PDO::PARAM_STR);
        }
        if ($bIsFirstName) {
            $rStmt->bindValue(':firstName', '%' . $aParams[SearchQueryCore::FIRST_NAME] . '%', PDO::PARAM_STR);
        }
        if ($bIsMiddleName) {
            $rStmt->bindValue(':middleName', '%' . $aParams[SearchQueryCore::MIDDLE_NAME] . '%', PDO::PARAM_STR);
        }
        if ($bIsLastName) {
            $rStmt->bindValue(':lastName', '%' . $aParams[SearchQueryCore::LAST_NAME] . '%', PDO::PARAM_STR);
        }
        if ($bIsSingleAge) {
            $rStmt->bindValue(':birthDate', '%' . $aParams[SearchQueryCore::AGE] . '%', PDO::PARAM_STR);
        }
        if ($bIsAge) {
            $rStmt->bindValue(':minAge', $aParams[SearchQueryCore::MIN_AGE], PDO::PARAM_INT);
            $rStmt->bindValue(':maxAge', $aParams[SearchQueryCore::MAX_AGE], PDO::PARAM_INT);
        }
        if ($bIsHeight) {
            $rStmt->bindValue(':height', $aParams[SearchQueryCore::HEIGHT], PDO::PARAM_INT);
        }
        if ($bIsWeight) {
            $rStmt->bindValue(':weight', $aParams[SearchQueryCore::WEIGHT], PDO::PARAM_INT);
        }
        if ($bIsCountry) {
            $rStmt->bindParam(':country', $aParams[SearchQueryCore::COUNTRY], PDO::PARAM_STR, 2);
        }
        if ($bIsCity) {
            $rStmt->bindValue(':city', '%' . str_replace('-', ' ', $aParams[SearchQueryCore::CITY]) . '%', PDO::PARAM_STR);
        }
        if ($bIsState) {
            $rStmt->bindValue(':state', '%' . str_replace('-', ' ', $aParams[SearchQueryCore::STATE]) . '%', PDO::PARAM_STR);
        }
        if ($bIsZipCode) {
            $rStmt->bindValue(':zipCode', '%' . $aParams[SearchQueryCore::ZIP_CODE] . '%', PDO::PARAM_STR);
        }
        if ($bIsMail) {
            $rStmt->bindValue(':email', '%' . $aParams[SearchQueryCore::EMAIL] . '%', PDO::PARAM_STR);
        }
        if ($bIsOnline) {
            $rStmt->bindValue(':userStatus', self::ONLINE_STATUS, PDO::PARAM_INT);
        }
        if ($bHideUserLogged) {
            $rStmt->bindValue(':profileId', $this->iProfileId, PDO::PARAM_INT);
        }
        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);

            return $aRow;
        }

        $iTotalUsers = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalUsers;
    }

    /**
     * Check online status.
     *
     * @param int $iProfileId
     * @param int $iTimeout Number of minutes when a user becomes inactive (offline).
     *
     * @return bool
     */
    public function isOnline($iProfileId, $iTimeout = 1)
    {
        $iProfileId = (int)$iProfileId;
        $iTimeout = (int)$iTimeout;

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = :profileId
            AND userStatus = :userStatus AND lastActivity >= DATE_SUB(:currentTime, INTERVAL :time MINUTE) LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':userStatus', self::ONLINE_STATUS, PDO::PARAM_INT);
        $rStmt->bindValue(':time', $iTimeout, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $this->sCurrentDate, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 1;
    }

    /**
     * Set the user status.
     *
     * @param int iProfileId
     * @param int $iStatus Values: 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
     *
     * @return void
     */
    public function setUserStatus($iProfileId, $iStatus)
    {
        $this->orm->update(DbTableName::MEMBER, 'userStatus', $iStatus, 'profileId', $iProfileId);
    }

    /**
     * Get the user status.
     *
     * @param int $iProfileId
     *
     * @return int The user status. 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
     */
    public function getUserStatus($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'userStatus' . $iProfileId, static::CACHE_TIME);

        if (!$iUserStatus = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT userStatus FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $iUserStatus = (int)$rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($iUserStatus);
        }

        return $iUserStatus;
    }

    /**
     * Update the notifications.
     *
     * @param string $sSection
     * @param string $sValue
     * @param int $iProfileId Profile ID
     *
     * @return void
     */
    public function setNotification($sSection, $sValue, $iProfileId)
    {
        $this->orm->update(DbTableName::MEMBER_NOTIFICATION, $sSection, $sValue, 'profileId', $iProfileId);
    }

    /**
     * Get the user notifications.
     *
     * @param int $iProfileId
     *
     * @return stdClass
     */
    public function getNotification($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'notification' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER_NOTIFICATION) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Check notifications.
     *
     * @param int $iProfileId
     * @param string $sNotifName Notification name.
     *
     * @return bool Returns TRUE if the notification is wanted, FALSE otherwise.
     */
    public function isNotification($iProfileId, $sNotifName)
    {
        $this->cache->start(self::CACHE_GROUP, 'isNotification' . $iProfileId, static::CACHE_TIME);

        if (!$bNotification = $this->cache->get()) {
            $sSql = 'SELECT ' . $sNotifName . ' FROM' . Db::prefix(DbTableName::MEMBER_NOTIFICATION) .
                'WHERE profileId = :profileId AND ' . $sNotifName . ' = 1 LIMIT 1';

            $rStmt = Db::getInstance()->prepare($sSql);
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $bNotification = $rStmt->rowCount() === 1;
            Db::free($rStmt);
            $this->cache->put($bNotification);
        }

        return $bNotification;
    }

    /**
     * Set the last activity of a user.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return void
     */
    public function setLastActivity($iProfileId, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'lastActivity', $this->sCurrentDate, 'profileId', $iProfileId);
    }

    /**
     * Set the last edit account of a user.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return void
     */
    public function setLastEdit($iProfileId, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'lastEdit', $this->sCurrentDate, 'profileId', $iProfileId);
    }

    /**
     * Approve a profile.
     *
     * @param int $iProfileId
     * @param int $iStatus 1 = approved | 0 = not approved
     * @param string $sTable
     *
     * @return void
     */
    public function approve($iProfileId, $iStatus, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'active', $iStatus, 'profileId', $iProfileId);
    }

    /**
     * Get member data. The validation hash, and other useful data for sending the activation email (hash, email, username, firstName).
     *
     * @param string $sEmail User's email address.
     * @param string $sTable
     *
     * @return stdClass|bool Returns the data member (email, username, firstName, hashValidation) on success, otherwise returns false if there is an error.
     */
    public function getHashValidation($sEmail, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'SELECT email, username, firstName, hashValidation FROM' . Db::prefix($sTable) .
            'WHERE email = :email AND active = :emailActivation LIMIT 1'
        );
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindValue(':emailActivation', RegistrationCore::EMAIL_ACTIVATION, PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow;
    }

    /**
     * Valid on behalf of a user with the hash.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable
     *
     * @return bool
     */
    public function validateAccount($sEmail, $sHash, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET active = :noActivation WHERE email = :email AND hashValidation = :hash AND active = :emailActivation LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindValue(':noActivation', RegistrationCore::NO_ACTIVATION, PDO::PARAM_INT);
        $rStmt->bindValue(':emailActivation', RegistrationCore::EMAIL_ACTIVATION, PDO::PARAM_INT);
        $rStmt->bindParam(':hash', $sHash, PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);

        return $rStmt->execute();
    }

    /**
     * Adding a User.
     *
     * @param array $aData
     *
     * @return int The ID of the User.
     */
    public function add(array $aData)
    {
        $sHashValidation = !empty($aData['hash_validation']) ? $aData['hash_validation'] : null;

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER) . '(email, username, password, firstName, lastName, sex, matchSex, birthDate, active, lang, ip, hashValidation, joinDate, lastActivity)
            VALUES (:email, :username, :password, :firstName, :lastName, :sex, :matchSex, :birthDate, :active, :lang, :ip, :hashValidation, :joinDate, :lastActivity)');
        $rStmt->bindValue(':email', trim($aData['email']), PDO::PARAM_STR);
        $rStmt->bindValue(':username', trim($aData['username']), PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $aData['first_name'], PDO::PARAM_STR);
        $rStmt->bindValue(':lastName', $aData['last_name'], PDO::PARAM_STR);
        $rStmt->bindValue(':sex', $aData['sex'], PDO::PARAM_STR);
        $rStmt->bindValue(':matchSex', Form::setVal($aData['match_sex']), PDO::PARAM_STR);
        $rStmt->bindValue(':birthDate', $aData['birth_date'], PDO::PARAM_STR);
        $rStmt->bindValue(':active', (!empty($aData['is_active']) ? $aData['is_active'] : RegistrationCore::NO_ACTIVATION), PDO::PARAM_INT);
        $rStmt->bindValue(':lang', (!empty($aData['lang']) ? substr($aData['lang'], 0, 5) : Lang::DEFAULT_LOCALE), PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $aData['ip'], PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $sHashValidation, PDO::PARAM_STR, self::HASH_VALIDATION_LENGTH);
        $rStmt->bindValue(':joinDate', $this->sCurrentDate, PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $this->sCurrentDate, PDO::PARAM_STR);
        $rStmt->execute();
        $this->setKeyId(Db::getInstance()->lastInsertId()); // Set the user's ID
        Db::free($rStmt);

        $this->setInfoFields($aData);
        $this->setDefaultPrivacySetting();
        $this->setDefaultNotification();

        // Last one, update the membership with the correct details
        $this->updateMembership(
            (int)DbConfig::getSetting('defaultMembershipGroupId'),
            $this->getKeyId(),
            $this->sCurrentDate
        );

        return $this->getKeyId();
    }

    /**
     * @param array $aData
     *
     * @return bool
     */
    public function setInfoFields(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_INFO) . '(profileId, country, city, state, zipCode, description, punchline)
            VALUES (:profileId, :country, :city, :state, :zipCode, :description, :punchline)');
        $rStmt->bindValue(':profileId', $this->getKeyId(), PDO::PARAM_INT);
        $rStmt->bindValue(':country', (!empty($aData['country']) ? $aData['country'] : ''), PDO::PARAM_STR);
        $rStmt->bindValue(':city', (!empty($aData['city']) ? $aData['city'] : ''), PDO::PARAM_STR);
        $rStmt->bindValue(':state', (!empty($aData['state']) ? $aData['state'] : ''), PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', (!empty($aData['zip_code']) ? $aData['zip_code'] : ''), PDO::PARAM_STR);
        $rStmt->bindValue(':description', (!empty($aData['description']) ? $aData['description'] : ''), PDO::PARAM_STR);
        $rStmt->bindValue(':punchline', (!empty($aData['punchline']) ? $aData['punchline'] : ''), PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Set the default privacy settings.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultPrivacySetting()
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_PRIVACY) .
            '(profileId, privacyProfile, searchProfile, userSaveViews)
            VALUES (:profileId, \'all\', \'yes\', \'yes\')');
        $rStmt->bindValue(':profileId', $this->getKeyId(), PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Set the default notifications.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultNotification()
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::MEMBER_NOTIFICATION) .
            '(profileId, enableNewsletters, newMsg, friendRequest)
            VALUES (:profileId, 1, 1, 1)');
        $rStmt->bindValue(':profileId', $this->getKeyId(), PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * To avoid flooding!
     * Waiting time before a new registration with the same IP address.
     *
     * @param string $sIp
     * @param int $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @param string $sTable
     *
     * @return bool Return TRUE if the weather was fine, FALSE otherwise.
     */
    public function checkWaitJoin($sIp, $iWaitTime, $sCurrentTime, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) .
            'WHERE ip = :ip AND DATE_ADD(joinDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');
        $rStmt->bindValue(':ip', $sIp, PDO::PARAM_STR);
        $rStmt->bindValue(':waitTime', $iWaitTime, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 0;
    }


    /********** AVATAR **********/

    /**
     * Update or add a new avatar.
     *
     * @param int $iProfileId
     * @param string|null $sAvatar NULL to remove the avatar.
     * @param int $iApproved
     *
     * @return bool
     */
    public function setAvatar($iProfileId, $sAvatar, $iApproved)
    {
        $sSql = 'UPDATE' . Db::prefix(DbTableName::MEMBER) .
            'SET avatar = :avatar, approvedAvatar = :approved WHERE profileId = :profileId LIMIT 1';

        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':avatar', $sAvatar, PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Get avatar.
     *
     * @param int $iProfileId
     * @param string|null $iApproved (1 = approved | 0 = pending | NULL = approved and pending)
     *
     * @return stdClass The Avatar (SQL alias is pic), profileId and approvedAvatar
     */
    public function getAvatar($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'avatar' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $bIsApproved = $iApproved !== null;

            $sSqlApproved = $bIsApproved ? ' AND approvedAvatar = :approved ' : ' ';
            $rStmt = Db::getInstance()->prepare(
                'SELECT profileId, avatar AS pic, approvedAvatar FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1'
            );
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            if ($bIsApproved) {
                $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_STR);
            }
            $rStmt->execute();
            $oData = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Delete an avatar in the database.
     *
     * @param int $iProfileId
     *
     * @return bool
     */
    public function deleteAvatar($iProfileId)
    {
        return $this->setAvatar($iProfileId, null, 1);
    }


    /********** BACKGROUND **********/

    /**
     * Get file of a user background.
     *
     * @param int $iProfileId
     * @param int|null $iApproved (1 = approved | 0 = pending | NULL = approved and pending).
     *
     * @return string
     */
    public function getBackground($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'background' . $iProfileId, static::CACHE_TIME);

        if (!$sFile = $this->cache->get()) {
            $bIsApproved = $iApproved !== null;

            $sSqlApproved = $bIsApproved ? ' AND approved = :approved ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT file FROM' . Db::prefix(DbTableName::MEMBER_BACKGROUND) . 'WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            if ($bIsApproved) {
                $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_STR);
            }
            $rStmt->execute();
            $sFile = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sFile);
        }

        return $sFile;
    }

    /**
     * Add profile background.
     *
     * @param int $iProfileId
     * @param string $sFile
     * @param int $iApproved
     *
     * @return bool
     */
    public function addBackground($iProfileId, $sFile, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix(DbTableName::MEMBER_BACKGROUND) . '(profileId, file, approved) VALUES (:profileId, :file, :approved)'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':file', $sFile, PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Delete profile background.
     *
     * @param int $iProfileId
     *
     * @return bool
     */
    public function deleteBackground($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::MEMBER_BACKGROUND) . 'WHERE profileId = :profileId'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Delete User.
     *
     * @param int $iProfileId
     * @param string $sUsername
     *
     * @return void
     *
     * @throws ForbiddenActionException
     */
    public function delete($iProfileId, $sUsername)
    {
        $sUsername = (string)$sUsername;
        $iProfileId = (int)$iProfileId;

        if ($sUsername === PH7_GHOST_USERNAME) {
            throw new ForbiddenActionException('You cannot delete this profile!');
        }

        $oDb = Db::getInstance();

        // PRIVATE MESSAGES
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MESSAGE) . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MESSAGE) . 'WHERE recipient = ' . $iProfileId);

        // MESSENGER MESSAGES
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MESSENGER) . 'WHERE fromUser = ' . Db::getInstance()->quote($sUsername));
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MESSENGER) . 'WHERE toUser = ' . Db::getInstance()->quote($sUsername));

        // PROFILE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_PROFILE) . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_PROFILE) . 'WHERE recipient = ' . $iProfileId);

        // PICTURE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_PICTURE) . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_PICTURE) . 'WHERE recipient = ' . $iProfileId);

        // VIDEO COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_VIDEO) . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_VIDEO) . 'WHERE recipient = ' . $iProfileId);

        // NOTE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_NOTE) . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_NOTE) . 'WHERE recipient = ' . $iProfileId);

        // BLOG COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_BLOG) . 'WHERE sender = ' . $iProfileId);

        // GAME COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::COMMENT_GAME) . 'WHERE sender = ' . $iProfileId);

        // PHOTO ALBUMS AND PICTURES
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::PICTURE) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::ALBUM_PICTURE) . 'WHERE profileId = ' . $iProfileId);

        // VIDEO ALBUMS AND VIDEOS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::VIDEO) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::ALBUM_VIDEO) . 'WHERE profileId = ' . $iProfileId);

        // FRIENDS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_FRIEND) . 'WHERE friendId = ' . $iProfileId);

        // WALL (FEED)
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_WALL) . 'WHERE profileId = ' . $iProfileId);

        // PROFILE BACKGROUND
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_BACKGROUND) . 'WHERE profileId = ' . $iProfileId);

        // NOTES
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::NOTE_CATEGORY) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::NOTE) . 'WHERE profileId = ' . $iProfileId);

        // LIKES
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::LIKE) . 'WHERE keyId LIKE ' . Db::getInstance()->quote('%' . $sUsername . '.html'));

        // PROFILE VISITS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_WHO_VIEW) . 'WHERE visitorId = ' . $iProfileId);

        // REPORT FROM THE USER
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::REPORT) . 'WHERE spammerId = ' . $iProfileId);

        // USER LOG SESSIONS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_LOG_SESS) . 'WHERE profileId = ' . $iProfileId);

        // FORUM TOPICS
        /*
        No! Ghost Profile is ultimately the best solution!
        WARNING: Do not change this part of code without asking permission to Pierre-Henry Soria
        */
        //$oDb->exec('DELETE FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) . 'WHERE profileId = ' . $iProfileId);
        //$oDb->exec('DELETE FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . 'WHERE profileId = ' . $iProfileId);

        // NOTIFICATIONS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_NOTIFICATION) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // PRIVACY SETTINGS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_PRIVACY) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // INFO FIELDS
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER_INFO) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // USER PROFILE DATA
        $oDb->exec('DELETE FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        unset($oDb); // Destruction of the object
    }

    /**
     * @param string $sUsernameSearch
     * @param string $sTable
     *
     * @return array data of users (profileId, username, sex)
     */
    public function getUsernameList($sUsernameSearch, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId, username, sex FROM' . Db::prefix($sTable) . 'WHERE username <> :ghostUsername AND username LIKE :username');
        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        $rStmt->bindValue(':username', '%' . $sUsernameSearch . '%', PDO::PARAM_STR);
        $rStmt->execute();
        $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aRow;
    }

    /**
     * Get (all) profile data.
     *
     * @param string $sOrder
     * @param int|null $iOffset
     * @param int|null $iLimit
     *
     * @return array Data of users.
     */
    public function getProfiles($sOrder = SearchCoreModel::LAST_ACTIVITY, $iOffset = null, $iLimit = null)
    {
        $bIsLimit = $iOffset !== null && $iLimit !== null;
        $bHideUserLogged = !empty($this->iProfileId);
        $bOnlyAvatarsSet = (bool)DbConfig::getSetting('profileWithAvatarSet');

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sOrder = SearchCoreModel::order($sOrder, SearchCoreModel::DESC);

        $sSqlLimit = $bIsLimit ? 'LIMIT :offset, :limit' : '';
        $sSqlHideLoggedProfile = $bHideUserLogged ? ' AND (m.profileId <> :profileId)' : '';
        $sSqlShowOnlyWithAvatars = $bOnlyAvatarsSet ? $this->getUserWithAvatarOnlySql() : '';

        $sSqlQuery = 'SELECT * FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m LEFT JOIN' . Db::prefix(DbTableName::MEMBER_PRIVACY) . 'AS p USING(profileId)
            LEFT JOIN' . Db::prefix(DbTableName::MEMBER_INFO) . 'AS i USING(profileId) WHERE (username <> :ghostUsername) AND
            (searchProfile = \'yes\') AND (username IS NOT NULL) AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND
            (country IS NOT NULL) AND (city IS NOT NULL) AND (groupId <> :visitorGroup) AND (groupId <> :pendingGroup) AND (ban = 0)' .
            $sSqlHideLoggedProfile . $sSqlShowOnlyWithAvatars . $sOrder . $sSqlLimit;

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        $rStmt->bindValue(':visitorGroup', self::VISITOR_GROUP, PDO::PARAM_INT);
        $rStmt->bindValue(':pendingGroup', self::PENDING_GROUP, PDO::PARAM_INT);

        if ($bHideUserLogged) {
            $rStmt->bindValue(':profileId', $this->iProfileId, PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();
        $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aRow;
    }

    /**
     * Get users from the location data.
     *
     * @param string $sCountryCode The country code. e.g. US, CA, FR, ES, BE, NL
     * @param string $sCity
     * @param bool $bCount
     * @param string $sOrder
     * @param int|null $iOffset
     * @param int|null $iLimit
     *
     * @return array|stdClass|int Object with the users list returned or integer for the total number users returned.
     */
    public function getGeoProfiles($sCountryCode, $sCity, $bCount, $sOrder, $iOffset = null, $iLimit = null)
    {
        $bLimit = $iOffset !== null && $iLimit !== null;

        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sOrder = !$bCount ? SearchCoreModel::order($sOrder, SearchCoreModel::DESC) : '';

        $sSqlLimit = (!$bCount || $bLimit) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(m.profileId)';

        $sSqlCity = !empty($sCity) ? 'AND (city LIKE :city)' : '';

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m LEFT JOIN' . Db::prefix(DbTableName::MEMBER_INFO) . 'AS i USING(profileId)
            WHERE (username <> :ghostUsername) AND (country = :country) ' . $sSqlCity . ' AND (username IS NOT NULL)
            AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND (country IS NOT NULL)
            AND (city IS NOT NULL) AND (groupId <> :visitorGroup) AND (groupId <> :pendingGroup) AND (ban = 0)' . $sOrder . $sSqlLimit
        );

        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, PDO::PARAM_STR);
        $rStmt->bindValue(':visitorGroup', self::VISITOR_GROUP, PDO::PARAM_INT);
        $rStmt->bindValue(':pendingGroup', self::PENDING_GROUP, PDO::PARAM_INT);

        $rStmt->bindParam(':country', $sCountryCode, PDO::PARAM_STR, 2);

        if (!empty($sCity)) {
            $rStmt->bindValue(':city', '%' . $sCity . '%', PDO::PARAM_STR);
        }

        if (!$bCount || $bLimit) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $aRow = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);

            return $aRow;
        }

        $iTotalUsers = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalUsers;
    }

    /**
     * Updating the privacy settings.
     *
     * @param int $iProfileId
     *
     * @return stdClass
     */
    public function getPrivacySetting($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'privacySetting' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $iProfileId = (int)$iProfileId;

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBER_PRIVACY) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Get the Profile ID of a user.
     *
     * @param string|null $sEmail
     * @param string|null $sUsername
     * @param string $sTable
     *
     * @return int|bool The Member ID if it is found or FALSE if not found.
     */
    public function getId($sEmail = null, $sUsername = null, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'id' . $sEmail . $sUsername . $sTable, static::CACHE_TIME);

        if (!$iProfileId = $this->cache->get()) {
            Various::checkModelTable($sTable);

            if (!empty($sEmail)) {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1');
                $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
            } else {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE username = :username LIMIT 1');
                $rStmt->bindValue(':username', $sUsername, PDO::PARAM_STR);
            }

            $rStmt->execute();

            if ($rStmt->rowCount() === 0) {
                return false;
            }

            $iProfileId = (int)$rStmt->fetchColumn();
            Db::free($rStmt);
            $this->cache->put($iProfileId);
        }

        return $iProfileId;
    }

    /**
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return string The email address of a member.
     */
    public function getEmail($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'email' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sEmail = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT email FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $sEmail = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sEmail);
        }

        return $sEmail;
    }

    /**
     * Retrieves the username from the user ID.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return string The username of a member.
     */
    public function getUsername($iProfileId, $sTable = DbTableName::MEMBER)
    {
        if ($iProfileId === PH7_ADMIN_ID) {
            return t('Administration of %site_name%');
        }

        $this->cache->start(self::CACHE_GROUP, 'username' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sUsername = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT username FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $sUsername = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sUsername);
        }

        return $sUsername;
    }

    /**
     * Retrieves the first name from the user ID.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return string The user first name.
     */
    public function getFirstName($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'firstName' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sFirstName = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT firstName FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $sFirstName = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sFirstName);
        }

        return $sFirstName;
    }

    /**
     * Get Gender (sex) of a user.
     *
     * @param int|null $iProfileId
     * @param string $sUsername
     * @param string $sTable
     *
     * @return string The user's sex.
     */
    public function getSex($iProfileId = null, $sUsername = null, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'sex' . $iProfileId . $sUsername . $sTable, static::CACHE_TIME);

        if (!$sSex = $this->cache->get()) {
            Various::checkModelTable($sTable);

            if (!empty($iProfileId)) {
                $rStmt = Db::getInstance()->prepare('SELECT sex FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
                $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            } else {
                $rStmt = Db::getInstance()->prepare('SELECT sex FROM' . Db::prefix($sTable) . 'WHERE username=:username LIMIT 1');
                $rStmt->bindValue(':username', $sUsername, PDO::PARAM_STR);
            }

            $rStmt->execute();
            $sSex = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sSex);
        }

        return $sSex;
    }

    /**
     * Get Match sex for a member (so only from the Members table, because Affiliates and Admins don't have match sex).
     *
     * @param int $iProfileId
     *
     * @return string The user's match sex.
     */
    public function getMatchSex($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'matchsex' . $iProfileId, static::CACHE_TIME);

        if (!$sMatchSex = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT matchSex FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $sMatchSex = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sMatchSex);
        }

        return $sMatchSex;
    }

    /**
     * Get Date of Birth of a user.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return string The user's date of birth.
     */
    public function getBirthDate($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'birthdate' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sBirthDate = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT birthDate FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $sBirthDate = $rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($sBirthDate);
        }

        return $sBirthDate;
    }

    /**
     * Get user's group.
     *
     * @param int $iProfileId
     * @param string sTable
     *
     * @return int The Group ID of a member
     */
    public function getGroupId($iProfileId, $sTable = DbTableName::MEMBER)
    {
        $this->cache->start(self::CACHE_GROUP, 'groupId' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$iGroupId = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT groupId FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $iGroupId = (int)$rStmt->fetchColumn();
            Db::free($rStmt);

            $this->cache->put($iGroupId);
        }

        return $iGroupId;
    }

    /**
     * Get the membership(s) data.
     *
     * @param int|null $iGroupId Group ID. Select only the specific membership from a group ID.
     *
     * @return stdClass|array The membership(s) data.
     */
    public function getMemberships($iGroupId = null)
    {
        $this->cache->start(self::CACHE_GROUP, DbTableName::MEMBERSHIP . $iGroupId, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $bIsGroupId = !empty($iGroupId);
            $sSqlGroup = $bIsGroupId ? ' WHERE groupId = :groupId ' : ' ';

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::MEMBERSHIP) . $sSqlGroup . 'ORDER BY enable ASC, groupId ASC');
            if (!empty($iGroupId)) {
                $rStmt->bindValue(':groupId', $iGroupId, PDO::PARAM_INT);
            }
            $rStmt->execute();
            $mData = $bIsGroupId ? $rStmt->fetch(PDO::FETCH_OBJ) : $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($mData);
        }

        return $mData;
    }

    /**
     * Get the membership details of a user.
     *
     * @param int $iProfileId
     *
     * @return stdClass The membership details.
     */
    public function getMembershipDetails($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'membershipDetails' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $sSql = 'SELECT m.*, g.expirationDays, g.name AS membershipName FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m INNER JOIN ' . Db::prefix(DbTableName::MEMBERSHIP) .
                'AS g USING(groupId) WHERE profileId = :profileId LIMIT 1';

            $rStmt = Db::getInstance()->prepare($sSql);
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Check if membership is expired.
     *
     * @param int $iProfileId
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     *
     * @return bool
     */
    public function checkMembershipExpiration($iProfileId, $sCurrentTime)
    {
        $sSqlQuery = 'SELECT m.profileId FROM' . Db::prefix(DbTableName::MEMBER) . 'AS m INNER JOIN' .
            Db::prefix(DbTableName::MEMBERSHIP) . 'AS pay USING(groupId) WHERE
            (pay.expirationDays = 0 OR DATE_ADD(m.membershipDate, INTERVAL pay.expirationDays DAY) >= :currentTime) AND
            (m.profileId = :profileId) LIMIT 1';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->rowCount() === 1;
    }

    /**
     * Update the membership group of a user.
     *
     * @param int $iNewGroupId The new ID of membership group.
     * @param int $iProfileId The user ID.
     * @param string|null $sDateTime In date format: 0000-00-00 00:00:00
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function updateMembership($iNewGroupId, $iProfileId, $sDateTime = null)
    {
        $bIsTime = !empty($sDateTime);

        $sSqlTime = $bIsTime ? ',membershipDate = :dateTime ' : ' ';

        $sSqlQuery = 'UPDATE' . Db::prefix(DbTableName::MEMBER) . 'SET groupId = :groupId' .
            $sSqlTime . 'WHERE profileId = :profileId LIMIT 1';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':groupId', $iNewGroupId, PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        if ($bIsTime) {
            $rStmt->bindValue(':dateTime', $sDateTime, PDO::PARAM_STR);
        }

        return $rStmt->execute();
    }

    /**
     * Get Info Fields from profile ID.
     *
     * @param int $iProfileId
     * @param string $sTable
     *
     * @return stdClass
     */
    public function getInfoFields($iProfileId, $sTable = DbTableName::MEMBER_INFO)
    {
        $this->cache->start(self::CACHE_GROUP, 'infoFields' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->execute();
            $oColumns = $rStmt->fetch(PDO::FETCH_OBJ);
            Db::free($rStmt);

            $oData = new stdClass;
            foreach ($oColumns as $sColumn => $sValue) {
                if ($sColumn !== 'profileId') {
                    $oData->$sColumn = $sValue;
                }
            }
            $this->cache->put($oData);
        }

        return $oData;
    }


    /**
     * @param string $sTable DB country table name.
     *
     * @return array
     *
     * @throws PH7InvalidArgumentException If the specified table is incorrect.
     */
    public function getCountries($sTable = DbTableName::MEMBER_COUNTRY)
    {
        $iNinetyDaysTime = 7776000;
        $this->cache->start(self::CACHE_GROUP, 'countriesList' . $sTable, $iNinetyDaysTime);

        if (!$aCountries = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $sSqlQuery = 'SELECT countryCode FROM' . Db::prefix($sTable);
            $rStmt = Db::getInstance()->prepare($sSqlQuery);
            $rStmt->execute();
            $aCountries = $rStmt->fetchAll(PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($aCountries);
        }

        return $aCountries;
    }

    /**
     * Add countries for members
     *
     * @param string $sCountryCode e.g. en, fr, be, ru, nl, ...
     *
     * @return bool|int
     *
     * @throws PH7InvalidArgumentException If the specified table is incorrect.
     */
    public function addCountry($sCountryCode, $sTable = DbTableName::MEMBER_COUNTRY)
    {
        Various::checkModelTable($sTable);

        return $this->orm->insert($sTable, ['countryCode' => $sCountryCode]);
    }

    /**
     * @param string $sTable
     *
     * @throws PH7InvalidArgumentException If the specified table is incorrect.
     */
    public function clearCountries($sTable = DbTableName::MEMBER_COUNTRY)
    {
        Various::checkModelTable($sTable);

        $oDb = Db::getInstance();
        $oDb->exec('TRUNCATE' . Db::prefix($sTable));
        unset($oDb);
    }

    /**
     * @return string
     */
    public function getUserWithAvatarOnlySql()
    {
        return ' AND avatar IS NOT NULL AND approvedAvatar = 1';
    }

    /**
     * @param array $aSex
     *
     * @return string
     */
    private function getSexInClauseSql(array $aSex)
    {
        $sGender = '';

        foreach ($aSex as $sSex) {
            if ($sSex === GenderTypeUserCore::MALE) {
                $sGender .= "'" . GenderTypeUserCore::MALE . "',";
            }

            if ($sSex === GenderTypeUserCore::FEMALE) {
                $sGender .= "'" . GenderTypeUserCore::FEMALE . "',";
            }

            if ($sSex === GenderTypeUserCore::COUPLE) {
                $sGender .= "'" . GenderTypeUserCore::COUPLE . "',";
            }
        }

        $sInClauseValue = rtrim($sGender, ','); // Removes the last extra comma

        if (!empty($sInClauseValue)) {
            return ' AND sex IN (' . $sInClauseValue . ') ';
        }

        return '';
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
