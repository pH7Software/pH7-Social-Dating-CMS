<?php
/**
 * @title            Db (Database) Class
 * @desc             PDO Singleton Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 * @version          1.6
 */

namespace PH7\Framework\Mvc\Model\Engine;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Core;

/**
 * @class Singleton Class
 */
class Db
{

    const ASC = 'ASC', DESC = 'DESC', RAND = 'RAND()';

    /**
     * Static attributes of the class.
     * Holds an insance of self with the \PDO class.
     *
     * @staticvar string $_sDsn Data Source Name
     * @staticvar string $_sUsername
     * @staticvar string $_sPassword
     * @staticvar string $_sPrefix
     * @staticvar array $_aDriverOptions
     * @staticvar integer $_iCount
     * @staticvar float $_fTime
     * @staticvar object $_oInstance
     */
    private static $_sDsn, $_sUsername, $_sPassword, $_sPrefix, $_aDriverOptions, $_iCount = 0, $_fTime = 0.0, $_oInstance = NULL, $_oDb;

    /**
     * The constructor is set to private, so nobody can create a new instance using new.
     */
    private function __construct() {}

    /**
     * @return object Returns the PDO instance class or create initial connection.
     */
    public static function getInstance($sDsn = NULL, $sUsername = NULL, $sPassword = NULL, $aDriverOptions = NULL, $sPrefix = NULL)
    {
        if(NULL === self::$_oInstance)
        {
            if(!empty($sDsn))
                self::$_sDsn = $sDsn;

            if(!empty($sUsername))
                self::$_sUsername = $sUsername;

            if(!empty($sPassword))
                self::$_sPassword = $sPassword;

            if(!empty($aDriverOptions))
                self::$_aDriverOptions = $aDriverOptions;

            if(!empty($sPrefix))
                self::$_sPrefix = $sPrefix;

            self::$_oInstance = new static;

            try
            {
                self::$_oDb = new \PDO(self::$_sDsn, self::$_sUsername, self::$_sPassword, self::$_aDriverOptions);
                self::$_oDb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            catch (Exception $oE)
            {
                exit('Error Establishing a Database Connection');
            }

            static::checkMySqlVersion();
        }

        return self::$_oInstance;
    }

    /**
     * Initiates a transaction.
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        return self::$_oDb->beginTransaction();
    }

    /**
     * Commits a transaction.
     *
     * @return boolean
     */
    public function commit()
    {
        return self::$_oDb->commit();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle.
     *
     * @return string
     */
    public function errorCode()
    {
        return self::$_oDb->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        return self::$_oDb->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     *
     * @param string $sStatement
     * @return mixed (boolean | integer)
     */
    public function exec($sStatement)
    {
        $fStartTime = microtime(true);
        $mReturn = self::$_oDb->exec($sStatement);
        $this->_increment();
        $this->_addTime($fStartTime, microtime(true));
        return $mReturn;
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @param int $iAttribute
     * @return mixed
     */
    public function getAttribute($iAttribute)
    {
        return self::$_oDb->getAttribute($iAttribute);
    }

    /**
     * Return an array of available PDO drivers.
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return self::$_oDb->getAvailableDrivers();
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sName Name of the sequence object from which the ID should be returned. Default NULL
     * @return string
     */
    public function lastInsertId($sName = null)
    {
        return self::$_oDb->lastInsertId($sName);
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @param string $sStatement A valid SQL statement for the target database server.
     * @return PDOStatement
     */
    public function prepare($sStatement)
    {
        $fStartTime = microtime(true);
        $bReturn = self::$_oDb->prepare($sStatement);
        $this->_increment();
        $this->_addTime($fStartTime, microtime(true));
        return $bReturn;
    }

    /**
     * Execute an SQL prepared with prepare() method.
     *
     * @param string $sStatement
     * @return boolean
     */
    public function execute($sStatement)
    {
        return self::$_oDb->execute($sStatement);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     *
     * @param string $sStatement
     * @return mixed (object | boolean) PDOStatement object, or FALSE on failure.
     */
    public function query($sStatement)
    {
        $fStartTime = microtime(true);
        $mReturn = self::$_oDb->query($sStatement);
        $this->_increment();
        $this->_addTime($fStartTime, microtime(true));
        return $mReturn;
    }

    /**
     * Execute query and return all rows in assoc array.
     *
     * @param string $sStatement
     * @return array
     */
    public function queryFetchAllAssoc($sStatement)
    {
        return self::$_oDb->query($sStatement)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and return one row in assoc array.
     *
     * @param string $sStatement
     * @return array
     */
    public function queryFetchRowAssoc($sStatement)
    {
        return self::$_oDb->query($sStatement)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Execute query and select one column only.
     *
     * @param string $sStatement
     * @return mixed
     */
    public function queryFetchColAssoc($sStatement)
    {
        return self::$_oDb->query($sStatement)->fetchColumn();
    }

    /**
     * Quotes a string for use in a query.
     *
     * @param string $sInput
     * @param integer $iParameterType
     * @return string
     */
    public function quote($sInput, $iParameterType = 0)
    {
        return self::$_oDb->quote($sInput, $iParameterType);
    }

    /**
     * Rolls back a transaction.
     *
     * @return boolean
     */
    public function rollBack()
    {
        return self::$_oDb->rollBack();
    }

    /**
     * Set an attribute.
     *
     * @param integer $iAttribute
     * @param mixed $mValue
     * @return boolean
     */
    public function setAttribute($iAttribute, $mValue)
    {
        return self::$_oDb->setAttribute($iAttribute, $mValue);
    }

    /**
     * Count the number of requests.
     *
     * @return float number
     */
    public static function queryCount()
    {
        return self::$_iCount;
    }

    /**
     * Show all tables.
     *
     * @return mixed (object | boolean) PDOStatement object, or FALSE on failure.
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
        return self::$_fTime;
    }

    /**
     * If table name is empty, only prefix will be returned otherwise the table name with its prefix will be returned.
     *
     * @param string $sTable Table name. Default ''
     * @param boolean $bTrim With or without a space before and after the table name. Default valut is FALSE, so with space before and after table name.
     * @return string prefixed table name, just prefix if table name is empty.
     */
    public static function prefix($sTable = '', $bTrim = false)
    {
        $sSpace = (!$bTrim) ? ' ' : '';
        return ($sTable !== '') ? $sSpace . self::$_sPrefix . $sTable . $sSpace : self::$_sPrefix;
    }

    /**
     * Free database.
     *
     * @param object \PDOStatement $rStmt Close cursor of PDOStatement class. Default NULL
     * @param boolean $bCloseConnection Close connection of PDO. Default FALSE
     * @return void
     */
    public static function free(\PDOStatement &$rStmt = NULL, $bCloseConnection = FALSE)
    {
        // Close Cursor
        if(NULL !== $rStmt)
        {
            $rStmt->closeCursor();
            unset($rStmt);
        }

        // Free instance of the PDO object
        if(TRUE === $bCloseConnection)
            self::$_oDb = NULL;
    }

    /**
     * Optimizing tables.
     *
     * @return void
     */
    public static function optimize()
    {
        $oAllTables = static::showTables();
        while($aTableNames = $oAllTables->fetch()) static::getInstance()->query('OPTIMIZE TABLE '. $aTableNames[0]);
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
        while($aTableNames = $oAllTables->fetch()) static::getInstance()->query('REPAIR TABLE '. $aTableNames[0]);
        unset($oAllTables);
    }

    /**
     * Check MySQL version.
     *
     * @return void
     */
    public static function checkMySqlVersion()
    {
        $sMySQLVer = self::$_oDb->getAttribute(\PDO::ATTR_SERVER_VERSION);
        if(version_compare($sMySQLVer, PH7_REQUIRE_SQL_VERSION, '<'))
            exit('ERROR: Your MySQL version is ' . $sMySQLVer . '. pH7CMS requires MySQL ' . PH7_REQUIRE_SQL_VERSION . ' or newer.');
    }

    /**
     * Add Time Query.
     *
     * @param float $fStartTime
     * @param float $fEndTime
     * @return void
     */
    private function _addTime($fStartTime, $fEndTime)
    {
        self::$_fTime += round($fEndTime - $fStartTime, 6);
    }

    /**
     * Increment function.
     *
     * @return void
     */
    private function _increment()
    {
        ++self::$_iCount;
    }

    /**
     * Like the constructor, we make __clone private, so nobody can clone the instance.
     */
    private function __clone()
    {
    }

}
