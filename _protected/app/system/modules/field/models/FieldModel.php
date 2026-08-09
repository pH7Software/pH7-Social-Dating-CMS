<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class FieldModel extends Model
{
    public const MAX_VARCHAR_LENGTH = 191; // 191 max for MySQL's utf8mb4

    private const MAX_INT_LENGTH = 11;
    private const DEF_INT_LENGTH = 9;
    private const FIELD_TEXTBOX_TYPE = 'textbox';
    private const FIELD_NUMBER_TYPE = 'number';
    private const PROFILE_ID_COLUMN = 'profileId';
    private const COLUMN_NAME_PATTERN = '/^[a-zA-Z0-9_]{2,30}$/D';

    private string $sTable;

    private ?string $sName;

    private ?string $sType;

    private ?int $iLength;

    private ?string $sDefVal;

    private string $sSql;

    /**
     * @param string|null $sTable  table name
     * @param mixed       $mName   field name
     * @param string|null $sType   field type
     * @param int|null    $iLength length field
     * @param string|null $sDefVal default field value
     */
    public function __construct(
        string $sTable,
        mixed $mName = null,
        ?string $sType = null,
        ?int $iLength = null,
        ?string $sDefVal = null
    ) {
        if ($mName !== null && !self::isValidColumnName($mName)) {
            throw new PH7InvalidArgumentException('Invalid field name!');
        }

        parent::__construct();

        $this->sTable = Various::checkModelTable($sTable);
        $this->sName = $mName;
        $this->sType = $sType;
        $this->iLength = (int)$iLength;
        $this->sDefVal = $sDefVal;
    }

    /**
     * Get all fields.
     *
     * @return array all fields
     */
    public function all(): array
    {
        $rStmt = Db::getInstance()->query('SELECT * FROM' . Db::prefix($this->sTable) . 'LIMIT 1');

        $aColumn = [];
        if ($rStmt->rowCount() > 0) {
            while ($aRow = $rStmt->fetch()) {
                foreach ($aRow as $mColumn => $sValue) {
                    if ($this->isColumnEligible($mColumn)) {
                        $aColumn[] = $mColumn;
                    }
                }
            }
        }

        return $aColumn;
    }

    public function insert(): bool
    {
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'ADD ' .
            self::quoteColumnName($this->sName) . $this->getSqlType() . $this->getSqlDefault() . ';';

        return $this->execute();
    }

    public function update(): bool
    {
        $mOldFieldName = $_GET['name'] ?? null;
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'CHANGE ' .
            self::quoteColumnName($mOldFieldName) . ' ' . self::quoteColumnName($this->sName) .
            $this->getSqlType() . $this->getSqlDefault() . ';';

        return $this->execute();
    }

    public function delete(): bool
    {
        $this->sSql = 'ALTER TABLE' . Db::prefix($this->sTable) . 'DROP ' .
            self::quoteColumnName($this->sName);

        return $this->execute();
    }

    /**
     * Count the number of fields.
     */
    public function total(): int
    {
        return count($this->all());
    }

    public static function isValidColumnName(mixed $mColumnName): bool
    {
        return is_string($mColumnName) && preg_match(self::COLUMN_NAME_PATTERN, $mColumnName) === 1;
    }

    /**
     * Executes SQL queries.
     *
     * @throws PH7InvalidArgumentException explanatory message
     *
     * @return bool returns TRUE if there are no errors, otherwise returns an ARRAY of error information
     */
    private function execute(): bool
    {
        $oDb = Db::getInstance();
        $rStmt = $oDb->exec($this->sSql);

        return $rStmt !== false;
    }

    /**
     * @throws PH7InvalidArgumentException
     */
    private function getSqlType(): string
    {
        $sSql = ' ';

        switch ($this->sType) {
            case self::FIELD_TEXTBOX_TYPE:

                $iDefaultValueLength = mb_strlen($this->sDefVal ?? '');
                if ($iDefaultValueLength > $this->iLength) {
                    $this->iLength = $iDefaultValueLength;
                }

                if ($this->iLength === 0 || $this->iLength > self::MAX_VARCHAR_LENGTH) {
                    $this->iLength = self::MAX_VARCHAR_LENGTH;
                }

                $sSql .= 'VARCHAR(' . $this->iLength . ')';

                break;
            case self::FIELD_NUMBER_TYPE:

                if (!is_numeric($this->sDefVal)) {
                    $this->sDefVal = '0';
                }

                if (strlen($this->sDefVal) > $this->iLength) {
                    $this->iLength = strlen($this->sDefVal);
                }

                if ($this->iLength === 0 || $this->iLength > self::MAX_INT_LENGTH) {
                    $this->iLength = self::DEF_INT_LENGTH; // Set the default INT() length value
                }

                $sSql .= 'INT(' . $this->iLength . ')';

                break;
            default:
                throw new PH7InvalidArgumentException('Invalid Field type!');
        }

        return $sSql;
    }

    private function getSqlDefault(): string
    {
        $sSql = ' DEFAULT ';
        $sSql .= $this->sDefVal !== null ? Db::getInstance()->quote($this->sDefVal) : 'NULL';

        return $sSql;
    }

    private static function quoteColumnName(mixed $mColumnName): string
    {
        if (!self::isValidColumnName($mColumnName)) {
            throw new PH7InvalidArgumentException('Invalid field name!');
        }

        return '`' . $mColumnName . '`';
    }

    /**
     * @param string|int $mColumn
     */
    private function isColumnEligible($mColumn): bool
    {
        return !is_numeric($mColumn) && $mColumn !== self::PROFILE_ID_COLUMN;
    }
}
