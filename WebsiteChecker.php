<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://ph7cms.com
 * @package        PH7 / ROOT
 */

namespace PH7;

defined('PH7') or exit(header('Location: ./'));

use RuntimeException;

class WebsiteChecker
{
    const REQUIRED_SERVER_VERSION = '5.6.0';
    const REQUIRED_CONFIG_FILE_NAME = '_constants.php';
    const INSTALL_FOLDER_NAME = '_install/';

    const PHP_VERSION_ERROR_MESSAGE = 'ERROR: Your current PHP version is %s. pH7CMS requires PHP %s or newer.<br /> Please ask your Web host to upgrade PHP to %s or newer.';
    const NO_CONFIG_FOUND_ERROR_MESSAGE = 'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.';

    /**
     * @throws RuntimeException
     */
    public function checkPhpVersion()
    {
        if ($this->isIncompatiblePhpVersion()) {
            throw new RuntimeException(
                sprintf(
                    self::PHP_VERSION_ERROR_MESSAGE,
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
    public function clearBrowserCache()
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
    public function doesConfigFileExist()
    {
        return is_file(__DIR__ . '/' . self::REQUIRED_CONFIG_FILE_NAME);
    }

    /**
     * @return string
     */
    public function getNoConfigFoundMessage()
    {
        return self::NO_CONFIG_FOUND_ERROR_MESSAGE;
    }

    /**
     * @return bool
     */
    public function doesInstallFolderExist()
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
