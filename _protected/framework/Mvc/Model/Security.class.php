<?php
/**
 * @title            Security Model Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\SecurityCore;

class Security
{
    /** @var string */
    private $sIp;

    /** @var string */
    private $sCurrentTime;

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
    public function blockIp($sIp, $iExpir = 86400)
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
     *
     * @param string $sEmail
     * @param string $sUsername
     * @param string $sPassword
     * @param int $sStatus
     * @param string $sTable Default DbTableName::MEMBER
     *
     * @return void
     */
    public function addLoginLog($sEmail, $sUsername, $sPassword, $sStatus, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable . '_log_login') . '(email, username, password, status, ip)
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
     * Blocking access to the login page after exceeded login attempts.
     *
     * @param int $iMaxAttempts
     * @param int $iAttemptTime
     * @param string $sEmail Email address of member.
     * @param PH7Tpl $oView
     * @param string $sTable Default DbTableName::MEMBER
     *
     * @return bool Returns TRUE if attempts are allowed, FALSE otherwise.
     */
    public function checkLoginAttempt(
        $iMaxAttempts,
        $iAttemptTime,
        $sEmail,
        PH7Tpl $oView,
        $sTable = DbTableName::MEMBER
    )
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable . '_attempts_login') . 'WHERE ip = :ip LIMIT 1');
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();

        if ($rStmt->rowCount() == 1) {
            $oRow = $rStmt->fetch(PDO::FETCH_OBJ);

            if ($oRow->attempts >= $iMaxAttempts) {
                $sLockoutTime = (new DateTime($oRow->lastLogin))->add(DateInterval::createFromDateString("$iAttemptTime minutes"))->format('Y-m-d H:i:s');

                if ($sLockoutTime > $this->sCurrentTime) {
                    /**
                     * Send email to prevent that someone tries to hack their member account.
                     * We test that the number of attempts equals the number of maximim tantatives to avoid duplication of sending emails.
                     */
                    if ($oRow->attempts == $iMaxAttempts) {
                        (new SecurityCore)->sendAlertLoginAttemptsExceeded($iMaxAttempts, $iAttemptTime, $this->sIp, $sEmail, $oView, $sTable);
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
     * Add Loging Attempt.
     *
     * @param string $sTable
     *
     * @return void
     */
    public function addLoginAttempt($sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix($sTable . '_attempts_login') . 'WHERE ip = :ip LIMIT 1');
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();

        if ($rStmt->rowCount() == 1) {
            $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
            $iAttempts = $oRow->attempts + 1;
            $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable . '_attempts_login') . 'SET attempts = :attempts, lastLogin = :currentTime WHERE ip = :ip');
            $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
            $rStmt->bindValue(':attempts', $iAttempts, PDO::PARAM_INT);
            $rStmt->bindValue(':currentTime', $this->sCurrentTime, PDO::PARAM_STR);
            $rStmt->execute();
        } else {
            $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix($sTable . '_attempts_login') . '(ip, attempts, lastLogin) VALUES (:ip, 1, :lastLogin)');
            $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
            $rStmt->bindValue(':lastLogin', $this->sCurrentTime, PDO::PARAM_STR);
            $rStmt->execute();
        }

        Db::free($rStmt);
    }

    /**
     * Clear Login Attempts.
     *
     * @param string $sTable
     *
     * @return void
     */
    public function clearLoginAttempts($sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix($sTable . '_attempts_login') . 'WHERE ip = :ip');
        $rStmt->bindValue(':ip', $this->sIp, PDO::PARAM_STR);
        $rStmt->execute();
        Db::free($rStmt);
    }
}
