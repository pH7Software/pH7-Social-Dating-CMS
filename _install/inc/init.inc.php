<?php
/**
 * @title            Init Controller File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc
 * @version          1.2
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use Exception;

/* We define the URL if mod_rewrite is enabled (to enable it, sample.htaccess has to be renamed to .htaccess) */
define('PH7_URL_SLUG_INSTALL', PH7_URL_INSTALL . (!is_url_rewrite() ? '?a=' : ''));

$sCtrlName = ucfirst((!empty($_GET['c']) ? $_GET['c'] : 'install')) . 'Controller';
$sCtrlClass = 'PH7\\' . $sCtrlName;
$sMainCtrlClass = MainController::class;
$sAction = (!empty($_GET['a'])) ? $_GET['a'] : 'index';

if (is_file(PH7_ROOT_PUBLIC . '_constants.php') && $sCtrlName == 'InstallController' && $sAction == 'index') {
    exit('Your site is already installed.<br /> If you want to redo a clean install, please delete your "_constants.php" file and delete all the content of your database.');
}

try {
    if (
        is_file(PH7_ROOT_INSTALL . 'controllers/' . $sCtrlName . '.php') &&
        class_exists($sCtrlClass)
    ) {
        $oCtrl = new $sCtrlClass;

        if (method_exists($oCtrl, $sAction))
            call_user_func(array($oCtrl, $sAction));
        else
            (new $sMainCtrlClass)->error_404();
    } else
        (new $sMainCtrlClass)->error_404();
} catch (Exception $oE) {
    echo $oE->getMessage();
}
