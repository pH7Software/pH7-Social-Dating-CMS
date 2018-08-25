<?php
/**
 * @title          Index
 * @desc           Index file for public root.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://ph7cms.com
 * @package        PH7 / ROOT
 */

namespace PH7;

define('PH7', 1);

use RuntimeException;

require __DIR__ . '/WebsiteChecker.php';

$oSiteChecker = new WebsiteChecker();

try {
    $oSiteChecker->checkPhpVersion();
    if (!$oSiteChecker->doesConfigFileExist()) {
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
require PH7_PATH_APP . 'Bootstrap.php';

$oApp = Bootstrap::getInstance();
$oApp->setTimezoneIfNotSet();

ob_start();
$oApp->run();
ob_end_flush();
