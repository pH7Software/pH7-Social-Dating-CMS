<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit
 */

use PH7\Framework\Loader\Autoloader as FrameworkLoader;

define('PH7', 1);

define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');
if (!ini_get('date.timezone')) {
    date_default_timezone_set(PH7_DEFAULT_TIMEZONE);
}

// Charset Constant
define('PH7_ENCODING', 'utf-8');

// General Kernel Constants
define('PH7_PATH_PROTECTED', dirname(dirname(__DIR__)) . '/_protected/');
define('PH7_PATH_FRAMEWORK', PH7_PATH_PROTECTED . 'framework/');
define('PH7_PATH_APP', PH7_PATH_PROTECTED . 'app/');
define('PH7_PATH_SYS', PH7_PATH_APP . 'system/');
define('PH7_PATH_SYS_MOD', PH7_PATH_SYS . 'modules/');
define('PH7_PATH_TEST', __DIR__ . '/');

// Config Constants
define('PH7_CONFIG', '');
define('PH7_PATH_APP_CONFIG', PH7_PATH_TEST . 'fixtures/config/');
define('PH7_CONFIG_FILE', 'test.ini');

// Max Values Constants
define('PH7_MAX_URL_LENGTH', 120);

require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';

FrameworkLoader::getInstance()->init();
