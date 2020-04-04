<?php
/**
 * @title          Development Environment File
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @link           http://ph7cms.com
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

// If php.ini is inadequate, we fix it.
error_reporting(E_ALL); // Since PHP 5.4 E_STRICT became part of E_ALL
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');
ini_set('track_errors', 'On');
ini_set('html_errors', 'On');
ini_set('docref_root', 'http://php.net/manual/');
ini_set('memory_limit', '356M');
