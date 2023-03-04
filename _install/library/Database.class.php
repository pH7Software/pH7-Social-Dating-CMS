<?php
/**
 * @title            Helper PDO Database Class
 *
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
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
    const DBMS_POSTGRESQL_NAME = 'PostgreSQL';

    const DSN_MYSQL_PREFIX = 'mysql';
    const DSN_POSTGRESQL_PREFIX = 'pgsql';

    public function __construct(array $aParams)
    {
        $aDriverOptions = [];

        if ($this->isMySQL($aParams['db_type'])) {
            $aDriverOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $aParams['db_charset'];
        }

        parent::__construct(
            "{$aParams['db_type']}:host={$aParams['db_hostname']};dbname={$aParams['db_name']};",
            $aParams['db_username'],
            $aParams['db_password'],
            $aDriverOptions
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
}
