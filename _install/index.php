<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             https://ph7cms.com
 * @package          PH7 / Install
 */

define('PH7', 1);

ob_start();

header('Content-Type: text/html; charset=utf-8');

require 'constants.php';

include PH7_ROOT_INSTALL . 'inc/log.inc.php';

require 'requirements.php';

require_once PH7_ROOT_INSTALL . 'vendor/autoload.php';

require PH7_ROOT_INSTALL . 'inc/init.inc.php';

ob_end_flush();
