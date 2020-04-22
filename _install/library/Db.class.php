<?php
/**
 * @title            Helper PDO Database Class
 *
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PDO;

class Db extends PDO
{
    const DBMS_MYSQL_NAME = 'MySQL';
    const DBMS_POSTGRESQL_NAME = 'PostgreSQL';

    const DSN_MYSQL_PREFIX = 'mysql';
    const DSN_POSTGRESQL_PREFIX = 'pgsql';

    public function __construct(array $aParams)
    {
        $aDriverOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $aParams['db_charset'];
        parent::__construct(
            "{$aParams['db_type']}:host={$aParams['db_hostname']};dbname={$aParams['db_name']};",
            $aParams['db_username'],
            $aParams['db_password'],
            $aDriverOptions
        );

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
