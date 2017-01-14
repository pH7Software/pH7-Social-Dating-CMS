<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit
 */

use PH7\Framework\Loader\Autoloader as FrameworkLoader;

define('PH7', 1);

define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');
if (!ini_get('date.timezone')) {
    date_default_timezone_set(PH7_DEFAULT_TIMEZONE);
}

define('PH7_ENCODING', 'utf-8');
define('PH7_PATH_PROTECTED', dirname(dirname(__DIR__)) . '/_protected/');
define('PH7_PATH_FRAMEWORK', PH7_PATH_PROTECTED . 'framework/');

require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';

FrameworkLoader::getInstance()->init();
