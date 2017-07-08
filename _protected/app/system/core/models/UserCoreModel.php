<?php
/**
 * @title          User Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\CArray\ObjArr;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Security\Security;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;
use stdClass;

// Abstract Class
class UserCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/user';
    const CACHE_TIME = 604800;

    /** @var string */
    protected $sCurrentDate;

    /** @var string */
    protected $iProfileId;

    public function __construct()
    {
        parent::__construct();

        $this->sCurrentDate = (new CDateTime)->get()->dateTime('Y-m-d H:i:s');
        $this->iProfileId = (new Session)->get('member_id');
    }

    public static function checkGroup()
    {
        $oSession = new Session;

        if (!$oSession->exists('member_group_id')) {
            $oSession->regenerateId();
            $oSession->set('member_group_id', PermissionCore::VISITOR_GROUP_ID);
        }

        $rStmt = Db::getInstance()->prepare('SELECT permissions FROM' . Db::prefix('Memberships') . 'WHERE groupId = :groupId LIMIT 1');
        $rStmt->bindValue(':groupId', $oSession->get('member_group_id'), \PDO::PARAM_INT);
        $rStmt->execute();
        $oFetch = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        unset($oSession);

        return ObjArr::toObject(unserialize($oFetch->permissions));
    }

    /**
     * Login method for Members and Affiliate, but not for Admins since it has another method PH7\AdminModel::adminLogin() even more secure.
     *
     * @param string $sEmail Not case sensitive since on lot of mobile devices (such as iPhone), the first letter is uppercase.
     * @param string $sPassword
     * @param string $sTable Default 'Members'
     *
     * @return mixed (boolean "true" or string "message")
     */
    public function login($sEmail, $sPassword, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT email, password FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        $sDbEmail = (!empty($oRow->email)) ? $oRow->email : '';
        $sDbPassword = (!empty($oRow->password)) ? $oRow->password : '';

        if (strtolower($sEmail) !== strtolower($sDbEmail)) {
            return 'email_does_not_exist';
        }
        if (!Security::checkPwd($sPassword, $sDbPassword)) {
            return 'password_does_not_exist';
        }

        return true;
    }

    /**
     * Set Log Session.
     *
     * @param string $sEmail
     * @param string $sUsername
     * @param string $sFirstName
     * @param string $sTable
     * @param string $sTable Default 'Members'
     *
     * @return void
     */
    public function sessionLog($sEmail, $sUsername, $sFirstName, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable . 'LogSess') . '(email, username, firstName, ip)
        VALUES (:email, :username, :firstName, :ip)');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $sFirstName, \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', Ip::get(), \PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * Read Profile Data.
     *
     * @param integer $iProfileId The user ID
     * @param string $sTable Default 'Members'
     *
     * @return stdClass The data of a member
     */
    public function readProfile($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'readProfile' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Get the total number of members.
     *
     * @param string $sTable Default 'Members'
     * @param integer $iDay Default '0'
     * @param string $sGenger Values ​​available 'all', 'male', 'female'. 'couple' is only available to Members. Default 'all'
     *
     * @return integer Total Users
     */
    public function total($sTable = 'Members', $iDay = 0, $sGenger = 'all')
    {
        Various::checkModelTable($sTable);
        $iDay = (int)$iDay;

        $bIsDay = ($iDay > 0);
        $bIsGenger = ($sTable === 'Members' ? ($sGenger === 'male' || $sGenger === 'female' || $sGenger === 'couple') : ($sGenger === 'male' || $sGenger === 'female'));

        $sSqlDay = $bIsDay ? ' AND (joinDate + INTERVAL :day DAY) > NOW()' : '';
        $sSqlGender = $bIsGenger ? ' AND sex = :gender' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) AS totalUsers FROM' . Db::prefix($sTable) . 'WHERE username <> \'' . PH7_GHOST_USERNAME . '\'' . $sSqlDay . $sSqlGender);
        if ($bIsDay) {
            $rStmt->bindValue(':day', $iDay, \PDO::PARAM_INT);
        }
        if ($bIsGenger) {
            $rStmt->bindValue(':gender', $sGenger, \PDO::PARAM_STR);
        }
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->totalUsers;
    }

    /**
     * Update profile data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param integer $iProfileId Profile ID
     * @param string $sTable Default 'Members'
     *
     * @return void
     */
    public function updateProfile($sSection, $sValue, $iProfileId, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, $sSection, $sValue, 'profileId', $iProfileId);
    }

    /**
     * Update Privacy setting data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param integer $iProfileId Profile ID
     *
     * @return void
     */
    public function updatePrivacySetting($sSection, $sValue, $iProfileId)
    {
        $this->orm->update('MembersPrivacy', $sSection, $sValue, 'profileId', $iProfileId);
    }

    /**
     * Change password of a member.
     *
     * @param string $sEmail
     * @param string $sNewPassword
     * @param string $sTable
     *
     * @return boolean
     */
    public function changePassword($sEmail, $sNewPassword, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET password = :newPassword WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':newPassword', Security::hashPwd($sNewPassword), \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Set a new hash validation.
     *
     * @param integer $iProfileId
     * @param string $sHash
     * @param string $sTable
     *
     * @return boolean
     */
    public function setNewHashValidation($iProfileId, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET hashValidation = :hash WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindParam(':hash', $sHash, \PDO::PARAM_STR, 40);

        return $rStmt->execute();
    }

    /**
     * Check the hash validation.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable
     *
     * @return boolean
     */
    public function checkHashValidation($sEmail, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) FROM' . Db::prefix($sTable) . 'WHERE email = :email AND hashValidation = :hash LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindParam(':hash', $sHash, \PDO::PARAM_STR, 40);
        $rStmt->execute();

        return $rStmt->fetchColumn() == 1;
    }

    /**
     * Search users.
     *
     * @param array $aParams
     * @param boolean $bCount
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return stdClass|integer Object for the users list returned or Integer for the total number users returned.
     */
    public function search(array $aParams, $bCount, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $bIsFirstName = !empty($aParams[SearchQueryCore::FIRST_NAME]) && Str::noSpaces($aParams[SearchQueryCore::FIRST_NAME]);
        $bIsMiddleName = !empty($aParams[SearchQueryCore::MIDDLE_NAME]) && Str::noSpaces($aParams[SearchQueryCore::MIDDLE_NAME]);
        $bIsLastName = !empty($aParams[SearchQueryCore::LAST_NAME]) && Str::noSpaces($aParams[SearchQueryCore::LAST_NAME]);
        $bIsSingleAge = !empty($aParams[SearchQueryCore::AGE]);
        $bIsAge = empty($aParams[SearchQueryCore::AGE]) && !empty($aParams[SearchQueryCore::MIN_AGE]) && !empty($aParams[SearchQueryCore::MAX_AGE]);
        $bIsHeight = !empty($aParams[SearchQueryCore::HEIGHT]);
        $bIsWeight = !empty($aParams[SearchQueryCore::WEIGHT]);
        $bIsCountry = !empty($aParams[SearchQueryCore::COUNTRY]) && Str::noSpaces($aParams[SearchQueryCore::COUNTRY]);
        $bIsCity = !empty($aParams[SearchQueryCore::CITY]) && Str::noSpaces($aParams[SearchQueryCore::CITY]);
        $bIsState = !empty($aParams[SearchQueryCore::STATE]) && Str::noSpaces($aParams[SearchQueryCore::STATE]);
        $bIsZipCode = !empty($aParams[SearchQueryCore::ZIP_CODE]) && Str::noSpaces($aParams[SearchQueryCore::ZIP_CODE]);
        $bIsMail = !empty($aParams[SearchQueryCore::EMAIL]) && Str::noSpaces($aParams[SearchQueryCore::EMAIL]);
        $bIsSex = !empty($aParams[SearchQueryCore::SEX]);
        $bHideUserLogged = !empty($this->iProfileId);
        $bIsMatchSex = !empty($aParams[SearchQueryCore::MATCH_SEX]);

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(m.profileId) AS totalUsers';
        $sSqlFirstName = $bIsFirstName ? ' AND firstName = :firstName' : '';
        $sSqlMiddleName = $bIsMiddleName ? ' AND middleName = :middleName' : '';
        $sSqlLastName = $bIsLastName ? ' AND lastName = :lastName' : '';
        $sSqlSingleAge = $bIsSingleAge ? ' AND birthDate LIKE :birthDate ' : '';
        $sSqlAge = $bIsAge ? ' AND birthDate BETWEEN DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :age2 YEAR) AND DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :age1 YEAR) ' : '';
        $sSqlHeight = $bIsHeight ? ' AND height = :height ' : '';
        $sSqlWeight = $bIsWeight ? ' AND weight = :weight ' : '';
        $sSqlCountry = $bIsCountry ? ' AND country = :country ' : '';
        $sSqlCity = $bIsCity ? ' AND city LIKE :city ' : '';
        $sSqlState = $bIsState ? ' AND state LIKE :state ' : '';
        $sSqlZipCode = $bIsZipCode ? ' AND zipCode LIKE :zipCode ' : '';
        $sSqlEmail = $bIsMail ? ' AND email LIKE :email ' : '';
        $sSqlOnline = !empty($aParams[SearchQueryCore::ONLINE]) ? ' AND userStatus = 1 AND lastActivity > DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL ' . DbConfig::getSetting('userTimeout') . ' MINUTE) ' : '';
        $sSqlAvatar = !empty($aParams[SearchQueryCore::AVATAR]) ? $this->getUserWithAvatarOnlySql() : '';
        $sSqlHideLoggedProfile = $bHideUserLogged ? ' AND (m.profileId <> :profileId)' : '';

        if (empty($aParams[SearchQueryCore::ORDER])) {
            $aParams[SearchQueryCore::ORDER] = SearchCoreModel::LATEST; // Default is "ORDER BY joinDate"
        }

        if (empty($aParams[SearchQueryCore::SORT])) {
            $aParams[SearchQueryCore::SORT] = SearchCoreModel::ASC; // Default is "ascending"
        }

        $sSqlOrder = SearchCoreModel::order($aParams[SearchQueryCore::ORDER], $aParams[SearchQueryCore::SORT]);

        $sSqlMatchSex = $bIsMatchSex ? ' AND matchSex LIKE :matchSex ' : '';

        if ($bIsSex) {
            $sGender = '';
            $aSex = $aParams[SearchQueryCore::SEX];
            foreach ($aSex as $sSex) {
                if ($sSex === 'male') {
                    $sGender .= '\'male\',';
                }

                if ($sSex === 'female') {
                    $sGender .= '\'female\',';
                }

                if ($sSex === 'couple') {
                    $sGender .= '\'couple\',';
                }
            }

            $sSqlSex = ' AND sex IN (' . rtrim($sGender, ',') . ') ';
        } else {
            $sSqlSex = '';
        }

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Members') . 'AS m LEFT JOIN' . Db::prefix('MembersPrivacy') . 'AS p USING(profileId)
            LEFT JOIN' . Db::prefix('MembersInfo') . 'AS i USING(profileId) WHERE username <> \'' . PH7_GHOST_USERNAME . '\' AND searchProfile = \'yes\'
            AND (groupId <> 1) AND (groupId <> 9) AND (ban = 0)' . $sSqlHideLoggedProfile . $sSqlFirstName . $sSqlMiddleName . $sSqlLastName . $sSqlMatchSex . $sSqlSex . $sSqlSingleAge . $sSqlAge . $sSqlCountry . $sSqlCity . $sSqlState .
            $sSqlZipCode . $sSqlHeight . $sSqlWeight . $sSqlEmail . $sSqlOnline . $sSqlAvatar . $sSqlOrder . $sSqlLimit
        );

        if ($bIsMatchSex) {
            $rStmt->bindValue(':matchSex', '%' . $aParams[SearchQueryCore::MATCH_SEX] . '%', \PDO::PARAM_STR);
        }
        if ($bIsFirstName) {
            $rStmt->bindValue(':firstName', $aParams[SearchQueryCore::FIRST_NAME], \PDO::PARAM_STR);
        }
        if ($bIsMiddleName) {
            $rStmt->bindValue(':middleName', $aParams[SearchQueryCore::MIDDLE_NAME], \PDO::PARAM_STR);
        }
        if ($bIsLastName) {
            $rStmt->bindValue(':lastName', $aParams[SearchQueryCore::LAST_NAME], \PDO::PARAM_STR);
        }
        if ($bIsSingleAge) {
            $rStmt->bindValue(':birthDate', '%' . $aParams[SearchQueryCore::AGE] . '%', \PDO::PARAM_STR);
        }
        if ($bIsAge) {
            $rStmt->bindValue(':age1', $aParams[SearchQueryCore::MIN_AGE], \PDO::PARAM_INT);
        }
        if ($bIsAge) {
            $rStmt->bindValue(':age2', $aParams[SearchQueryCore::MAX_AGE], \PDO::PARAM_INT);
        }
        if ($bIsHeight) {
            $rStmt->bindValue(':height', $aParams[SearchQueryCore::HEIGHT], \PDO::PARAM_INT);
        }
        if ($bIsWeight) {
            $rStmt->bindValue(':weight', $aParams[SearchQueryCore::WEIGHT], \PDO::PARAM_INT);
        }
        if ($bIsCountry) {
            $rStmt->bindParam(':country', $aParams[SearchQueryCore::COUNTRY], \PDO::PARAM_STR, 2);
        }
        if ($bIsCity) {
            $rStmt->bindValue(':city', '%' . str_replace('-', ' ', $aParams[SearchQueryCore::CITY]) . '%', \PDO::PARAM_STR);
        }
        if ($bIsState) {
            $rStmt->bindValue(':state', '%' . str_replace('-', ' ', $aParams[SearchQueryCore::STATE]) . '%', \PDO::PARAM_STR);
        }
        if ($bIsZipCode) {
            $rStmt->bindValue(':zipCode', '%' . $aParams[SearchQueryCore::ZIP_CODE] . '%', \PDO::PARAM_STR);
        }
        if ($bIsMail) {
            $rStmt->bindValue(':email', '%' . $aParams[SearchQueryCore::EMAIL] . '%', \PDO::PARAM_STR);
        }
        if ($bHideUserLogged) {
            $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        }
        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int) $oRow->totalUsers;
        }
    }

    /**
     * Check online status.
     *
     * @param integer $iProfileId
     * @param integer $iTime Number of minutes that a member becomes inactive (offline). Default 1 minute
     *
     * @return boolean
     */
    public function isOnline($iProfileId, $iTime = 1)
    {
        $iProfileId = (int)$iProfileId;
        $iTime = (int)$iTime;

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix('Members') . 'WHERE profileId = :profileId
            AND userStatus = 1 AND lastActivity >= DATE_SUB(:currentTime, INTERVAL :time MINUTE) LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':time', $iTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $this->sCurrentDate, \PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 1;
    }

    /**
     * Set the user status.
     *
     * @param integer iProfileId
     * @param integer $iStatus Values: 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
     *
     * @return void
     */
    public function setUserStatus($iProfileId, $iStatus)
    {
        $this->orm->update('Members', 'userStatus', $iStatus, 'profileId', $iProfileId);
    }

    /**
     * Get the user status.
     *
     * @param integer $iProfileId
     *
     * @return integer The user status. 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
     */
    public function getUserStatus($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'userStatus' . $iProfileId, static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT userStatus FROM' . Db::prefix('Members') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int)$oRow->userStatus;
            unset($oRow);
            $this->cache->put($iData);
        }

        return $iData;
    }

    /**
     * Update the notifications.
     *
     * @param string $sSection
     * @param string $sValue
     * @param integer $iProfileId Profile ID
     *
     * @return void
     */
    public function setNotification($sSection, $sValue, $iProfileId)
    {
        $this->orm->update('MembersNotifications', $sSection, $sValue, 'profileId', $iProfileId);
    }

    /**
     * Get the user notifications.
     *
     * @param integer $iProfileId
     *
     * @return stdClass
     */
    public function getNotification($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'notification' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('MembersNotifications') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Check notifications.
     *
     * @param integer $iProfileId
     * @param string $sNotiName Notification name.
     *
     * @return boolean
     */
    public function isNotification($iProfileId, $sNotiName)
    {
        $this->cache->start(self::CACHE_GROUP, 'isNotification' . $iProfileId, static::CACHE_TIME);

        if (!$bData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT ' . $sNotiName . ' FROM' . Db::prefix('MembersNotifications') . 'WHERE profileId = :profileId AND ' . $sNotiName . ' = 1 LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $bData = ($rStmt->rowCount() === 1);
            Db::free($rStmt);
            $this->cache->put($bData);
        }

        return $bData;
    }

    /**
     * Set the last activity of a user.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return void
     */
    public function setLastActivity($iProfileId, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'lastActivity', $this->sCurrentDate, 'profileId', $iProfileId);
    }

    /**
     * Set the last edit account of a user.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return void
     */
    public function setLastEdit($iProfileId, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'lastEdit', $this->sCurrentDate, 'profileId', $iProfileId);
    }

    /**
     * Approve a profile.
     *
     * @param integer $iProfileId
     * @param integer $iStatus 1 = apprved | 0 = not approved
     * @param string $sTable Default 'Members'
     *
     * @return void
     */
    public function approve($iProfileId, $iStatus, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $this->orm->update($sTable, 'active', $iStatus, 'profileId', $iProfileId);
    }

    /**
     * Get member data. The hash of course but also some useful data for sending the activation email. (hash, email, username, firstName).
     *
     * @param string $sEmail User's email address.
     * @param string $sTable Default 'Members'
     *
     * @return stdClass|boolean Returns the data member (email, username, firstName, hashValidation) on success, otherwise returns false if there is an error.
     */
    public function getHashValidation($sEmail, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT email, username, firstName, hashValidation FROM' . Db::prefix($sTable) . 'WHERE email = :email AND active = 2');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow;
    }

    /**
     * Valid on behalf of a user with the hash.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable Default 'Members'
     *
     * @return boolean
     */
    public function validateAccount($sEmail, $sHash, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET active = 1 WHERE email = :email AND hashValidation = :hash AND active = 2');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindParam(':hash', $sHash, \PDO::PARAM_STR, 40);
        return $rStmt->execute();
    }

    /**
     * Adding a User.
     *
     * @param array $aData
     *
     * @return integer The ID of the User.
     */
    public function add(array $aData)
    {
        $sHashValidation = (!empty($aData['hash_validation']) ? $aData['hash_validation'] : null);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Members') . '(email, username, password, firstName, lastName, sex, matchSex, birthDate, active, ip, hashValidation, joinDate, lastActivity)
            VALUES (:email, :username, :password, :firstName, :lastName, :sex, :matchSex, :birthDate, :active, :ip, :hashValidation, :joinDate, :lastActivity)');
        $rStmt->bindValue(':email', trim($aData['email']), \PDO::PARAM_STR);
        $rStmt->bindValue(':username', trim($aData['username']), \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':lastName', $aData['last_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':sex', $aData['sex'], \PDO::PARAM_STR);
        $rStmt->bindValue(':matchSex', Form::setVal($aData['match_sex']), \PDO::PARAM_STR);
        $rStmt->bindValue(':birthDate', $aData['birth_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':active', (!empty($aData['is_active']) ? $aData['is_active'] : 1), \PDO::PARAM_INT);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_STR);
        $rStmt->bindParam(':hashValidation', $sHashValidation, \PDO::PARAM_STR, 40);
        $rStmt->bindValue(':joinDate', $this->sCurrentDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $this->sCurrentDate, \PDO::PARAM_STR);
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
     * @return boolean
     */
    public function setInfoFields(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersInfo') . '(profileId, middleName, country, city, state, zipCode, description, website, socialNetworkSite)
            VALUES (:profileId, :middleName, :country, :city, :state, :zipCode, :description, :website, :socialNetworkSite)');
        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        $rStmt->bindValue(':middleName', (!empty($aData['middle_name']) ? $aData['middle_name'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':country', (!empty($aData['country']) ? $aData['country'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':city', (!empty($aData['city']) ? $aData['city'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':state', (!empty($aData['state']) ? $aData['state'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', (!empty($aData['zip_code']) ? $aData['zip_code'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':description', (!empty($aData['description']) ? $aData['description'] : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':website', (!empty($aData['website']) ? trim($aData['website']) : ''), \PDO::PARAM_STR);
        $rStmt->bindValue(':socialNetworkSite', (!empty($aData['social_network_site']) ? trim($aData['social_network_site']) : ''), \PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Set the default privacy settings.
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultPrivacySetting()
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersPrivacy') .
            '(profileId, privacyProfile, searchProfile, userSaveViews)
            VALUES (:profileId, \'all\', \'yes\', \'yes\')');
        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Set the default notifications.
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultNotification()
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersNotifications') .
            '(profileId, enableNewsletters, newMsg, friendRequest)
            VALUES (:profileId, 0, 1, 1)');
        $rStmt->bindValue(':profileId', $this->getKeyId(), \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * To avoid flooding!
     * Waiting time before a new registration with the same IP address.
     *
     * @param string $sIp
     * @param integer $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @param string $sTable Default 'Members'
     *
     * @return boolean Return TRUE if the weather was fine, FALSE otherwise.
     */
    public function checkWaitJoin($sIp, $iWaitTime, $sCurrentTime, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) .
            'WHERE ip = :ip AND DATE_ADD(joinDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');
        $rStmt->bindValue(':ip', $sIp, \PDO::PARAM_STR);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 0;
    }


    /********** AVATAR **********/

    /**
     * Update or add a new avatar.
     *
     * @param integer $iProfileId
     * @param string $sAvatar
     * @param integer $iApproved
     *
     * @return boolean
     */
    public function setAvatar($iProfileId, $sAvatar, $iApproved)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Members') . 'SET avatar = :avatar, approvedAvatar = :approved WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':avatar', $sAvatar, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Get avatar.
     *
     * @param integer $iProfileId
     * @param integer $iApproved (1 = approved | 0 = pending | NULL = approved and pending)
     *
     * @return stdClass The Avatar (SQL alias is pic), profileId and approvedAvatar
     */
    public function getAvatar($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'avatar' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $sSqlApproved = (isset($iApproved)) ? ' AND approvedAvatar = :approved ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT profileId, avatar AS pic, approvedAvatar FROM' . Db::prefix('Members') . 'WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            if (isset($iApproved)) $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Delete an avatar in the database.
     *
     * @param integer $iProfileId
     *
     * @return boolean
     */
    public function deleteAvatar($iProfileId)
    {
        return $this->setAvatar($iProfileId, null, 1);
    }


    /********** BACKGROUND **********/

    /**
     * Get file of a user background.
     *
     * @param integer $iProfileId
     * @param integer $iApproved (1 = approved | 0 = pending | NULL = approved and pending) Default NULL
     *
     * @return string
     */
    public function getBackground($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'background' . $iProfileId, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            $sSqlApproved = (isset($iApproved)) ? ' AND approved = :approved ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT file FROM' . Db::prefix('MembersBackground') . 'WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            if (isset($iApproved)) {
                $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            }
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->file;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Add profile background.
     *
     * @param integer $iProfileId
     * @param string $sFile
     * @param integer $iApproved
     *
     * @return boolean
     */
    public function addBackground($iProfileId, $sFile, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('MembersBackground') . '(profileId, file, approved) VALUES (:profileId, :file, :approved)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Delete profile background.
     *
     * @param integer $iProfileId
     *
     * @return boolean
     */
    public function deleteBackground($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('MembersBackground') . 'WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Delete User.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $sUsername = (string)$sUsername;
        $iProfileId = (int)$iProfileId;

        if ($sUsername === PH7_GHOST_USERNAME) {
            exit('You cannot delete this profile!');
        }

        $oDb = Db::getInstance();

        // DELETE MESSAGES
        $oDb->exec('DELETE FROM' . Db::prefix('Messages') . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('Messages') . 'WHERE recipient = ' . $iProfileId);

        // DELETE MESSAGES OF MESSENGER
        $oDb->exec('DELETE FROM' . Db::prefix('Messenger') . 'WHERE fromUser = ' . Db::getInstance()->quote($sUsername));
        $oDb->exec('DELETE FROM' . Db::prefix('Messenger') . 'WHERE toUser = ' . Db::getInstance()->quote($sUsername));

        // DELETE PROFILE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsProfile') . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsProfile') . 'WHERE recipient = ' . $iProfileId);

        // DELETE PICTURE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsPicture') . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsPicture') . 'WHERE recipient = ' . $iProfileId);

        // DELETE VIDEO COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsVideo') . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsVideo') . 'WHERE recipient = ' . $iProfileId);

        // DELETE NOTE COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsNote') . 'WHERE sender = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsNote') . 'WHERE recipient = ' . $iProfileId);

        // DELETE BLOG COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsBlog') . 'WHERE sender = ' . $iProfileId);

        // DELETE GAME COMMENTS
        $oDb->exec('DELETE FROM' . Db::prefix('CommentsGame') . 'WHERE sender = ' . $iProfileId);

        // DELETE PICTURES ALBUMS AND PICTURES
        $oDb->exec('DELETE FROM' . Db::prefix('Pictures') . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('AlbumsPictures') . 'WHERE profileId = ' . $iProfileId);

        // DELETE VIDEOS ALBUMS AND VIDEOS
        $oDb->exec('DELETE FROM' . Db::prefix('Videos') . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('AlbumsVideos') . 'WHERE profileId = ' . $iProfileId);

        // DELETE FRIENDS
        $oDb->exec('DELETE FROM' . Db::prefix('MembersFriends') . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('MembersFriends') . 'WHERE friendId = ' . $iProfileId);

        // DELETE WALL
        $oDb->exec('DELETE FROM' . Db::prefix('MembersWall') . 'WHERE profileId = ' . $iProfileId);

        // DELETE BACKGROUND
        $oDb->exec('DELETE FROM' . Db::prefix('MembersBackground') . 'WHERE profileId = ' . $iProfileId);

        // DELETE NOTES
        $oDb->exec('DELETE FROM' . Db::prefix('NotesCategories') . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('Notes') . 'WHERE profileId = ' . $iProfileId);

        // DELETE LIKE
        $oDb->exec('DELETE FROM' . Db::prefix('Likes') . 'WHERE keyId LIKE ' . Db::getInstance()->quote('%' . $sUsername . '.html'));

        // DELETE PROFILE VISITS
        $oDb->exec('DELETE FROM' . Db::prefix('MembersWhoViews') . 'WHERE profileId = ' . $iProfileId);
        $oDb->exec('DELETE FROM' . Db::prefix('MembersWhoViews') . 'WHERE visitorId = ' . $iProfileId);

        // DELETE REPORT
        $oDb->exec('DELETE FROM' . Db::prefix('Report') . 'WHERE spammerId = ' . $iProfileId);

        // DELETE TOPICS of FORUMS
        /*
        No! Ghost Profile is ultimately the best solution!
        WARNING: Do not change this part of code without asking permission from Pierre-Henry Soria
        */
        //$oDb->exec('DELETE FROM' . Db::prefix('ForumsMessages') . 'WHERE profileId = ' . $iProfileId);
        //$oDb->exec('DELETE FROM' . Db::prefix('ForumsTopics') . 'WHERE profileId = ' . $iProfileId);

        // DELETE NOTIFICATIONS
        $oDb->exec('DELETE FROM' . Db::prefix('MembersNotifications') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // DELETE PRIVACY SETTINGS
        $oDb->exec('DELETE FROM' . Db::prefix('MembersPrivacy') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // DELETE INFO FIELDS
        $oDb->exec('DELETE FROM' . Db::prefix('MembersInfo') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // DELETE USER
        $oDb->exec('DELETE FROM' . Db::prefix('Members') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        unset($oDb); // Destruction of the object
    }

    /**
     * @param string $sUsernameSearch
     * @param string $sTable Default 'Members'
     *
     * @return stdClass data of users (profileId, username, sex)
     */
    public function getUsernameList($sUsernameSearch, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId, username, sex FROM' . Db::prefix($sTable) . 'WHERE username <> \'' . PH7_GHOST_USERNAME . '\' AND username LIKE :username');
        $rStmt->bindValue(':username', '%' . $sUsernameSearch . '%', \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    /**
     * Get profiles data.
     *
     * @param string $sOrder
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return stdClass Data of users
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

        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix('Members') . 'AS m LEFT JOIN' . Db::prefix('MembersPrivacy') . 'AS p USING(profileId)
            LEFT JOIN' . Db::prefix('MembersInfo') . 'AS i USING(profileId) WHERE (username <> \'' . PH7_GHOST_USERNAME . '\') AND (searchProfile = \'yes\')
            AND (username IS NOT NULL) AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND (country IS NOT NULL)
            AND (city IS NOT NULL) AND (groupId <> 1) AND (groupId <> 9) AND (ban = 0)' .
            $sSqlHideLoggedProfile . $sSqlShowOnlyWithAvatars . $sOrder . $sSqlLimit
        );

        if ($bHideUserLogged) {
            $rStmt->bindValue(':profileId', $this->iProfileId, \PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow;
    }

    /**
     * Get the users from the location data.
     *
     * @param string $sCountry
     * @param string $sCity
     * @param boolean $bCount
     * @param string $sOrder
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return stdClass|integer Object with the users list returned or integer for the total number users returned.
     */
    public function getGeoProfiles($sCountry, $sCity, $bCount, $sOrder, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sOrder = !$bCount ? SearchCoreModel::order($sOrder, SearchCoreModel::DESC) : '';

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? '*' : 'COUNT(m.profileId) AS totalUsers';

        $sSqlCity = !empty($sCity) ? 'AND (city LIKE :city)' : '';

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Members') . 'AS m LEFT JOIN' . Db::prefix('MembersInfo') . 'AS i USING(profileId)
            WHERE (username <> \'' . PH7_GHOST_USERNAME . '\') AND (country = :country) ' . $sSqlCity . ' AND (username IS NOT NULL)
            AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND (country IS NOT NULL)
            AND (city IS NOT NULL) AND (groupId <> 1) AND (groupId <> 9) AND (ban = 0)' . $sOrder . $sSqlLimit
        );
        $rStmt->bindParam(':country', $sCountry, \PDO::PARAM_STR, 2);

        if (!empty($sCity)) {
            $rStmt->bindValue(':city', '%' . $sCity . '%', \PDO::PARAM_STR);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int)$oRow->totalUsers;
        }

    }

    /**
     * Updating the privacy settings.
     *
     * @param integer $iProfileId
     *
     * @return stdClass
     */
    public function getPrivacySetting($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'privacySetting' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $iProfileId = (int)$iProfileId;

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('MembersPrivacy') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Get the Profile ID of a user.
     *
     * @param string $sEmail Default NULL
     * @param string $sUsername Default NULL
     * @param string $sTable Default 'Members'
     *
     * @return integer|boolean The Member ID if it is found or FALSE if not found.
     */
    public function getId($sEmail = null, $sUsername = null, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'id' . $sEmail . $sUsername . $sTable, static::CACHE_TIME);

        if (!$iData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            if (!empty($sEmail)) {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1');
                $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
            } else {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE username = :username LIMIT 1');
                $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
            }
            $rStmt->execute();

            if ($rStmt->rowCount() === 0) {
                return false;
            } else {
                $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
                Db::free($rStmt);
                $iData = (int)$oRow->profileId;
                unset($oRow);
                $this->cache->put($iData);
            }
        }

        return $iData;
    }

    /**
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return string The email address of a member
     */
    public function getEmail($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'email' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT email FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->email;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Retrieves the username from the user ID.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return string The Username of member
     */
    public function getUsername($iProfileId, $sTable = 'Members')
    {
        if ($iProfileId === PH7_ADMIN_ID) {
            return t('Administration of %site_name%');
        }

        $this->cache->start(self::CACHE_GROUP, 'username' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT username FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->username;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Retrieves the first name from the user ID.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return string The first name of member
     */
    public function getFirstName($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'firstName' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT firstName FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->firstName;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Get Gender (sex) of a user.
     *
     * @param integer $iProfileId Default NULL
     * @param string $sUsername Default NULL
     * @param string $sTable Default 'Members'
     *
     * @return string The sex of a member
     */
    public function getSex($iProfileId = null, $sUsername = null, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'sex' . $iProfileId . $sUsername . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            if (!empty($iProfileId)) {
                $rStmt = Db::getInstance()->prepare('SELECT sex FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            } else {
                $rStmt = Db::getInstance()->prepare('SELECT sex FROM' . Db::prefix($sTable) . 'WHERE username=:username LIMIT 1');
                $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
            }

            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->sex;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Get Match sex for a member (so only from the Members table, because Affiliates and Admins don't have match sex).
     *
     * @param integer $iProfileId
     *
     * @return string The User's birthdate.
     */
    public function getMatchSex($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'matchsex' . $iProfileId, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT matchSex FROM' . Db::prefix('Members') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = $oRow->matchSex;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Get Birth Date of a user.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     *
     * @return string The User's birthdate.
     */
    public function getBirthDate($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'birthdate' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT birthDate FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = $oRow->birthDate;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Get user's group.
     *
     * @param integer $iProfileId
     * @param string sTable Default 'Members'
     *
     * @return integer The Group ID of a member
     */
    public function getGroupId($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'groupId' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT groupId FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = (int)$oRow->groupId;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * Get the membership(s) data.
     *
     * @param integer $iGroupId Group ID. Select only the specific membership from a group ID.
     *
     * @return stdClass The membership(s) data.
     */
    public function getMemberships($iGroupId = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'memberships' . $iGroupId, static::CACHE_TIME);

        if (!$mData = $this->cache->get()) {
            $bIsGroupId = !empty($iGroupId);
            $sSqlGroup = ($bIsGroupId) ? ' WHERE groupId = :groupId ' : ' ';

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Memberships') . $sSqlGroup . 'ORDER BY enable DESC, name ASC');
            if (!empty($iGroupId)) $rStmt->bindValue(':groupId', $iGroupId, \PDO::PARAM_INT);
            $rStmt->execute();
            $mData = ($bIsGroupId) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($mData);
        }

        return $mData;
    }

    /**
     * Get the membership details of a user.
     *
     * @param integer $iProfileId
     *
     * @return stdClass The membership detais.
     */
    public function getMembershipDetails($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'membershipdetails' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $sSql = 'SELECT m.*, g.expirationDays, g.name AS membershipName FROM' . Db::prefix('Members') . 'AS m INNER JOIN ' . Db::prefix('Memberships') .
                'AS g USING(groupId) WHERE profileId = :profileId LIMIT 1';

            $rStmt = Db::getInstance()->prepare($sSql);
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Check if membership is expired.
     *
     * @param integer $iProfileId
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     *
     * @return boolean
     */
    public function checkMembershipExpiration($iProfileId, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId FROM' . Db::prefix('Members') . 'AS m INNER JOIN' . Db::prefix('Memberships') .
            'AS pay USING(groupId) WHERE (pay.expirationDays = 0 OR DATE_ADD(m.membershipDate, INTERVAL pay.expirationDays DAY) >= :currentTime)
             AND (m.profileId = :profileId) LIMIT 1');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_INT);
        $rStmt->execute();

        return ($rStmt->rowCount() === 1);
    }

    /**
     * Update the membership group of a user.
     *
     * @param integer $iNewGroupId The new ID of membership group.
     * @param integer $iProfileId The user ID.
     * @param string $sDateTime In date format: 0000-00-00 00:00:00
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function updateMembership($iNewGroupId, $iProfileId, $sDateTime = null)
    {
        $bIsTime = !empty($sDateTime);

        $sSqlTime = ($bIsTime) ? ',membershipDate = :dateTime ' : ' ';
        $sSqlQuery = 'UPDATE' . Db::prefix('Members') . 'SET groupId = :groupId' .
            $sSqlTime . 'WHERE profileId = :profileId LIMIT 1';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':groupId', $iNewGroupId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        if ($bIsTime) {
            $rStmt->bindValue(':dateTime', $sDateTime, \PDO::PARAM_STR);
        }

        return $rStmt->execute();
    }

    /**
     * Get Info Fields from profile ID.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'MembersInfo'
     *
     * @return stdClass
     */
    public function getInfoFields($iProfileId, $sTable = 'MembersInfo')
    {
        $this->cache->start(self::CACHE_GROUP, 'infoFields' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oColumns = $rStmt->fetch(\PDO::FETCH_OBJ);
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
     * @return string
     */
    public function getUserWithAvatarOnlySql()
    {
        return ' AND avatar IS NOT NULL AND approvedAvatar = 1';
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone() {}
}
