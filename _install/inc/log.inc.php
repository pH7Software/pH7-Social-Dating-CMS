<?php
/**
 * @title            Log File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc
 */

defined('PH7') or exit('Restricted access');

ini_set('log_errors', 'On');
ini_set('error_log', PH7_ROOT_INSTALL . 'data/logs/php_error.log');
ini_set('ignore_repeated_errors', 'On');
