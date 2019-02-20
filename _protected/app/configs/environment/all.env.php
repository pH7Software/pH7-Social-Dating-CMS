<?php
/**
 * @title          All Environment File
 * @desc           File for all environments.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @link           http://ph7cms.com
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

ini_set('log_errors', 'On');
ini_set('error_log', PH7_PATH_LOG . 'php_error.log');
ini_set('ignore_repeated_errors', 'On'); // Do not log repeated errors that occur in same file on same line

// For Security Session
// ini_set('session.auto_start', 0); // The default configuration of PHP (php.ini) should already have this setting disabled
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.hash_function', 1);
ini_set('session.hash_bits_per_character', 6);
