<?php
/**
 * @title          Index
 * @desc           Index file for public root.
 *
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://ph7cms.com
 * @package        PH7 / ROOT / Core
 */

namespace PH7;

define('PH7', 1);

define('PH7_REQUIRE_SERVER_VERSION', '5.6.0');
define('PH7_REQUIRE_SQL_VERSION', '5.0');

if (version_compare(PHP_VERSION, PH7_REQUIRE_SERVER_VERSION, '<')) {
    exit('ERROR: Your current PHP version is ' . PHP_VERSION . '. pH7CMS requires PHP ' . PH7_REQUIRE_SERVER_VERSION . ' or newer.<br /> Please ask your Web host to upgrade PHP to ' . PH7_REQUIRE_SERVER_VERSION . ' or newer.');
}

// If no settings found, go to the installer
if (!is_file(__DIR__ . '/_constants.php')) {
    if (is_dir(__DIR__ . '/_install/')) {
        // Clear redirection cache since some folks get it cached
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Location: _install/');
    } else {
        echo 'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.';
    }
    exit;
}

require __DIR__ . '/_constants.php';
require PH7_PATH_APP . 'Bootstrap.php';

$oApp = Bootstrap::getInstance();
$oApp->setTimezoneIfNotSet();

ob_start();
$oApp->run();
ob_end_flush();
