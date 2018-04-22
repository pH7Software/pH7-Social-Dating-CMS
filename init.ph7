<?php
/**
 * @title          Index
 * @desc           Index file for public root.
 *
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://ph7cms.com
 * @package        PH7 / ROOT / Core
 */

namespace PH7;

define('PH7', 1);

use RuntimeException;

class Root
{
    const REQUIRED_SERVER_VERSION = '5.6.0';
    const REQUIRED_CONFIG_FILE_NAME = '_constants.php';
    const INSTALL_FOLDER_NAME = '_install/';

    /**
     * @throws RuntimeException
     */
    public function checkPhpVersion()
    {
        if ($this->isIncompatiblePhpVersion()) {
            $sMsg = 'ERROR: Your current PHP version is %s. pH7CMS requires PHP %s or newer.<br /> Please ask your Web host to upgrade PHP to %s or newer.';
            throw new RuntimeException(
                sprintf(
                    $sMsg,
                    PHP_VERSION,
                    self::REQUIRED_SERVER_VERSION,
                    self::REQUIRED_SERVER_VERSION
                )
            );
        }
    }

    /**
     * Clear redirection cache since some folks get it cached.
     *
     * @return void
     */
    public function clearHttpCache()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }

    public function moveToInstaller()
    {
        header('Location: ' . self::INSTALL_FOLDER_NAME);
    }

    /**
     * @return bool
     */
    public function isConfigFileExists()
    {
        return is_file(__DIR__ . '/' . self::REQUIRED_CONFIG_FILE_NAME);
    }

    /**
     * @return bool
     */
    public function isInstallFolderExists()
    {
        return is_dir(__DIR__ . '/' . self::INSTALL_FOLDER_NAME);
    }

    /**
     * @return bool
     */
    private function isIncompatiblePhpVersion()
    {
        return version_compare(PHP_VERSION, self::REQUIRED_SERVER_VERSION, '<');
    }
}

$oRoot = new Root();

try {
    $oRoot->checkPhpVersion();
    if (!$oRoot->isConfigFileExists()) {
        if ($oRoot->isInstallFolderExists()) {
            $oRoot->clearHttpCache();
            $oRoot->moveToInstaller();
        } else {
            echo 'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.';
        }
        exit;
    }
} catch(RuntimeException $oExcept) {
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
