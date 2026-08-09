<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link             https://ph7builder.com
 * @package          PH7 / Install
 */

define('PH7', 1);

ob_start();

header_remove('X-Powered-By');
header('Content-Type: text/html; charset=utf-8');

require 'constants.php';

include PH7_ROOT_INSTALL . 'inc/log.inc.php';

require 'requirements.php';

$sAutoloadPath = PH7_ROOT_INSTALL . 'vendor/autoload.php';
if (!is_file($sAutoloadPath) || !is_readable($sAutoloadPath)) {
    http_response_code(500);
    error_log('Installer dependencies are missing: run Composer install before opening _install.');
    display_html_header('Installer dependencies missing');
    echo '<h3 class="error">Installer dependencies are missing.</h3>',
        '<p>Run <code>composer install --no-dev --optimize-autoloader</code> from the pH7Builder root directory, then reload this page.</p>';
    display_html_footer();
    exit(1);
}

require_once $sAutoloadPath;

require PH7_ROOT_INSTALL . 'inc/init.inc.php';

ob_end_flush();
