<?php

namespace PH7\Cli\Installer\Misc;

use PDO;
use PH7\Cli\Installer\Exception\SQLInvalidVersion;

class Database extends PDO
{
    public const DBMS_MYSQL_NAME = 'MySQL';
    public const DBMS_POSTGRESQL_NAME = 'PostgreSQL';
    public const DSN_MYSQL_PREFIX = 'mysql';
    public const DSN_POSTGRESQL_PREFIX = 'pgsql';
    public const CHARSET = 'utf8mb4';
    public const PORT = '3306';

    private const REQUIRED_VERSION = '5.5.3';

    public function __construct(array $params)
    {
        $driverOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . self::CHARSET;

        if (!$this->checkVersion()) {
            throw new SQLInvalidVersion('Invalid DB version');
        }

        parent::__construct(
            "{$params['db_type']}:host={$params['db_hostname']};dbname={$params['db_name']};",
            $params['db_username'],
            $params['db_password'],
            $driverOptions
        );

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function checkVersion(): bool
    {
        $this->getAttribute(PDO::ATTR_DRIVER_NAME) === self::DSN_MYSQL_PREFIX &&
        version_compare($this->getAttribute(PDO::ATTR_SERVER_VERSION), self::REQUIRED_VERSION, '>=');
    }
}
