<?php
/**
 * @title            Db (Database) Class
 * @desc             PDO Singleton Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 * @version          1.6
 */

namespace PH7\Framework\Mvc\Model\Engine;

defined('PH7') or exit('Restricted access');

use PDO;
use PDOStatement;

/**
 * @class Singleton Class
 */
class Db
{
    const REQUIRED_SQL_VERSION = 5.0;

    const ASC = 'ASC';
    const DESC = 'DESC';
    const RAND = 'RAND()';
    const SET_DELIMITER = ',';

    /** @var string */
    private static $sDsn;

    /** @var string */
    private static $sUsername;

    /** @var string */
    private static $sPassword;

    /** @var string */
    private static $sPrefix;

    /** @var array */
    private static $aDriverOptions;

    /** @var int */
    private static $iCount = 0;

    /** @var float */
    private static $fTime = 0.0;

    /** @var self|null */
    private static $oInstance = null;

    /** @var PDO */
    private static $oDb;

    /**
     * The constructor is set to private, so nobody can create a new instance using new.
     */
    private function __construct()
    {
    }

    /**
     * @param string|null $sDsn
     * @param string|null $sUsername
     * @param string|null $sPassword
     * @param array|null $aDriverOptions
     * @param string|null $sPrefix
     *
     * @return self Returns the PDO instance class or create initial connection.
     */
    public static function getInstance($sDsn = null, $sUsername = null, $sPassword = null, $aDriverOptions = null, $sPrefix = null)
    {
        if (self::$oInstance === null) {
            if (!empty($sDsn)) {
                self::$sDsn = $sDsn;
            }

            if (!empty($sUsername)) {
                self::$sUsername = $sUsername;
            }

            if (!empty($sPassword)) {
                self::$sPassword = $sPassword;
            }

            if (!empty($aDriverOptions)) {
                self::$aDriverOptions = $aDriverOptions;
            }

            if (!empty($sPrefix)) {
                self::$sPrefix = $sPrefix;
            }

            self::$oInstance = new static;

            try {
                self::$oDb = new PDO(self::$sDsn, self::$sUsername, self::$sPassword, self::$aDriverOptions);
                self::$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $oE) {
                exit('Error Establishing a Database Connection');
            }

            static::checkMySqlVersion();
        }

        return self::$oInstance;
    }

    /**
     * Initiates a transaction.
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return self::$oDb->beginTransaction();
    }

    /**
     * Commits a transaction.
     *
     * @return bool
     */
    public function commit()
    {
        return self::$oDb->commit();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle.
     *
     * @return string
     */
    public function errorCode()
    {
        return self::$oDb->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        return self::$oDb->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     *
     * @param string $sStatement
     *
     * @return bool|int
     */
    public function exec($sStatement)
    {
        $fStartTime = microtime(true);
        $mReturn = self::$oDb->exec($sStatement);
        $this->increment();
        $this->addTime($fStartTime, microtime(true));

        return $mReturn;
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @param int $iAttribute
     *
     * @return mixed
     */
    public function getAttribute($iAttribute)
    {
        return self::$oDb->getAttribute($iAttribute);
    }

    /**
     * Return an array of available PDO drivers.
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return self::$oDb->getAvailableDrivers();
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sName Name of the sequence object from which the ID should be returned.
     *
     * @return string
     */
    public function lastInsertId($sName = null)
    {
        return self::$oDb->lastInsertId($sName);
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $sStatement A valid SQL statement for the target database server.
     *
     * @return PDOStatement
     */
    public function prepare($sStatement)
    {
        $fStartTime = microtime(true);
        $bReturn = self::$oDb->prepare($sStatement);
        $this->increment();
        $this->addTime($fStartTime, microtime(true));

        return $bReturn;
    }

    /**
     * Execute an SQL prepared with prepare() method.
     *
     * @param string $sStatement
     *
     * @return bool
     */
    public function execute($sStatement)
    {
        return self::$oDb->execute($sStatement);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     *
     * @param string $sStatement
     *
     * @return PDOStatement|bool Returns PDOStatement object, or FALSE on failure.
     */
    public function query($sStatement)
    {
        $fStartTime = microtime(true);
        $mReturn = self::$oDb->query($sStatement);
        $this->increment();
        $this->addTime($fStartTime, microtime(true));

        return $mReturn;
    }

    /**
     * Execute query and return all rows in assoc array.
     *
     * @param string $sStatement
     *
     * @return array
     */
    public function queryFetchAllAssoc($sStatement)
    {
        return self::$oDb->query($sStatement)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and return one row in assoc array.
     *
     * @param string $sStatement
     *
     * @return array
     */
    public function queryFetchRowAssoc($sStatement)
    {
        return self::$oDb->query($sStatement)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and select one column only.
     *
     * @param string $sStatement
     *
     * @return mixed
     */
    public function queryFetchColAssoc($sStatement)
    {
        return self::$oDb->query($sStatement)->fetchColumn();
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $sInput
     * @param int $iParameterType
     *
     * @return string
     */
    public function quote($sInput, $iParameterType = PDO::PARAM_NULL)
    {
        return self::$oDb->quote($sInput, $iParameterType);
    }

    /**
     * Rolls back a transaction.
     *
     * @return bool
     */
    public function rollBack()
    {
        return self::$oDb->rollBack();
    }

    /**
     * Set an attribute.
     *
     * @param int $iAttribute
     * @param mixed $mValue
     *
     * @return bool
     */
    public function setAttribute($iAttribute, $mValue)
    {
        return self::$oDb->setAttribute($iAttribute, $mValue);
    }

    /**
     * Count the number of requests.
     *
     * @return float number
     */
    public static function queryCount()
    {
        return self::$iCount;
    }

    /**
     * Show all tables.
     *
     * @return PDOStatement|bool Returns PDOStatement object, or FALSE on failure.
     */
    public static function showTables()
    {
        return static::getInstance()->query('SHOW TABLES');
    }

    /**
     * Time Query.
     *
     * @return float
     */
    public static function time()
    {
        return self::$fTime;
    }

    /**
     * If table name is empty, only prefix will be returned otherwise the table name with its prefix will be returned.
     *
     * @param string $sTable Table name. Default ''
     * @param bool $bSpace With or without a space before and after the table name. Default value is FALSE, so with space before and after table name.
     *
     * @return string prefixed table name, just prefix if table name is empty.
     */
    public static function prefix($sTable = '', $bSpace = true)
    {
        $sSpace = $bSpace ? ' ' : '';

        return ($sTable !== '') ? $sSpace . self::$sPrefix . $sTable . $sSpace : self::$sPrefix;
    }

    /**
     * Free database.
     *
     * @param PDOStatement $rStmt Close cursor of PDOStatement class.
     * @param bool $bCloseConnection Close connection of PDO.
     *
     * @return void
     */
    public static function free(PDOStatement &$rStmt = null, $bCloseConnection = false)
    {
        // Close Cursor
        if ($rStmt !== null) {
            $rStmt->closeCursor();
            unset($rStmt);
        }

        // Free instance of the PDO object
        if ($bCloseConnection === true) {
            self::$oDb = null;
        }
    }

    /**
     * Optimizing tables.
     *
     * @return void
     */
    public static function optimize()
    {
        $oAllTables = static::showTables();
        while ($aTableNames = $oAllTables->fetch()) {
            static::getInstance()->query('OPTIMIZE TABLE ' . $aTableNames[0]);
        }
        unset($oAllTables);
    }

    /**
     * Repair tables.
     *
     * @return void
     */
    public static function repair()
    {
        $oAllTables = static::showTables();

        while ($aTableNames = $oAllTables->fetch()) {
            static::getInstance()->query('REPAIR TABLE ' . $aTableNames[0]);
        }

        unset($oAllTables);
    }

    /**
     * Check MySQL version.
     *
     * @return void
     */
    public static function checkMySqlVersion()
    {
        $sMySQLVer = self::$oDb->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (version_compare($sMySQLVer, self::REQUIRED_SQL_VERSION, '<')) {
            $sMsg = 'ERROR: Your MySQL version is ' . $sMySQLVer . '. pH7CMS requires MySQL ' . self::REQUIRED_SQL_VERSION . ' or newer.';
            exit($sMsg);
        }
    }

    /**
     * Add Time Query.
     *
     * @param float $fStartTime
     * @param float $fEndTime
     *
     * @return void
     */
    private function addTime($fStartTime, $fEndTime)
    {
        self::$fTime += round($fEndTime - $fStartTime, 6);
    }

    /**
     * Increment function.
     *
     * @return void
     */
    private function increment()
    {
        self::$iCount++;
    }

    /**
     * Like the constructor, we make __clone private, so nobody can clone the instance.
     */
    private function __clone()
    {
    }
}
