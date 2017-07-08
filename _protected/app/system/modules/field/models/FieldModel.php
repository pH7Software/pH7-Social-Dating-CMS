<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http;

class FieldModel extends Framework\Mvc\Model\Engine\Model
{

    private $_sTable, $_sName, $_sType, $_iLength, $_sDefVal, $_sSql;

    /**
     * Constructor.
     *
     * @param string $sTable Table name.
     * @param string $sName Fielde name. Default NULL
     * @param string $sType Field type. Default NULL
     * @param integer $iLength Length field. Default NULL
     * @param string $sDefVal Default field value. Default NULL
     */
    public function __construct($sTable, $sName = null, $sType = null, $iLength = null, $sDefVal = null)
    {
        $this->_sTable = Various::checkModelTable($sTable);
        $this->_sName = $sName;
        $this->_sType = $sType;
        $this->_iLength = (int) $iLength;
        $this->_sDefVal = $sDefVal;
    }

    /**
     * Get all fields.
     *
     * @return object Data of users
     * @return array All fields.
     */
    public function all()
    {
        $rStmt = Db::getInstance()->query('SELECT * FROM' . Db::prefix($this->_sTable) . 'LIMIT 1');

        $iNum = (int) $rStmt->rowCount();
        $aColumn = array();

        if ($iNum > 0)
        {
            while ($aRow = $rStmt->fetch())
            {
                foreach ($aRow as $sColumn => $sValue)
                {
                    if (!is_numeric($sColumn) && $sColumn !== 'profileId')
                        $aColumn[] = $sColumn;
                }
            }
        }

        return $aColumn;
    }

    public function insert()
    {
        $this->_sSql = 'ALTER TABLE' . Db::prefix($this->_sTable) . 'ADD ' . $this->_sName . ' ' . $this->getType();
        return $this->execute();
    }

    public function update()
    {
        $this->_sSql = 'ALTER TABLE' . Db::prefix($this->_sTable) . 'CHANGE ' . (new Http)->get('name') . ' ' . $this->_sName . ' ' . $this->getType();
        return $this->execute();
    }

    public function delete()
    {
        $this->_sSql = 'ALTER TABLE' . Db::prefix($this->_sTable) . 'DROP ' . $this->_sName;
        return $this->execute();
    }

    /**
     * Count fields.
     *
     * @return integer
     */
    public function total()
    {
        return (int) count($this->all());
    }

    /**
     * Executes SQL queries.
     *
     * @return mixed (boolean | array) Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException Explanatory message.
     */
    protected function execute()
    {
        $rStmt = Db::getInstance()->exec($this->_sSql);
        return ($rStmt === false) ? $rStmt->errorInfo() : true;
    }

    protected function getType()
    {
        switch ($this->_sType)
        {
            case 'textbox':
                if (mb_strlen($this->_sDefVal) > $this->_iLength) $this->_iLength = mb_strlen($this->_sDefVal);
                if ($this->_iLength == 0 || $this->_iLength > 255) $this->_iLength = 255;
                $this->_sSql .= 'VARCHAR(' . $this->_iLength . ')';
            break;

            case 'number':
                if (!is_numeric($this->_sDefVal)) $this->_sDefVal = 0;
                if (strlen($this->_sDefVal) > $this->_iLength) $this->_iLength = strlen($this->_sDefVal);
                if ($this->_iLength == 0 || $this->_iLength > 11) $this->_iLength = 9; // Set the default maximum length value.
                $this->_sSql .= 'INT(' . $this->_iLength . ')';
            break;

            default:
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Invalid Field type!');
        }

        return $this->_sSql . ' NOT NULL DEFAULT ' . Db::getInstance()->quote($this->_sDefVal) . ';';
    }

}
