<?php
/**
 * @title            Constants File
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://ph7cms.com
 * @package          PH7 / Install
 */

defined('PH7') or exit('Restricted access');

//---------------------------- Variables --------------------------------//

//------------ URL ----------------//
// Check the SSL protocol compatibility
$sHttp = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://';
// Determine the domain name with the port
$sDomain = ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') ? $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];

// Determine the current file of the application
$sPhp_self = str_replace('\\', '', dirname(htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES))); // Remove backslashes for Windows compatibility


//---------------------------- Constants --------------------------------//

//------------ Other ----------------//
define('PH7_ADMIN_MOD', 'admin123');
define('PH7_REQUIRE_SERVER_VERSION', '5.6.0');
define('PH7_REQUIRE_SQL_VERSION', '5.0');
define('PH7_ENCODING', 'utf-8');
define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);

//------------ URL ----------------//
define('PH7_URL_INSTALL', $sHttp . $sDomain . $sPhp_self . '/'); // INSTALL URL
define('PH7_URL_ROOT', dirname(PH7_URL_INSTALL) . '/'); // ROOT URL

//----------- PATH -----------------//
define('PH7_ROOT_PUBLIC', dirname(__DIR__) . PH7_DS); // PUBLIC ROOT
define('PH7_ROOT_INSTALL', __DIR__ . PH7_DS); // ROOT INSTALL'
define('PH7_PATH_PUBLIC_DATA_SYS_MOD', PH7_ROOT_PUBLIC . 'data/system/modules/');
