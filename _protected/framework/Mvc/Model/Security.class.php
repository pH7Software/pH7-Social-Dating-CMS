<?php
/**
 * @title            Security Model Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.3
 */

namespace PH7\Framework\Mvc\Model;

defined('PH7') or exit('Restricted access');

use DateInterval;
use DateTime;
use PDO;
use PH7\DbTableName;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\SecurityCore;

class Security
{
    private string $sIp;

    private string $sCurrentTime;

    public function __construct()
    {
        $this->sIp = Ip::get();
        $this->sCurrentTime = (new CDateTime)->get()->dateTime('Y-m-d H:i:s');
    }

    /**
     * Block user IP
     *
     * @param string $sIp IP address.
     * @param int $iExpir Expiration in seconds. Default 86400
     *
     * @return bool Returns TRUE if no IP has been found (and the new IP has been added to the block list), otherwise FALSE.
     */
    public function blockIp(string $sIp, int $iExpir = 86400): bool
    {
        $iExpir = time() + (int)$iExpir;
        $rStmt = Db::getInstance()->prepare('SELECT ip FROM' . Db::prefix(DbTableName::BLOCK_IP) . 'WHERE ip = :ip LIMIT 1');
        $rStmt->bindValue(':ip', $sIp, PDO::PARAM_STR);
        $rStmt->execute();

        // If IP is not found
        if ($rStmt->rowCount() == 0) {
            $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::BLOCK_IP) . 'VALUES (:ip, :expiration)');
            $rStmt->bindValue(':ip', $sIp, PDO::PARAM_STR);
            $rStmt->bindValue(':expiration', $iExpir, PDO::PARAM_INT);
            $rStmt->execute();
            return true;
        }

        return false;
    }

    /**
     * Add Login Log.
     */
    public function addLoginLog(string $sEmail, string $sUsername, string $sPassword, string $sStatus, string $sTable = DbTableName::MEMBER_LOG_LOGIN): void
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable) . '(email, username, password, status, ip)
        VALUES (:email, :username, :password, :status, :ip)');
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindValue(':username', $sUsername, PDO::PARAM_STR);
        $rStmt->bindValue(':password', $sPassword, PDO::PARAM_STR);
        $rStmt->bindValue(':status', $sStatus, PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * Set User Log Session.
     *
     * @param int $iProfileId
     * @param string $sEmail
     * @param string $sFirstName
     * @param string $sTable
     */
    public function addSessionLog($iProfileId, string $sEmail, string $sFirstName, string $sTable = DbTableName::MEMBER_LOG_SESS): void
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix($sTable) . '(profileId, email, firstName, ip)
            VALUES (:profileId, :email, :firstName, :ip)'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':email', $sEmail, PDO::PARAM_STR);
        $rStmt->bindValue(':firstName', $sFirstName, PDO::PARAM_STR);
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * Blocking access to the login page after exceeded login attempts.
     *
     * @param int $iMaxAttempts
     * @param int $iAttemptTime
     * @param string $sEmail Email address of member.
     * @param Templatable $oView
     * @param string $sTable Default DbTableName::MEMBER
     *
     * @return bool Returns TRUE if attempts are allowed, FALSE otherwise.
     */
    public function checkLoginAttempt(
        int $iMaxAttempts,
        int $iAttemptTime,
        string $sEmail,
        Templatable $oView,
        string $sTable = DbTableName::MEMBER_ATTEMPT_LOGIN
    ): bool
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable) . 'WHERE ip = :ip LIMIT 1');
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();

        if ($rStmt->rowCount() == 1) {
            $oAttemptRow = $rStmt->fetch(PDO::FETCH_OBJ);

            if ($oAttemptRow->attempts >= $iMaxAttempts) {
                $sLockoutTime = (new DateTime($oAttemptRow->lastLogin))->add(
                    DateInterval::createFromDateString("$iAttemptTime minutes")
                )->format('Y-m-d H:i:s');

                if ($this->sCurrentTime <= $sLockoutTime) {
                    /**
                     * Send email to prevent that someone tries to hack their member account.
                     * We test that the number of attempts equals the number of maximum attempts to avoid sending several same emails.
                     */
                    if ($oAttemptRow->attempts == $iMaxAttempts) {
                        (new SecurityCore)->sendLoginAttemptsExceededAlert(
                            $iMaxAttempts,
                            $iAttemptTime,
                            $this->sIp,
                            $sEmail,
                            $oView,
                            $sTable
                        );
                    }
                } else {
                    // Clear Login Attempts
                    $this->clearLoginAttempts($sTable);
                    return true; // Authorized
                }
                return false; // Banned
            }
        }
        return true; // Authorized
    }

    /**
     * Add Login Attempt.
     */
    public function addLoginAttempt(string $sTable = DbTableName::MEMBER_ATTEMPT_LOGIN): void
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix($sTable) . 'WHERE ip = :ip LIMIT 1'
        );
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();

        if ($rStmt->rowCount() == 1) {
            $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
            $iAttempts = $oRow->attempts + 1;
            $rStmt = Db::getInstance()->prepare(
                'UPDATE' . Db::prefix($sTable) . 'SET attempts = :attempts, lastLogin = :currentTime WHERE ip = :ip'
            );
            $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
            $rStmt->bindValue(':attempts', $iAttempts, PDO::PARAM_INT);
            $rStmt->bindValue(':currentTime', $this->sCurrentTime, PDO::PARAM_STR);
            $rStmt->execute();
        } else {
            $rStmt = Db::getInstance()->prepare(
                'INSERT INTO' . Db::prefix($sTable) . '(ip, attempts, lastLogin) VALUES (:ip, 1, :lastLogin)'
            );
            $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
            $rStmt->bindValue(':lastLogin', $this->sCurrentTime, PDO::PARAM_STR);
            $rStmt->execute();
        }

        Db::free($rStmt);
    }

    /**
     * Clear Login Attempts.
     */
    public function clearLoginAttempts(string $sTable = DbTableName::MEMBER_ATTEMPT_LOGIN): void
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix($sTable) . 'WHERE ip = :ip'
        );
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }
}
