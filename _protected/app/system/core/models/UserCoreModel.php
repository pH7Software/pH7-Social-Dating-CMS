<?php
/**
 * @title          User Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 * @version        1.0
 */
namespace PH7;

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Mvc\Model\Engine\Util\Various,
PH7\Framework\Date\CDateTime;

// Abstract Class
class UserCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const
    CACHE_GROUP = 'db/sys/mod/user',
    CACHE_TIME = 604800,
    RAND = 'rand',
    LATEST = 'joinDate',
    LAST_ACTIVITY = 'lastActivity';

    protected $sCurrentDate;

    public function __construct()
    {
        parent::__construct();

        $this->sCurrentDate = (new Framework\Date\CDateTime)->get()->dateTime('Y-m-d H:i:s');
    }

    public static function checkGroup()
    {
        $oSession = new Framework\Session\Session;
        if (!$oSession->exists('member_group_id'))
        {
            $oSession->regenerateId();
            $oSession->set('member_group_id', '1'); // Visitor's group
        }
        unset($oSession);

        $rStmt = Db::getInstance()->prepare('SELECT permissions FROM' . Db::prefix('Memberships') . 'WHERE groupId = :groupId LIMIT 1');
        $rStmt->bindParam(':groupId', $_SESSION[Framework\Config\Config::getInstance()->values['session']['prefix'] . 'member_group_id'], \PDO::PARAM_INT);
        $rStmt->execute();
        $oFetch = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return Framework\CArray\ObjArr::toObject(unserialize($oFetch->permissions));
    }

    /**
     * Login method for Members and Affiliate, but not for Admins, since another method PH7\AdminModel::adminLogin() there is even more secure.
     *
     * @param string $sEmail
     * @param string $sPassword
     * @param string $sTable
     * @return mixed (boolean "true" or string "message")
     */
    public function login($sEmail, $sPassword, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT email, password, prefixSalt, suffixSalt FROM'.Db::prefix($sTable).'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email',$sEmail, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        $sDbEmail = (!empty($oRow->email)) ? $oRow->email : '';
        $sDbPassword = (!empty($oRow->password)) ? $oRow->password : '';

        if ($sEmail !== $sDbEmail)
            return 'email_does_not_exist';
        elseif (Framework\Security\Security::hashPwd($oRow->prefixSalt, $sPassword, $oRow->suffixSalt) !== $sDbPassword)
            return 'password_does_not_exist';
        else
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
     * @return void
     */
    public function sessionLog($sEmail, $sUsername, $sFirstName, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable.'LogSession') . '(email, username, firstName, ip)
        VALUES (:email, :username, :firstName, :ip)');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $sFirstName, \PDO::PARAM_STR);
        $rStmt->bindValue(':ip', Framework\Ip\Ip::get(), \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * Read Profile Data.
     *
     * @param integer $iProfileId The user ID
     * @param string $sTable Default 'Members'
     * @return object The data of a member
     */
    public function readProfile($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'readProfile' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT * FROM'.Db::prefix($sTable).'WHERE profileId= :profileId LIMIT 1');
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
     * @return integer Total Users
     */
    public function total($sTable = 'Members', $iDay = 0, $sGenger = 'all')
    {
        Various::checkModelTable($sTable);
        $iDay = (int) $iDay;

        $bIsDay = ($iDay > 0);
        $bIsGenger = ($sTable === 'Members' ? ($sGenger === 'male' || $sGenger === 'female' || $sGenger === 'couple') : ($sGenger === 'male' || $sGenger === 'female'));

        $sSqlDay = $bIsDay ? ' AND (joinDate + INTERVAL :day DAY) > NOW()' : '';
        $sSqlGender = $bIsGenger ? ' AND sex = :gender' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) AS totalUsers FROM' . Db::prefix($sTable) . 'WHERE username <> \''.PH7_GHOST_USERNAME.'\'' . $sSqlDay . $sSqlGender);
        if ($bIsDay) $rStmt->bindValue(':day', $iDay, \PDO::PARAM_INT);
        if ($bIsGenger) $rStmt->bindValue(':gender', $sGenger, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int) $oRow->totalUsers;
    }

    /**
     * Update profile data.
     *
     * @param string $sSection
     * @param string $sValue
     * @param integer $iProfileId Profile ID
     * @param string $sTable Default 'Members'
     * @return void
     */
    public function updateProfile($sSection, $sValue, $iProfileId, $sTable ='Members')
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
     * @param string $sPrefixSalt
     * @param string $sSuffixSalt
     * @return boolean
     */
    public function changePassword($sEmail, $sNewPassword, $sTable, $sPrefixSalt, $sSuffixSalt)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET password = :newPassword, prefixSalt = :prefixSalt, suffixSalt = :suffixSalt WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':newPassword',Framework\Security\Security::hashPwd($sPrefixSalt,$sNewPassword,$sSuffixSalt), \PDO::PARAM_INT);
        $rStmt->bindValue(':prefixSalt', $sPrefixSalt, \PDO::PARAM_INT);
        $rStmt->bindValue(':suffixSalt', $sSuffixSalt, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Set a new hash validation.
     *
     * @param integer $iProfileId
     * @param string $sHash
     * @param string $sTable
     */
    public function setNewHashValidation($iProfileId, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET hashValidation = :hash WHERE profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':hash', $sHash, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * Check the hash validation.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable
     * @return boolean
     */
    public function checkHashValidation($sEmail, $sHash, $sTable)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) FROM' . Db::prefix($sTable) . 'WHERE email = :email AND hashValidation = :hash LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':hash', $sHash, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->fetchColumn() == 1);
    }

    /**
     * Search users.
     *
     * @param array $aParams
     * @param boolean $bCount
     * @param integer $iOffset
     * @param integer $iLimit
     * @param string $sTable Default "Members"
     * @return mixed (object | integer) object for the users list returned or integer for the total number users returned.
     */
    public function search(array $aParams, $bCount, $iOffset, $iLimit, $sTable = 'Members')
    {
        Various::checkModelTable($sTable); // Checks if this table is correct

        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $bIsLimit = ($bCount === false);
        $bIsSingleAge = !empty($aParams['age']);
        $bIsAge = empty($aParams['age']) && !empty($aParams['age1']) && !empty($aParams['age2']);
        $bIsHeight = !empty($aParams['height']);
        $bIsWeight = !empty($aParams['weight']);
        $bIsCountry = !empty($aParams['country']);
        $bIsCity = !empty($aParams['city']);
        $bIsState = !empty($aParams['state']);
        $bIsZipCode = !empty($aParams['zip_code']);
        $bIsMail = !empty($aParams['mail']);
        $bIsSex = !empty($aParams['sex']);


        $sSqlLimit = ($bIsLimit) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = ($bIsLimit) ? '*' : 'COUNT(m.profileId) AS totalUsers';
        $sSqlSingleAge = ($bIsSingleAge) ? ' AND birthDate LIKE :year ' : '';
        $sSqlAge = ($bIsAge) ? ' AND birthDate BETWEEN DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :age2 YEAR) AND DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL :age1 YEAR) ' : '';
        $sSqlHeight = ($bIsHeight) ? ' AND height = :height ' : '';
        $sSqlWeight = ($bIsWeight) ? ' AND weight = :weight ' : '';
        $sSqlCountry = ($bIsCountry) ? ' AND country = :country ' : '';
        $sSqlCity = ($bIsCity) ? ' AND city LIKE :city ' : '';
        $sSqlState = ($bIsState) ? ' AND state LIKE :state ' : '';
        $sSqlZipCode = ($bIsZipCode) ? ' AND zipCode LIKE :zipCode ' : '';
        $sSqlEmail = ($bIsMail) ? ' AND email LIKE :email ' : '';
        $sSqlOnline = (!empty($aParams['online'])) ? ' AND userStatus = 1 AND lastActivity > DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL ' . Framework\Mvc\Model\DbConfig::getSetting('userTimeout') . ' MINUTE) ' : '';

        if (empty($aParams['order'])) $aParams['order'] = SearchCoreModel::LATEST; // Default is "ORDER BY joinDate"
        if (empty($aParams['sort'])) $aParams['sort'] =  SearchCoreModel::ASC; // Default is "ascending"
        $sSqlOrder = SearchCoreModel::order($aParams['order'], $aParams['sort']);

        $sSqlMatchSex = (!empty($aParams['match_sex'])) ? ' AND matchSex LIKE :matchSex ' : '';

        $sGender = '';
        if ($bIsSex)
        {
            $aSex = $aParams['sex'];
            foreach ($aSex as $sSex)
            {
                if ($sSex === 'male')
                {
                    $sGender .= '\'male\', ';
                }

                if ($sSex === 'female')
                {
                    $sGender .= '\'female\', ';
                }

                if ($sSex === 'couple')
                {
                    $sGender .= '\'couple\', ';
                }
            }

            $sSqlSex = ($bIsSex) ? ' AND sex IN ( ' . substr($sGender, 0, -2) . ' ) ' : '';
        }
        else
        {
            $sSqlSex = '';
        }

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix($sTable) . 'AS m LEFT JOIN' . Db::prefix('MembersPrivacy') . 'AS p ON m.profileId = p.profileId WHERE username <> \''.PH7_GHOST_USERNAME.'\' AND userSaveViews = \'yes\' AND groupId = \'2\'' .  $sSqlMatchSex .  $sSqlSex . $sSqlSingleAge . $sSqlAge . $sSqlCountry . $sSqlCity . $sSqlState . $sSqlZipCode . $sSqlHeight . $sSqlWeight . $sSqlEmail . $sSqlOnline . $sSqlOrder . $sSqlLimit);

        if (!empty($aParams['match_sex'])) $rStmt->bindValue(':matchSex', '%' . $aParams['match_sex'] . '%', \PDO::PARAM_STR);
        if ($bIsSingleAge) $rStmt->bindValue(':year', '%' . ((int)$this->sCurrentDate-$aParams['age']) . '%', \PDO::PARAM_INT);
        if ($bIsAge) $rStmt->bindValue(':age1', $aParams['age1'], \PDO::PARAM_INT);
        if ($bIsAge) $rStmt->bindValue(':age2', $aParams['age2'], \PDO::PARAM_INT);
        if ($bIsHeight) $rStmt->bindValue(':height', $aParams['height'], \PDO::PARAM_INT);
        if ($bIsWeight) $rStmt->bindValue(':weight', $aParams['weight'], \PDO::PARAM_INT);
        if ($bIsCountry) $rStmt->bindValue(':country', $aParams['country'], \PDO::PARAM_STR);
        if ($bIsCity) $rStmt->bindValue(':city', '%' . $aParams['city'] . '%', \PDO::PARAM_STR);
        if ($bIsState) $rStmt->bindValue(':state', '%' . $aParams['state'] . '%', \PDO::PARAM_STR);
        if ($bIsZipCode) $rStmt->bindValue(':zipCode', '%' . $aParams['zip_code'] . '%', \PDO::PARAM_STR);
        if ($bIsMail) $rStmt->bindValue(':email', '%' . $aParams['mail'] . '%', \PDO::PARAM_STR);

        if ($bIsLimit)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if ($bIsLimit)
        {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        else
        {
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
     * @return boolean
     */
    public function isOnline($iProfileId, $iTime = 1)
    {
        $iProfileId = (int) $iProfileId;
        $iTime = (int) $iTime;

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix('Members') . 'WHERE profileId = :profileId
            AND userStatus = 1 AND lastActivity > DATE_SUB(:currentTime, INTERVAL :time MINUTE) LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':time', $iTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $this->sCurrentDate, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 1);
    }

    /**
     * Set the user status.
     *
     * @param integer iProfileId
     * @param integer $iStatus Values: 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
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
     * @return integer The user status. 0 = Offline, 1 = Online, 2 = Busy, 3 = Away
     */
    public function getUserStatus($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'userStatus' . $iProfileId, static::CACHE_TIME);

        if (!$iData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT userStatus FROM ' . Db::prefix('Members') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $iData = (int) $oRow->userStatus;
            unset($oRow);
            $this->cache->put($iData);
        }

        return $iData;
    }

    /**
     * Update Notifications
     *
     * @param string $sSection
     * @param string $sValue
     * @param integer $iProfileId Profile ID
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
     * @return integer
     */
    public function getNotification($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'notification' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT * FROM ' . Db::prefix('MembersNotifications') . 'WHERE profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * Set the last activity of a user.
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
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
     * @param integer $iStatus
     * @param string $sTable 'Members'
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
     * @return mixed (object | boolean) Returns the data member (email, username, firstName, hashValidation) on success, otherwise returns false if there is an error.
     */
    public function getHashValidation($sEmail, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT email, username, firstName, hashValidation FROM' . Db::prefix($sTable) . 'WHERE email = :email AND active = 2');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow =  $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    /**
     * Valid on behalf of a user with the hash.
     *
     * @param string $sEmail
     * @param string $sHash
     * @param string $sTable Default 'Members'
     * @return boolean
     */
    public function validateAccount($sEmail, $sHash, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET active = 1 WHERE email = :email AND hashValidation = :hash AND active = 2');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':hash', $sHash, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * Adding a User.
     *
     * @param array $aData
     * @return integer The ID of the User.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO'.Db::prefix('Members').'(email, username, password, firstName, lastName, sex, matchSex, birthDate, country, city, state, zipCode, description, website, socialNetworkSite, active, ip, hashValidation, prefixSalt, suffixSalt, joinDate, lastActivity, groupId)
        VALUES (:email, :username, :password, :firstName, :lastName, :sex, :matchSex, :birthDate, :country, :city, :state, :zipCode, :description, :website, :socialNetworkSite, :active, :ip, :hashValidation, :prefixSalt, :suffixSalt, :joinDate, :lastActivity, :groupId)');
        $rStmt->bindValue(':email',   trim($aData['email']), \PDO::PARAM_STR);
        $rStmt->bindValue(':username', trim($aData['username']), \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Framework\Security\Security::hashPwd($aData['prefix_salt'],$aData['password'],$aData['suffix_salt'], Framework\Security\Security::USER), \PDO::PARAM_INT);
        $rStmt->bindValue(':firstName', $aData['first_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':lastName', $aData['last_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':sex', $aData['sex'], \PDO::PARAM_STR);
        $rStmt->bindValue(':matchSex', Form::setVal($aData['match_sex']), \PDO::PARAM_STR);
        $rStmt->bindValue(':birthDate', $aData['birth_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':country', $aData['country'], \PDO::PARAM_STR);
        $rStmt->bindValue(':city', $aData['city'], \PDO::PARAM_STR);
        $rStmt->bindValue(':state', $aData['state'], \PDO::PARAM_STR);
        $rStmt->bindValue(':zipCode', $aData['zip_code'], \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $aData['description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':website', trim($aData['website']), \PDO::PARAM_STR);
        $rStmt->bindValue(':socialNetworkSite', trim($aData['social_network_site']), \PDO::PARAM_STR);
        $rStmt->bindValue(':active', (!empty($aData['is_active']) ? $aData['is_active'] : 1), \PDO::PARAM_INT);
        $rStmt->bindValue(':ip', $aData['ip'], \PDO::PARAM_INT);
        $rStmt->bindValue(':hashValidation', (!empty($aData['hash_validation']) ? $aData['hash_validation'] : null), \PDO::PARAM_STR);
        $rStmt->bindValue(':prefixSalt', $aData['prefix_salt'], \PDO::PARAM_INT);
        $rStmt->bindValue(':suffixSalt', $aData['suffix_salt'], \PDO::PARAM_INT);
        $rStmt->bindValue(':joinDate', $this->sCurrentDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':lastActivity', $this->sCurrentDate, \PDO::PARAM_STR);
        $rStmt->bindValue(':groupId', (int) Framework\Mvc\Model\DbConfig::getSetting('defaultMembershipGroupId'), \PDO::PARAM_INT);
        $rStmt->execute();
        $iProfileId = (int) Db::getInstance()->lastInsertId();
        Db::free($rStmt);
        $this->setDefaultPrivacySetting($iProfileId);
        $this->setDefaultNotification($iProfileId);
        return $iProfileId;
    }

    /**
     * Sets default privacy settings.
     *
     * @param integer $iProfileId
     * @return Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultPrivacySetting($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO'.Db::prefix('MembersPrivacy').'(profileId, privacyProfile, searchProfile, userSaveViews)
            VALUES (:profileId, \'all\', \'yes\', \'yes\')');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Sets default notifications.
     *
     * @param integer $iProfileId
     * @return Returns TRUE on success or FALSE on failure.
     */
    public function setDefaultNotification($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO'.Db::prefix('MembersNotifications').'(profileId, enableNewsletters)
            VALUES (:profileId, 0)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
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
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitJoin($sIp, $iWaitTime, $sCurrentTime, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE ip = :ip AND DATE_ADD(joinDate, INTERVAL :waitTime MINUTE) > :currentTime');
        $rStmt->bindValue(':ip', $sIp, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 0);
    }


    /********** AVATAR **********/

    /**
     * Update or add a new avatar.
     *
     * @param integer $iProfileId
     * @param string $sAvatar
     * @param integer $iApproved
     * @return boolean
     */
    public function setAvatar($iProfileId, $sAvatar, $iApproved)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Members') . 'SET avatar = :avatar, approvedAvatar = :approvedAvatar WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':avatar', $sAvatar, \PDO::PARAM_STR);
        $rStmt->bindValue(':approvedAvatar', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Gets avatar.
     *
     * @param integer $iProfileId
     * @param integer $iApproved ('1' = approved '0' = pending, null = approved and pending) Default NULL
     * @return object The Avatar (SQL alias is pic), profileId and approvedAvatar
     */
    public function getAvatar($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'avatar' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $sSqlApproved = (!empty($iApproved)) ? ' AND approvedAvatar = :approvedAvatar ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT profileId, avatar AS pic, approvedAvatar FROM' . Db::prefix('Members') . ' WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            if (!empty($iApproved)) $rStmt->bindValue(':approvedAvatar', $iApproved, \PDO::PARAM_INT);
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
     * @return boolean
     */
    public function deleteAvatar($iProfileId)
    {
        return $this->setAvatar($iProfileId, null, 1);
    }


    /********** BACKGROUND **********/

    /**
     * Gets file of a user background.
     *
     * @param integer $iProfileId
     * @param integer $iApproved ('1' = approved '0' = pending, null = approved and pending) Default NULL
     * @return boolean
     */
    public function getBackground($iProfileId, $iApproved = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'background' . $iProfileId, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
            $sSqlApproved = (!empty($iApproved)) ? ' AND approved = :approved ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT file FROM'.Db::prefix('MembersBackground').'WHERE profileId = :profileId' . $sSqlApproved . 'LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            if (!empty($iApproved)) $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
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
     * @param integer $iApproved Default 1
     * @return boolean
     */
    public function addBackground($iProfileId, $sFile, $iApproved = 1)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO'.Db::prefix('MembersBackground').'(profileId, file, approved) VALUES (:profileId, :file, :approved)');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':file', $sFile, \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Delete profile background.
     *
     * @param integer $iProfileId
     * @return boolean
     */
    public function deleteBackground($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM'.Db::prefix('MembersBackground').'WHERE profileId = :profileId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        return $rStmt->execute();
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
        $sUsername = (string) $sUsername;
        $iProfileId = (int) $iProfileId;

        if ($sUsername == PH7_GHOST_USERNAME) exit('You cannot delete this profile!');

        $oDb = Db::getInstance();

        // DELETE MESSAGES
        $oDb->query('DELETE FROM' . Db::prefix('Messages') . 'WHERE sender = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('Messages') . 'WHERE recipient = ' . $iProfileId);

        // DELETE MESSAGES OF MESSENGER
        $oDb->query('DELETE FROM' . Db::prefix('Messenger') . 'WHERE fromUser = ' . Db::getInstance()->quote($sUsername));
        $oDb->query('DELETE FROM' . Db::prefix('Messenger') . 'WHERE toUser = ' . Db::getInstance()->quote($sUsername));

        // DELETE PROFILE COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsProfile') . 'WHERE sender = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('CommentsProfile') . 'WHERE recipient = ' . $iProfileId);

        // DELETE PICTURE COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsPicture') . 'WHERE sender = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('CommentsPicture') . 'WHERE recipient = ' . $iProfileId);

        // DELETE VIDEO COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsVideo') . 'WHERE sender = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('CommentsVideo') . 'WHERE recipient = ' . $iProfileId);

        // DELETE NOTE COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsNote') . 'WHERE sender = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('CommentsNote') . 'WHERE recipient = ' . $iProfileId);

        // DELETE BLOG COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsBlog') . 'WHERE sender = ' . $iProfileId);

        // DELETE GAME COMMENTS
        $oDb->query('DELETE FROM' . Db::prefix('CommentsGame') . 'WHERE sender = ' . $iProfileId);

        // DELETE PICTURES ALBUMS AND PICTURES
        $oDb->query('DELETE FROM' . Db::prefix('Pictures') . 'WHERE profileId = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('AlbumsPictures') . 'WHERE profileId = ' . $iProfileId);

        // DELETE VIDEOS ALBUMS AND VIDEOS
        $oDb->query('DELETE FROM' . Db::prefix('Videos') . 'WHERE profileId = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('AlbumsVideos') . 'WHERE profileId = ' . $iProfileId);

        // DELETE FRIENDS
        $oDb->query('DELETE FROM' . Db::prefix('MembersFriends') . 'WHERE profileId = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('MembersFriends') . 'WHERE friendId = ' . $iProfileId);

        // DELETE WALL
        $oDb->query('DELETE FROM' . Db::prefix('MembersWall') . 'WHERE profileId = ' . $iProfileId);

        // DELETE BACKGROUND
        $oDb->query('DELETE FROM' . Db::prefix('MembersBackground') . 'WHERE profileId = ' . $iProfileId);

        // DELETE NOTES
        $oDb->query('DELETE FROM' . Db::prefix('NotesCategories') . 'WHERE profileId = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('Notes') . 'WHERE profileId = ' . $iProfileId);

        // DELETE LIKE
        $oDb->query('DELETE FROM' . Db::prefix('Likes') . 'WHERE keyId LIKE ' . Db::getInstance()->quote('%' . $sUsername . '.html'));

        // DELETE PROFILE VISITS
        $oDb->query('DELETE FROM' . Db::prefix('MembersWhoViews') . 'WHERE profileId = ' . $iProfileId);
        $oDb->query('DELETE FROM' . Db::prefix('MembersWhoViews') . 'WHERE visitorId = ' . $iProfileId);

        // DELETE REPORT
        $oDb->query('DELETE FROM' . Db::prefix('Report') . 'WHERE spammerId = ' . $iProfileId);

        // DELETE TOPICS of FORUMS
        /*
        No! Ghost Profile is ultimately the best solution!
        WARNING: Do not change this part of code without asking permission from Pierre-Henry Soria
        */
        //$oDb->query('DELETE FROM' . Db::prefix('ForumsMessages') . 'WHERE profileId = ' . $iProfileId);
        //$oDb->query('DELETE FROM' . Db::prefix('ForumsTopics') . 'WHERE profileId = ' . $iProfileId);

        // DELETE NOTIFICATIONS
        $oDb->query('DELETE FROM' . Db::prefix('MembersNotifications') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // DELETE PRIVACY SETTINGS
        $oDb->query('DELETE FROM' . Db::prefix('MembersPrivacy') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        // DELETE USER
        $oDb->query('DELETE FROM' . Db::prefix('Members') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');

        unset($oDb); // Destruction of the object
    }

    /**
     * Order By method.
     *
     * @access protected
     * @param string $sOrder
     * @return string SQL order by query
     */
    protected function profileOrderBy($sOrder)
    {
        switch($sOrder)
        {
            case static::RAND:
                $sSqlOrderBy =  'RAND() ';
            break;

            case static::LATEST:
            case static::LAST_ACTIVITY:
                $sSqlOrderBy =  $sOrder . ' DESC ';
            break;

            default:
                throw new Framework\Error\CException\PH7Exception('The argument order is wrong, please correct it, please.');
        }

        return ' ORDER BY ' . $sSqlOrderBy;
    }

    /**
     * @param string $sUsernameSearch
     * @param string $sTable Default 'Members'
     * @return object data of users (profileId, username, sex)
     */
    public function getUsernameList($sUsernameSearch, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT profileId, username, sex FROM' . Db::prefix($sTable) . 'WHERE username <> \''.PH7_GHOST_USERNAME.'\' AND username LIKE :username');
        $rStmt->bindValue(':username', '%'.$sUsernameSearch.'%', \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oRow;
    }

    /**
     * Get profiles data.
     *
     * @param string $sOrder Default PH7\UserCoreModel::LAST_ACTIVITY
     * @param integer $iOffset Default NULL
     * @param integer $iLimit Default NULL
     * @return object Data of users
     */
    public function getProfiles($sOrder = self::LAST_ACTIVITY, $iOffset = null, $iLimit = null)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;
        $sOrder = $this->profileOrderBy($sOrder);

        $bIsLimit = (null !== $iOffset && null !== $iLimit);
        $sSqlLimit = ($bIsLimit ? 'LIMIT :offset, :limit' : '');
        $rStmt = Db::getInstance()->prepare('SELECT * FROM '.Db::prefix('Members').' WHERE (username <> \''.PH7_GHOST_USERNAME.'\')
            AND (username IS NOT NULL) AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND (country IS NOT NULL) AND (city IS NOT NULL) AND (groupId=\'2\')' . $sOrder . $sSqlLimit);
        if ($bIsLimit) $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        if ($bIsLimit) $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
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
     * @return mixed (object | integer) object for the users list returned or integer for the total number users returned.
     */
    public function getGeoProfiles($sCountry, $sCity, $bCount, $sOrder, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $bIsLimit = ($bCount === false);

        $sOrder = ($bIsLimit) ? $this->profileOrderBy($sOrder) : '';
        $sSqlLimit = ($bIsLimit) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = ($bIsLimit) ? '*' : 'COUNT(profileId) AS totalUsers';

        $sSqlCity = (!empty($sCity)) ?  'AND (city LIKE :city)' : '';
        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM'.Db::prefix('Members').' WHERE (username <> \''.PH7_GHOST_USERNAME.'\') AND (country = :country) ' . $sSqlCity . ' AND (username IS NOT NULL) AND (firstName IS NOT NULL) AND (sex IS NOT NULL) AND (matchSex IS NOT NULL) AND (country IS NOT NULL) AND (city IS NOT NULL) AND (groupId=\'2\')' . $sOrder . $sSqlLimit);
        $rStmt->bindValue(':country', $sCountry, \PDO::PARAM_STR);
        (!empty($sCity)) ? $rStmt->bindValue(':city', '%' . $sCity . '%', \PDO::PARAM_STR) : '';

        if ($bIsLimit)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if ($bIsLimit)
        {
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return (int) $oRow->totalUsers;
        }

    }

    /**
     * Updating the privacy settings.
     *
     * @param integer $iProfileId
     * @return object
     */
    public function getPrivacySetting($iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'privacySetting' . $iProfileId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $iProfileId = (int) $iProfileId;

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
     * @param string $sEmail Default NULL
     * @param string $sUsername Default NULL
     * @param string $sTable Default 'Members'
     * @return integer The Member ID
     */
    public function getId($sEmail = null, $sUsername = null, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'id' . $sEmail . $sUsername . $sTable, static::CACHE_TIME);

        if (!$iData = $this->cache->get())
        {
            Various::checkModelTable($sTable);

            if (!empty($sEmail))
            {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1');
                $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
            }
            else
            {
                $rStmt = Db::getInstance()->prepare('SELECT profileId FROM' . Db::prefix($sTable) . 'WHERE username = :username LIMIT 1');
                $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
            }
            $rStmt->execute();

            if ($rStmt->rowCount() == 0)
            {
                return false;
            }
            else
            {
               $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
               Db::free($rStmt);
               $iData = (int) $oRow->profileId;
               unset($oRow);
               $this->cache->put($iData);
            }
        }

        return $iData;
    }

    /**
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     * @return string The email address of a member
     */
    public function getEmail($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'email' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
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
     * Retrieves the username from the user ID
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     * @return string The Username of member
     */
    public function getUsername($iProfileId, $sTable = 'Members')
    {
        if ($iProfileId === PH7_ADMIN_ID) return t('Administration of %site_name%');

        $this->cache->start(self::CACHE_GROUP, 'username' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
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
     * Retrieves the first name from the user ID
     *
     * @param integer $iProfileId
     * @param string $sTable Default 'Members'
     * @return string The first name of member
     */
    public function getFirstName($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'firstName' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT firstName FROM' . Db::prefix($sTable) . 'WHERE profileId=:profileId LIMIT 1');
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
     * @param integer $iProfileId Default NULL
     * @param string $sUsername Default NULL
     * @param string $sTable Default 'Members'
     * @return string The sex of a member
     */
    public function getSex($iProfileId = null, $sUsername = null, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'sex' . $iProfileId . $sUsername . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
            Various::checkModelTable($sTable);

            if (!empty($iProfileId))
            {
                $rStmt = Db::getInstance()->prepare('SELECT sex FROM' . Db::prefix($sTable) . 'WHERE profileId=:profileId LIMIT 1');
                $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            }
            else
            {
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
     * Get user's group.
     *
     * @param integer $iProfileId
     * @param string sTable Default 'Members'
     * @return integer The Group ID of a member
     */
    public function getGroupId($iProfileId, $sTable = 'Members')
    {
        $this->cache->start(self::CACHE_GROUP, 'groupId' . $iProfileId . $sTable, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
            Various::checkModelTable($sTable);

            $rStmt = Db::getInstance()->prepare('SELECT groupId FROM' . Db::prefix($sTable) . 'WHERE profileId=:profileId LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = (int) $oRow->groupId;
            unset($oRow);
            $this->cache->put($sData);
        }
        return $sData;
    }

    /**
     * Get the membership data
     *
     * @param integer $iWhereId Default NULL
     * @return object The data
     */
    public function getMemberships($iWhereId = null)
    {
        $this->cache->start(self::CACHE_GROUP, 'memberships' . $iWhereId, static::CACHE_TIME);

        if (!$mData = $this->cache->get())
        {
            $sSqlWhere = (!empty($iWhereId)) ? ' WHERE groupId = :groupId ' : ' ';
            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Memberships') . $sSqlWhere . 'ORDER BY enable DESC, name ASC');
            if (!empty($iWhereId)) $rStmt->bindValue(':groupId', $iWhereId, \PDO::PARAM_INT);
            $rStmt->execute();
            $mData = (!empty($iWhereId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($mData);
        }
        return $mData;
    }

    /**
     * Check if membership is expired.
     *
     * @param integer $iProfileId
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @return boolean
     */
    public function checkMembershipExpiration($iProfileId, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT m.profileId FROM' . Db::prefix('Members') . 'AS m INNER JOIN' . Db::prefix('Memberships') . 'AS pay ON m.groupId = pay.groupId WHERE pay.expirationDays = 0 OR DATE_SUB(m.membershipExpiration, INTERVAL pay.expirationDays DAY) <= :currentTime AND profileId = :profileId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_INT);
        $rStmt->execute();
        return ($rStmt->rowCount() == 1);
    }

    /**
     * Update the membership group of user.
     *
     * @param integer $iNewGroupId The new ID of membership group.
     * @param integer $iProfileId The ID of user.
     * @param integer $iPrice
     * @param string $sDateTime In date format: 0000-00-00 00:00:00
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function updateMembership($iNewGroupId, $iProfileId, $iPrice, $sDateTime)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Members') . 'AS m INNER JOIN' . Db::prefix('Membership') . 'AS pay ON m.groupId = pay.groupId SET m.groupId = :groupId, m.membershipExpiration = :dateTime WHERE m.profileId = :profileId AND pay.price = :price LIMIT 1');
        $rStmt->bindValue(':groupId', $iNewGroupId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':price', $iPrice, \PDO::PARAM_INT);
        $rStmt->bindValue(':dateTime', $sDateTime, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * Clone is set to private to stop cloning.
     *
     * @access private
     */
    private function __clone() {}

}
