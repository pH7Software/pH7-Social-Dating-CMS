<?php
/**
 * @title          Record Class
 * @desc           Record Database Class. It is an Object-relational mapping (ORM).
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Mvc / Model / Engine
 * @version        0.9
 */

namespace PH7\Framework\Mvc\Model\Engine;
defined('PH7') or exit('Restricted access');

/**
 * @class Singleton Class
 */
class Record
{

    /**
     * @var array $_aErrors
     */
    private $_aErrors = array();

    /**
     * @var string $_sSql
     */
    private $_sSql;

    /**
     * @var array $_aValues
     */
    private $_aValues = array();

    /**
     * Import the Singleton trait.
     */
    use \PH7\Framework\Pattern\Singleton;

    /**
     * We do not put a "__construct" and "__clone" "private" because it is already included in the class \PH7\Framework\Pattern\Base that is included in the \PH7\Framework\Pattern\Singleton class.
     */

    /**
     * Add a value to the values array.
     *
     * @param string $sKey the array key
     * @param string $sValue The value
     * @return object this
     */
    public function addValue($sKey, $sValue)
    {
        $this->_aValues[$sKey] = $sValue;
        return $this;
    }

    /**
     * Set the values.
     *
     * @param array $aValues
     * @return void
     */
    public function setValues(array $aValues)
    {
        $this->_aValues = $aValues;
    }

    /**
     * Get the message of exception.
     *
     * @return string The message of exception.
     */
    public function getErrors()
    {
        if (count($this->_aErrors) > 1)
        {
            $sErrMsg = '';
            foreach ($this->_aErrors as $sError)
                $sErrMsg .= $sError . "\r\n";

            return $sErrMsg;
        }
    }

    /**
     * Delete a recored from a table.
     *
     * @param string $sTable The table name
     * @param string $sField
     * @param string $sId
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function delete($sTable, $sField, $sId)
    {
        try
        {
            // Get the primary key name
            $this->_sSql = 'DELETE FROM ' . Db::prefix($sTable) . " WHERE $sField =:id";
            $rStmt = Db::getInstance()->prepare($this->_sSql);
            $rStmt->bindParam(':id', $sId, \PDO::PARAM_STR);
            $bStatus = $rStmt->execute();
            Db::free($rStmt);
            return $bStatus;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * Insert a value into a table.
     *
     * @param string $sTable
     * @param array $aValues
     * @return integer The last Insert Id on success or throw PDOException on failure.
     */
    public function insert($sTable, array $aValues)
    {
        $aValues = is_null($aValues) ? $this->_aValues : $aValues;
        $this->_sSql = 'INSERT INTO ' . Db::prefix($sTable) . 'SET ';

        $oCachingIterator = new \CachingIterator(new \ArrayIterator($aValues));

        try
        {
            foreach ($oCachingIterator as $sField => $sValue)
            {
                $this->_sSql .= $sField . ' = :' . $sField;
                $this->_sSql .= $oCachingIterator->hasNext() ? ',' : '';
                $this->_sSql .= "\n";
            }

            $rStmt = Db::getInstance()->prepare($this->_sSql);

            foreach ($aValues as $sField => $sValue)
                $rStmt->bindParam(':' . $sField, $sValue);

            $rStmt->execute($aValues);
            Db::free($rStmt);
            return Db::getInstance()->lastInsertId();
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * Update a value in a table.
     *
     * @param string $sTable
     * @param string $sField, The field to be updated
     * @param string $sValue The new value
     * @param string $sPk The primary key. Default: NULL
     * @param string $sId The id. Default: NULL
     * @return integer The number of rows on success or throw PDOException on failure.
     */
    public function update($sTable, $sField, $sValue, $sPk = null, $sId = null)
    {
        try
        {
            $bIsWhere = isset($sPk, $sId);
            $sSqlWhere = ($bIsWhere) ? " WHERE $sPk = :id" : '';
            $this->_sSql = 'UPDATE ' . Db::prefix($sTable) . " SET $sField= :value" . $sSqlWhere;
            $rStmt = Db::getInstance()->prepare($this->_sSql);
            $rStmt->bindParam(':value', $sValue, \PDO::PARAM_STR);
            if ($bIsWhere) $rStmt->bindParam(':id', $sId, \PDO::PARAM_STR);
            $rStmt->execute();
            $iRow = $rStmt->rowCount();
            Db::free($rStmt);
            return $iRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * SQL Query.
     *
     * @param string $sSql
     * @return mixed (object | boolean) Object on success or throw PDOException on failure.
     *
     */
    public function query($sSql)
    {
        try
        {
            $rStmt = Db::getInstance()->prepare($sSql);
            $rStmt->execute();
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * Execute a Record query.
     *
     * @return mixed (object | boolean) Returns a PDOStatement object, or FALSE on failure.
     */
    public function execute()
    {
        $rStmt = Db::getInstance()->query($this->_sSql);
        Db::free();
        return $rStmt;
    }

    /**
     * Select All In One Query.
     *
     * @param mixed (array | string) $mArrWhat
     * @param mixed (array | string) $mTable
     * @param string $sField
     * @param string $sId
     * @param array $aJoin Default ''
     * @param string $sWhere Default NULL
     * @return mixed (object | boolean) Object on success or throw PDOException on failure.
     */
    public function selectAllInOne($mArrWhat, $mTable, $sField = '', $sId = '', $aJoin = '', $sWhere = null)
    {
        try
        {
            if (is_array(Db::prefix($mTable)))
                $mTable = implode(', ', Db::prefix($mTable));

            if (is_array($mArrWhat))
            {
                if (count($mArrWhat))
                    $mWhat = implode(', ', $mArrWhat);
                else
                    $mWhat = '*';
            }
            else
            {
                $mWhat = $mArrWhat;
            }

            $sSql = "SELECT $mWhat FROM " . Db::prefix($mTable) . " WHERE $sField = :id";

            if (is_array($aJoin) && count($aJoin) == 2)
                $sSql .= "LEFT JOIN $aJoin[0] ON $sTable.$aJoin[1] = $aJoin[0].$aJoin[1]";

            if (!empty($sWhere))
                $sSql .= " WHERE $sWhere";

            $rStmt = Db::getInstance()->prepare($sSql);
            $rStmt->bindParam(':id', $sId);
            $rStmt->execute();
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $oRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * Update statement.
     * Sample: Record::getInstance()->updates('MyTable', array('foo' => 'bar', 'foo2' => 'bar4', 'foo9' => 'bar4'))->where('fooID', 22)->execute();
     *
     * @param string $sTable
     * @param array $aValues
     * @return object this
     */
    public function updates($sTable, array $aValues)
    {
        $aValues = is_null($aValues) ? $this->_aValues : $aValues;
        $this->_sSql = 'UPDATE' . Db::prefix($sTable) . 'SET ';

        $oCachingIterator = new \CachingIterator(new \ArrayIterator($aValues));

        foreach ($oCachingIterator as $sField => $sValue)
        {
            $this->_sSql .= $sField . ' = ' . Db::getInstance()->quote($sValue);
            $this->_sSql .= $oCachingIterator->hasNext() ? ',' : ' ';
        }

        return $this;
    }

    /**
     * Select statement.
     *
     * @param string $sArrWhat
     * @param string $sTable
     * @return object this
     *
     */
    public function select($sArrWhat, $sTable)
    {
        $this->_sSql = 'SELECT ' . $sArrWhat . ' FROM' . Db::prefix($sTable);
        return $this;
    }

    /**
     * Where clause.
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     *
     */
    public function where($sField, $sValue)
    {
        $this->_sSql .= " WHERE $sField=$sValue";
        return $this;
    }

    /**
     * Set limit.
     *
     * @param integer $iOffset
     * @param integer $iLimit
     * @return object this
     */
    public function limit($iOffset, $iLimit)
    {
        $this->_sSql .= " LIMIT $iOffset, $iLimit";
        return $this;
    }

    /**
     * Add an AND clause.
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     */
    public function andClause($sField, $sValue)
    {
        $this->_sSql .= " AND $sField = $sValue";
        return $this;
    }

    /**
     * Add an ORDER BY clause.
     *
     * @param string $sField
     * @param string $sOrder
     * @return object this
     */
    public function orderBy($sField, $sOrder = 'ASC')
    {
        $this->_sSql .= " ORDER BY $sField $sOrder";
        return $this;
    }

    /**
     * Add a GROUP BY clause.
     *
     * @param string $sGroup
     * @return object this
     */
    public function groupBy($sGroup)
    {
        $this->_sSql .= " GROUP BY $sGroup";
        return $this;
    }

    /**
     * Add an HAVING clause.
     *
     * @param string $sHaving
     * @return object this
     */
    public function having($sHaving)
    {
        $this->_sSql .= " HAVING $sHaving";
        return $this;
    }

}
