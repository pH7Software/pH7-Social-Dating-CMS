<?php
/**
 * @title            Index File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install
 * @version          1.3
 */

define('PH7', 1);

ob_start();

header('Content-Type: text/html; charset=utf-8');

require 'constants.php';

include PH7_ROOT_INSTALL . 'inc/log.inc.php';

require 'requirements.php';

include_once PH7_ROOT_INSTALL . 'inc/fns/misc.php';
require_once PH7_ROOT_INSTALL . 'library/Smarty/Smarty.class.php';
require_once PH7_ROOT_INSTALL . 'inc/loader.inc.php';

/*** We define the URL if overwrite mode is enabled (to enable it. Htaccess must be present in the current directory) ***/
$sSlugUrlInstall = (!is_url_rewrite()) ? '?a=' : '';
define('PH7_URL_SLUG_INSTALL', PH7_URL_INSTALL . $sSlugUrlInstall);

require PH7_ROOT_INSTALL . 'inc/init.inc.php';

ob_end_flush();
