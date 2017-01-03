<?php
/**
 * @title          Record Class
 * @desc           Record Database Class. It's the pH7CMS home-made Object-Relational Mapping (ORM).
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Mvc / Model / Engine
 * @version        1.1
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
     * We do not put a "__construct" and "__clone" "private" because it is already included in the \PH7\Framework\Pattern\Statik trait that is included in the \PH7\Framework\Pattern\Singleton trait.
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
        $sErrMsg = '';
        if (count($this->_aErrors) > 1)
        {
            foreach ($this->_aErrors as $sError)
                $sErrMsg .= $sError . "\r\n";
        }
        return $sErrMsg;
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
            $oDb = Db::getInstance();

            // We start the transaction.
            $oDb->beginTransaction();

            $this->_sSql = 'DELETE FROM' . Db::prefix($sTable) . "WHERE $sField = :id";
            $rStmt = $oDb->prepare($this->_sSql);
            $rStmt->bindParam(':id', $sId);
            $bStatus = $rStmt->execute();

            // If all goes well, we commit the transaction.
            $oDb->commit();

            Db::free($rStmt);
            return $bStatus;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();

            // We cancel the transaction if an error occurs.
            $oDb->rollBack();
            return false;
        }
    }

    /**
     * Insert a value into a table.
     *
     * @param string $sTable
     * @param array $aValues
     * @return mixed (integer | boolean) Returns the last Insert ID on success or FALSE on failure.
     */
    public function insert($sTable, array $aValues)
    {
        $aValues = is_null($aValues) ? $this->_aValues : $aValues;
        $this->_sSql = 'INSERT INTO' . Db::prefix($sTable) . 'SET ';

        $oCachingIterator = new \CachingIterator(new \ArrayIterator($aValues));

        try
        {
            $oDb = Db::getInstance();

            // We start the transaction.
            $oDb->beginTransaction();

            foreach ($oCachingIterator as $sField => $sValue)
            {
                $this->_sSql .= $sField . ' = :' . $sField;
                $this->_sSql .= $oCachingIterator->hasNext() ? ',' : '';
            }

            $rStmt = $oDb->prepare($this->_sSql);

            foreach ($aValues as $sField => $sValue)
                $rStmt->bindParam(':' . $sField, $sValue);

            $rStmt->execute($aValues);

            // If all goes well, we commit the transaction.
            $oDb->commit();

            Db::free($rStmt);
            return $oDb->lastInsertId();
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();

            // We cancel the transaction if an error occurs.
            $oDb->rollBack();
            return false;
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
     * @return mixed (integer | boolean) Returns the number of rows on success or FALSE on failure.
     */
    public function update($sTable, $sField, $sValue, $sPk = null, $sId = null)
    {
        try
        {
            $oDb = Db::getInstance();

            // We start the transaction.
            $oDb->beginTransaction();

            $bIsWhere = isset($sPk, $sId);

            $this->_sSql = 'UPDATE' . Db::prefix($sTable) . "SET $sField = :value";

            if ($bIsWhere)
                $this->_sSql .= " WHERE $sPk = :id";

            $rStmt = $oDb->prepare($this->_sSql);
            $rStmt->bindParam(':value', $sValue);
            if ($bIsWhere) $rStmt->bindParam(':id', $sId);
            $rStmt->execute();
            $iRow = $rStmt->rowCount();

            // If all goes well, we commit the transaction.
            $oDb->commit();

            Db::free($rStmt);
            return $iRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();

            // We cancel the transaction if an error occurs.
            $oDb->rollBack();
            return false;
        }
    }

    /**
     * SQL Query.
     *
     * @param string $sSql
     * @return mixed (object | boolean) Returns the object on success or FALSE on failure.
     *
     */
    public function query($sSql)
    {
        try
        {
            $oDb = Db::getInstance();

            // We start the transaction.
            $oDb->beginTransaction();

            $rStmt = $oDb->prepare($sSql);
            $rStmt->execute();
            $oRow = $rStmt->fetchAll(\PDO::FETCH_OBJ);

            // If all goes well, we commit the transaction.
            $oDb->commit();

            Db::free($rStmt);
            return $oRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();

            // We cancel the transaction if an error occurs.
            $oDb->rollBack();
            return false;
        }
    }

    /**
     * Execute a Record query.
     *
     * @return mixed (object | boolean) Returns a PDOStatement object, or FALSE on failure.
     */
    public function execute()
    {
        return $this->query($this->_sSql);
    }

    /**
     * Clean out the query variables.
     * You can do this to build another query.
     *
     * @return void
     */
    public function clean()
    {
        // Set to default values
        $this->_sSql = '';
        $this->_aValues = array();
    }

    /**
     * Escape.
     *
     * @param string $sValue
     * @return string
     */
    public function escape($sValue)
    {
        return Db::getInstance()->quote($sValue);
    }

    /**
     * Select "All In One" in a SQL's query.
     *
     * @param mixed (array | string) $mTable
     * @param string $sField Default: NULL
     * @param string $sId Default: NULL
     * @param mixed (array | string) $mWhat Default: '*'
     * @param array $aJoin Default: NULL
     * @param string $sOptions Default: NULL
     * @return mixed (object | boolean) Object on success or throw PDOException on failure.
     */
    public function getAllInOne($mTable, $sField = null, $sId = null, $mWhat = '*', array $aJoin = null, $sOptions = null)
    {
        try
        {
            if (is_array($mTable))
            {
                $sTable = '';
                foreach ($mTable as $sTable)
                    $sTable .= Db::prefix($sTable, true) . ',';

                $sTable = rtrim($sTable, ',');
            }
            else
            {
                $sTable = Db::prefix($mTable, true);
            }

            if (is_array($mWhat))
                $sWhat = (count($mWhat)) ? implode(',', $mWhat) : '*';
            else
                $sWhat = $mWhat;

            $bIsWhere = isset($sField, $sId);

            $this->_sSql = "SELECT $sWhat FROM " . $sTable;

            if (!empty($aJoin) && count($aJoin) == 2)
                $this->_sSql .= " LEFT JOIN $aJoin[0] ON $sTable.$aJoin[1] = $aJoin[0].$aJoin[1]";

            if ($bIsWhere)
                $this->_sSql .= " WHERE $sField = :id";

            if (!empty($sOptions))
                $this->_sSql .= " $sOptions";

            $rStmt = Db::getInstance()->prepare($this->_sSql);
            if ($bIsWhere) $rStmt->bindParam(':id', $sId);
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
     * Select query and return one value result.
     *
     * @param string $sTable
     * @param string $sField Default: NULL
     * @param string $sId Default: NULL
     * @param string $sWhat Default: '*'
     * @param string $sOptions Default: NULL
     * @return mixed (string | object | boolean) SQL query on success (returns strong or object value) or throw PDOException on failure (returns a false boolean).
     *
     */
    public function getOne($sTable, $sField = null, $sId = null, $sWhat = '*', $sOptions = null)
    {
        try
        {
            $bIsWhere = isset($sField, $sId);

            $this->_sSql = 'SELECT ' . $sWhat . ' FROM' . Db::prefix($sTable);

            if ($bIsWhere)
                $this->_sSql .= "WHERE $sField = :id ";

            if (!empty($sOptions))
                $this->_sSql .= " $sOptions ";

            $this->_sSql .= 'LIMIT 0,1'; // Get only one column

            $rStmt = Db::getInstance()->prepare($this->_sSql);
            if ($bIsWhere) $rStmt->bindParam(':id', $sId);
            $rStmt->execute();
            $mRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            return $mRow;
        }
        catch (Exception $oE)
        {
            $this->_aErrors[] = $oE->getMessage();
        }
    }

    /**
     * Update statement.
     * Sample: Record::getInstance()->updates('MyTable', array('foo' => 'bar', 'foo2' => 'bar4', 'foo9' => 'bar4'))->find('fooID', 22)->execute();
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
            $this->_sSql .= $sField . ' = ' . $this->escape($sValue);
            $this->_sSql .= $oCachingIterator->hasNext() ? ',' : '';
        }

        return $this;
    }

    /**
     * Select statement.
     * Sample: Record::getInstance()->select('MyTable', 'column1, column2')->where('column1', 'me')->andClause('column2', 'rack', '<>')->orderBy('column1', Db::DESC)->execute();
     *
     * @param string $sTable
     * @param string $sWhat Default: '*'
     * @return object this
     *
     */
    public function select($sTable, $sWhat = '*')
    {
        $this->_sSql = 'SELECT ' . $sWhat . ' FROM' . Db::prefix($sTable);
        return $this;
    }

    /**
     * Find in SQL column(s) (with where clause).
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Record::where()
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     *
     */
    public function find($sField, $sValue)
    {
        $this->where($sField, $sValue, '=');
        return $this;
    }

    /**
     * AND for Find.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Record::andClause()
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     *
     */
    public function andFind($sField, $sValue)
    {
        $this->andClause($sField, $sValue, '=');
        return $this;
    }

    /**
     * OR for Find.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Record::orClause()
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     *
     */
    public function orFind($sField, $sValue)
    {
        $this->orClause($sField, $sValue, '=');
        return $this;
    }

    /**
     * HAVING for find.
     *
     * @see \PH7\Framework\Mvc\Model\Engine\Record::having()
     *
     * @param string $sField
     * @param string $sValue
     * @return object this
     */
    public function havingFind($sField, $sValue)
    {
        $this->having($sField, $sValue, '=');
        return $this;
    }

    /**
     * Add a WHERE clause.
     *
     * @param string $sField
     * @param string $sValue
     * @param string $sOperator Default: '='
     * @return object this
     *
     */
    public function where($sField, $sValue, $sOperator = '=')
    {
        $this->optClause('WHERE', $sField, $sValue, $sOperator);
        return $this;
    }

    /**
     * Add an AND clause.
     *
     * @param string $sField
     * @param string $sValue
     * @param string $sOperator Default: '='
     * @return object this
     */
    public function andClause($sField, $sValue, $sOperator = '=')
    {
        $this->optClause('AND', $sField, $sValue, $sOperator);
        return $this;
    }

    /**
     * Add an OR clause.
     *
     * @param string $sField
     * @param string $sValue
     * @param string $sOperator Default: '='
     * @return object this
     */
    public function orClause($sField, $sValue, $sOperator = '=')
    {
        $this->optClause('OR', $sField, $sValue, $sOperator);
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
        $this->clause('LIMIT', "$iOffset, $iLimit");
        return $this;
    }

    /**
     * Add an ORDER BY clause.
     *
     * @param string $sField
     * @param string $sOrder Default: 'ASC'
     * @return object this
     */
    public function orderBy($sField, $sOrder = Db::ASC)
    {
        $this->clause('ORDER BY', "$sField $sOrder");
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
        $this->clause('GROUP BY', $sGroup);
        return $this;
    }

    /**
     * Add an HAVING clause.
     *
     * @param string $sField
     * @param string $sValue
     * @param string $sOperator Default: '='
     * @return object this
     */
    public function having($sField, $sValue, $sOperator = '=')
    {
        $this->optClause('HAVING', $sField, $sValue, $sOperator);
        return $this;
    }

    /**
     * Add any clauses.
     *
     * @param string $sClsName Clause operator.
     * @param string $sVal Value.
     * @return object this
     */
    protected function clause($sClsName, $sVal)
    {
        $this->_sSql .= " $sClsName $sVal";
        return $this;
    }

    /**
     * Add a clause with operator and escape the input value.
     *
     * @param string $sClsName Clause operator.
     * @param string $sField Field
     * @param string $sVal Value.
     * @param string $sOpt Operator.
     * @return object this
     */
    protected function optClause($sClsName, $sField, $sVal, $sOpt)
    {
        $sVal = $this->escape($sVal);
        $this->_sSql .= " $sClsName $sField $sOpt $sVal";
        return $this;
    }

}
