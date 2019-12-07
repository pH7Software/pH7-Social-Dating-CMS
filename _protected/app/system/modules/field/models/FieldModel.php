<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Model
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http as HttpRequest;

class FieldModel extends Model
{
    const MAX_VARCHAR_LENGTH = 191; // 191 max for MySQL's utf8mb4
    const MAX_INT_LENGTH = 11;
    const DEF_INT_LENGTH = 9;
    const FIELD_TEXTBOX_TYPE = 'textbox';
    const FIELD_NUMBER_TYPE = 'number';
    const PROFILE_ID_COLUMN = 'profileId';

    /** @var string */
    private $sTable;

    /** @var null|string */
    private $sName;

    /** @var null|string */
    private $sType;

    /** @var null|int */
    private $iLength;

    /** @var null|string */
    private $sDefVal;

    /** @var string */
    private $sSql;

    /**
     * @param string|null $sTable Table name.
     * @param string|null $sName Field name.
     * @param string|null $sType Field type.
     * @param int|null $iLength Length field.
     * @param string|null $sDefVal Default field value.
     */
    public function __construct($sTable, $sName = null, $sType = null, $iLength = null, $sDefVal = null)
    {
        parent::__construct();

        $this->sTable = Various::checkModelTable($sTable);
        $this->sName = $sName;
        $this->sType = $sType;
        $this->iLength = (int)$iLength;
        $this->sDefVal = $sDefVal;
    }

    /**
     * Get all fields.
     *
     * @return array All fields.
     */
    public function all()
    {
        $rStmt = Db::getInstance()->query('SELECT * FROM' . Db::prefix($this->sTable) . 'LIMIT 1');

        $aColumn = [];
        if ($rStmt->rowCount() > 0) {
            while ($aRow = $rStmt->fetch()) {
                foreach ($aRow as $sColumn => $sValue) {
                    if ($this->isColumnEligible($sColumn)) {
                        $aColumn[] = $sColumn;
                    }
                }
            }
        }

        return $aColumn;
    }

    /**
     * @return bool
     */
    public function insert()
    {
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'ADD ' . $this->sName . $this->getSqlType() . $this->getSqlDefault() . ';';
        return $this->execute();
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sOldFieldName = (new HttpRequest)->get('name');
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'CHANGE ' . $sOldFieldName . ' ' . $this->sName . $this->getSqlType() . $this->getSqlDefault() . ';';

        return $this->execute();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'DROP ' . $this->sName;
        return $this->execute();
    }

    /**
     * Count the number of fields.
     *
     * @return int
     */
    public function total()
    {
        return count($this->all());
    }

    /**
     * Executes SQL queries.
     *
     * @return bool|array Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
     *
     * @throws PH7InvalidArgumentException Explanatory message.
     */
    private function execute()
    {
        $oDb = Db::getInstance();
        $rStmt = $oDb->exec($this->sSql);

        return $rStmt === false ? $oDb->errorInfo() : true;
    }

    /**
     * @return string
     *
     * @throws PH7InvalidArgumentException
     */
    private function getSqlType()
    {
        $sSql = ' ';

        switch ($this->sType) {
            case self::FIELD_TEXTBOX_TYPE:
                {
                    if (mb_strlen($this->sDefVal) > $this->iLength) {
                        $this->iLength = mb_strlen($this->sDefVal);
                    }

                    if ($this->iLength === 0 || $this->iLength > self::MAX_VARCHAR_LENGTH) {
                        $this->iLength = self::MAX_VARCHAR_LENGTH;
                    }

                    $sSql .= 'VARCHAR(' . $this->iLength . ')';
                }
                break;

            case self::FIELD_NUMBER_TYPE:
                {
                    if (!is_numeric($this->sDefVal)) {
                        $this->sDefVal = 0;
                    }

                    if (strlen($this->sDefVal) > $this->iLength) {
                        $this->iLength = strlen($this->sDefVal);
                    }

                    if ($this->iLength === 0 || $this->iLength > self::MAX_INT_LENGTH) {
                        $this->iLength = self::DEF_INT_LENGTH; // Set the default INT() length value
                    }

                    $sSql .= 'INT(' . $this->iLength . ')';
                }
                break;

            default:
                throw new PH7InvalidArgumentException('Invalid Field type!');
        }

        return $sSql;
    }

    /**
     * @return string
     */
    private function getSqlDefault()
    {
        $sSql = ' DEFAULT ';
        $sSql .= isset($this->sDefVal) ? Db::getInstance()->quote($this->sDefVal) : 'NULL';

        return $sSql;
    }

    /**
     * @param string $sColumn
     *
     * @return bool
     */
    private function isColumnEligible($sColumn)
    {
        return !is_numeric($sColumn) && $sColumn !== self::PROFILE_ID_COLUMN;
    }
}
