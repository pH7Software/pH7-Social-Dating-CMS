<?php
/**
 * @title            Db Connect File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Inc
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

$aSessionDatabase = $_SESSION['db'] ?? [];
$bHasSessionCredentials = is_array($aSessionDatabase) && isset(
    $aSessionDatabase['type'],
    $aSessionDatabase['hostname'],
    $aSessionDatabase['name'],
    $aSessionDatabase['username'],
    $aSessionDatabase['password'],
    $aSessionDatabase['port'],
    $aSessionDatabase['charset']
);

if ($bHasSessionCredentials) {
    $aParams = array(
        'db_type' => $aSessionDatabase['type'],
        'db_hostname' => $aSessionDatabase['hostname'],
        'db_name' => $aSessionDatabase['name'],
        'db_username' => $aSessionDatabase['username'],
        'db_password' => $aSessionDatabase['password'],
        'db_port' => (int)$aSessionDatabase['port'],
        'db_charset' => $aSessionDatabase['charset']
    );
} else {
    $aStoredConfig = defined('PH7_PATH_APP_CONFIG')
        ? parse_ini_file(PH7_PATH_APP_CONFIG . 'config.ini', true, INI_SCANNER_TYPED)
        : false;
    $aStoredDatabase = is_array($aStoredConfig) && isset($aStoredConfig['database']) &&
        is_array($aStoredConfig['database'])
        ? $aStoredConfig['database']
        : [];
    if (!isset(
        $aStoredDatabase['type'],
        $aStoredDatabase['hostname'],
        $aStoredDatabase['name'],
        $aStoredDatabase['username'],
        $aStoredDatabase['password'],
        $aStoredDatabase['port'],
        $aStoredDatabase['charset']
    )) {
        throw new \RuntimeException('The saved database configuration is incomplete. Restart the installation.');
    }

    $aParams = array(
        'db_type' => $aStoredDatabase['type'],
        'db_hostname' => $aStoredDatabase['hostname'],
        'db_name' => $aStoredDatabase['name'],
        'db_username' => $aStoredDatabase['username'],
        'db_password' => $aStoredDatabase['password'],
        'db_port' => (int)$aStoredDatabase['port'],
        'db_charset' => $aStoredDatabase['charset']
    );
}

$DB = new Database($aParams);
