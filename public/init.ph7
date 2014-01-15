<?php
/**
 * @title          Index
 * @desc           Index file for public root.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://software.hizup.com
 * @package        PH7 / ROOT / Core
 * @version        1.0
 */

namespace PH7;

define('PH7', 1);

//define('PH7_REQUIRE_SERVER_VERSION', '5.5.0'); // For pH7CMS 1.1
define('PH7_REQUIRE_SERVER_VERSION', '5.4.0');
define('PH7_REQUIRE_SQL_VERSION', '5.0');

if (version_compare(PHP_VERSION, PH7_REQUIRE_SERVER_VERSION, '<'))
    exit('ERROR: Your PHP version is ' . PHP_VERSION . '. pH7CMS requires PHP ' . PH7_REQUIRE_SERVER_VERSION . ' or newer.');

// If no system settings, go install
if (!is_file(__DIR__ . '/_constants.php'))
    exit((is_dir(__DIR__ . '/_install/')) ? header('Location: _install/') : 'CONFIG FILE NOT FOUND!');

require __DIR__ . '/_constants.php';
require PH7_PATH_APP  . 'Bootstrap.php';
