<?php
/**
 * @desc           Index file for the public root.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2011-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link           https://ph7builder.com
 * @package        PH7 / ROOT
 */

namespace PH7;

define('PH7', 1);

use RuntimeException;

require __DIR__ . '/WebsiteChecker.php';

$oSiteChecker = new WebsiteChecker();

try {
    $oSiteChecker->checkPhpVersion();
    $bConfigFileExists = $oSiteChecker->doesConfigFileExist();
    if (!$bConfigFileExists || !$oSiteChecker->doesConfigPinCanonicalAuthority()) {
        $oSiteChecker->normalizeRequestAuthority();
    }
    if (!$bConfigFileExists) {
        if ($oSiteChecker->doesInstallFolderExist()) {
            $oSiteChecker->clearBrowserCache();
            $oSiteChecker->moveToInstaller();
        } else {
            echo $oSiteChecker->getNoConfigFoundMessage();
        }
        exit;
    }
} catch (RuntimeException $oExcept) {
    echo $oExcept->getMessage();
    exit;
}

require __DIR__ . '/_constants.php';

if (!defined('PH7_PATH_APP') || !is_file(PH7_PATH_APP . 'Bootstrap.php')) {
    $sConfiguredPath = defined('PH7_PATH_APP') ? PH7_PATH_APP : '(undefined)';
    echo 'Configuration error: invalid "_protected" path. ';
    echo 'Please update PH7_PATH_PROTECTED in "_constants.php" to the real location of your protected folder. ';
    echo 'Current PH7_PATH_APP: ' . htmlspecialchars((string)$sConfiguredPath, ENT_QUOTES);
    exit;
}

require PH7_PATH_APP . 'configs/constants.php';
require PH7_PATH_APP . 'includes/helpers/misc.php';
require PH7_PATH_APP . 'Bootstrap.php';

$oApp = Bootstrap::getInstance();
$oApp->setTimezoneIfNotSet();

ob_start();
$oApp->run();
ob_end_flush();
