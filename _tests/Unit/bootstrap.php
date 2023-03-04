<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit
 */

use PH7\App\Includes\Classes\Loader\Autoloader as AppLoader;
use PH7\Framework\Loader\Autoloader as FrameworkLoader;
use PH7\Framework\Str\Str;
use PH7\Framework\Translate\Lang;

define('PH7', 1);

// Set default HTTP_ACCEPT_LANGUAGE SERVER var
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-GB,en;q=0.9';

// Timezone constant
define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');

// Charset constant
define('PH7_ENCODING', 'utf-8');

// URL/Path constants
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);
define('PH7_SH', '/'); // SlasH
define('PH7_PAGE_EXT', '.html');
define('PH7_URL_PROT', 'http://');
define('PH7_DOMAIN', 'localhost:8888');
define('PH7_URL_ROOT', PH7_URL_PROT . 'localhost:8888/');
define('PH7_URL_STATIC', '');
define('PH7_RELATIVE', '');

// General Kernel constants
define('PH7_PATH_PROTECTED', dirname(dirname(__DIR__)) . '/_protected/');
define('PH7_PATH_FRAMEWORK', PH7_PATH_PROTECTED . 'framework/');
define('PH7_PATH_APP', PH7_PATH_PROTECTED . 'app/');
define('PH7_PATH_SYS', PH7_PATH_APP . 'system/');
define('PH7_PATH_SYS_MOD', PH7_PATH_SYS . 'modules/');
define('PH7_PATH_APP_LANG', PH7_PATH_APP . 'langs/');
define('PH7_PATH_TEST', __DIR__ . PH7_DS);

// Config constants
define('PH7_CONFIG', 'config/');
define('PH7_PATH_APP_CONFIG', PH7_PATH_APP . 'configs/');
define('PH7_CONFIG_FILE', 'config.ini');

// Modules constants
define('PH7_MOD', 'modules/');
define('PH7_MODELS', 'models/');
define('PH7_VIEWS', 'views/');
define('PH7_FORMS', 'forms/');

// Templates & Assets constants
define('PH7_LAYOUT', 'templates/');
define('PH7_TPL', 'themes/');
define('PH7_TPL_NAME', 'base');
define('PH7_TPL_MOD_NAME', 'base');
define('PH7_TPL_MAIL_NAME', 'base');
define('PH7_CSS', 'css/');
define('PH7_JS', 'js/');
define('PH7_IMG', 'img/');

// App constants
define('PH7_SYS', 'system/');

// Admin constant
define('PH7_ADMIN_MOD', 'admin123');

// Lang constant
define('PH7_DEFAULT_LANG_CODE', 'en');
define('PH7_LANG_CODE', 'en');

// Cache constant
define('PH7_PATH_CACHE', PH7_PATH_PROTECTED . 'data/cache/');

// Max Values constants
define('PH7_MAX_URL_LENGTH', 120);


include PH7_PATH_TEST . 'requirements_check.inc.php';

// Fix if timezone isn't correctly set
if (!ini_get('date.timezone')) {
    date_default_timezone_set(PH7_DEFAULT_TIMEZONE);
}

// Loading Framework Classes
require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';
FrameworkLoader::getInstance()->init();

// Loading classes from ~/protected/app/includes/classes/*
require PH7_PATH_APP . 'includes/classes/Loader/Autoloader.php';
AppLoader::getInstance()->init();

if (!function_exists('escape')) {
    new Str; // Load class to get escape() function
}

if (!function_exists('t')) {
    include PH7_PATH_APP_LANG . 'en_US/language.php';
    // Load class to include t() function
    new Lang;
}
