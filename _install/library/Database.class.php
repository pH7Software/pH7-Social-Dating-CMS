<?php
/**
 * @title            Helper PDO Database Class
 *
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Library
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PDO;

class Database extends PDO
{
    const DBMS_MYSQL_NAME = 'MySQL';

    const DSN_MYSQL_PREFIX = 'mysql';

    public function __construct(array $aParams)
    {
        parent::__construct(
            $this->buildDsn($aParams),
            $aParams['db_username'],
            $aParams['db_password'],
            []
        );

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Checks if the DBMS is MySQL.
     */
    private function isMySQL(string $sDbType): bool
    {
        return $sDbType === Database::DSN_MYSQL_PREFIX;
    }

    private function buildDsn(array $aParams): string
    {
        $sDsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s',
            $aParams['db_type'],
            $aParams['db_hostname'],
            $aParams['db_port'],
            $aParams['db_name']
        );

        if ($this->isMySQL($aParams['db_type'])) {
            $sDsn .= ';charset=' . $aParams['db_charset'];
        }

        return $sDsn;
    }
}
