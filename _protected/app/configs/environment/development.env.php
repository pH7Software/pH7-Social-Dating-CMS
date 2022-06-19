<?php
/**
 * @title          Development Environment File
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @link           http://ph7builder.com
 * @copyright      (c) 2012-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

define('PH7_ENV_ENABLED', 'On');

// If php.ini is inadequate for us, let's fix it
error_reporting(E_ALL); // Since PHP 5.4 E_STRICT became part of E_ALL
ini_set('display_errors', PH7_ENV_ENABLED);
ini_set('display_startup_errors', PH7_ENV_ENABLED);
ini_set('track_errors', PH7_ENV_ENABLED);
ini_set('html_errors', PH7_ENV_ENABLED);
ini_set('docref_root', 'http://php.net/manual/');
ini_set('memory_limit', '356M');
