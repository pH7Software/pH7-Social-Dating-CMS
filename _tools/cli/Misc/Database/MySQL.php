<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace PH7\Cli\Misc\Database;

use PDO;
use PH7\Cli\Exception\SQLInvalidVersion;

class MySQL extends PDO
{
    public const DBMS_MYSQL_NAME = 'MySQL';
    public const DBMS_POSTGRESQL_NAME = 'PostgreSQL';
    public const DSN_MYSQL_PREFIX = 'mysql';
    public const DSN_POSTGRESQL_PREFIX = 'pgsql';
    public const CHARSET = 'utf8mb4';

    private const REQUIRED_VERSION = '5.5.3';

    public function __construct(array $params)
    {
        $driverOptions = [
            self::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $params['db_charset']
        ];

        parent::__construct(
            "{$params['db_type']}:host={$params['db_hostname']};dbname={$params['db_name']};",
            $params['db_username'],
            $params['db_password'],
            $driverOptions
        );

        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);

        if (!$this->checkVersion()) {
            throw new SQLInvalidVersion('Invalid DB version');
        }
    }

    private function checkVersion(): bool
    {
        $this->getAttribute(self::ATTR_DRIVER_NAME) === self::DSN_MYSQL_PREFIX &&
        version_compare($this->getAttribute(self::ATTR_SERVER_VERSION), self::REQUIRED_VERSION, '>=');
    }
}
