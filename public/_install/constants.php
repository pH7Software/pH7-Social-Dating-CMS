<?php
/**
 * @title            Constants File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install
 * @version          1.0
 */

defined('PH7') or exit('Restricted access');

//------------ Other ----------------//
define('PH7_REQUIRE_VERSION', '5.4.0');
define('PH7_ENCODING', 'utf-8');
define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);

// URL association for SSL and protocol compatibility
$sHttp = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
// Determines the domain name with the port
$sDomain = ($_SERVER['SERVER_PORT'] != '80') ?  $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_NAME'];

//------------ URL ----------------//
define('PH7_PROT', $sHttp);
define('PH7_URL_INSTALL', dirname(PH7_PROT . $sDomain . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES)) . '/'); // INSTALL URL
define('PH7_URL_ROOT', dirname(PH7_URL_INSTALL) . '/'); // ROOT URL

//----------- PATH -----------------//
define('PH7_ROOT_PUBLIC', dirname(__DIR__) . PH7_DS); // PUBLIC ROOT
define('PH7_ROOT_INSTALL', __DIR__ . PH7_DS); // ROOT INSTALL'
define('PH7_PATH_PUBLIC_DATA_SYS_MOD', PH7_ROOT_PUBLIC . 'data/system/modules/');

