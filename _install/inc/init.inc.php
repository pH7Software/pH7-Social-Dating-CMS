<?php
/**
 * @title            Init Controller File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Inc
 * @version          1.2
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use Throwable;

define('WEBSITE_ALREADY_INSTALLED_MESSAGE', 'Your site is already installed.<br /> If you wish to do a new clean installation, please delete your "_constants.php" file and delete all the contents of your database (e.g. MySQL).');

/* Build installer routes from the rewrite marker set by the active .htaccess or web-server configuration. */
define('PH7_URL_SLUG_INSTALL', PH7_URL_INSTALL . (!is_url_rewrite() ? '?a=' : ''));

try {
    $sController = $_GET['c'] ?? 'install';
    $sAction = $_GET['a'] ?? 'index';
    $bValidRoute = is_string($sController) && is_string($sAction) &&
        preg_match('/^[a-z][a-z0-9_]*$/i', $sController) === 1 &&
        preg_match('/^[a-z][a-z0-9_]*$/i', $sAction) === 1;
    $sCtrlName = $bValidRoute ? ucfirst($sController) . 'Controller' : 'MainController';
    $sCtrlClass = 'PH7\\' . $sCtrlName;
    $sMainCtrlClass = MainController::class;

    if (is_software_installed($sCtrlName, $sAction)) {
        exit(WEBSITE_ALREADY_INSTALLED_MESSAGE);
    }

    if (
        $bValidRoute &&
        is_file(PH7_ROOT_INSTALL . 'controllers/' . $sCtrlName . '.php') &&
        class_exists($sCtrlClass)
    ) {
        $oCtrl = new $sCtrlClass;

        if ($oCtrl instanceof InstallController && $sAction !== 'index' && !$oCtrl->hasInstallationAccess()) {
            redirect(PH7_URL_SLUG_INSTALL . 'index');
        } elseif (method_exists($oCtrl, $sAction)) {
            $oCtrl->$sAction();
        } else {
            (new $sMainCtrlClass)->error_404();
        }
    } else {
        (new $sMainCtrlClass)->error_404();
    }
} catch (Throwable $oE) {
    error_log((string)$oE);
    http_response_code(500);
    if (ob_get_level() > 0) {
        ob_clean();
    }
    display_html_header('Installation error');
    echo '<h3 class="error">The installer could not complete this request.</h3>',
        '<p>Review <code>_install/data/logs/php_error.log</code>, correct the reported server or database issue, and try again.</p>';
    display_html_footer();
}
