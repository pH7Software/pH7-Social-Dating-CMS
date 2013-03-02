<?php
/**
 * @title          Development Environment File
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @link           http://software.hizup.com
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

// If php.ini is inadequate, it corrects on the fly
error_reporting(E_ALL | E_STRICT); // E_STRICT for PHP5 and higher
ini_set('display_errors ' , 'On');
ini_set('display_startup_errors', 'On');
ini_set('track_errors', 'On');
ini_set('html_errors', 'On');
ini_set('docref_root', 'http://php.net/manual/');
ini_set('memory_limit', '356M');
