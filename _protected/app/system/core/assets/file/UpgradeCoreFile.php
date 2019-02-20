<?php
/**
 * @title            Upgrade Class
 * @desc             Allows you to make updated the software (SQL, files, ...).
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / File
 * @version          1.8
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\File as F;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\Version;

@set_time_limit(0);
@ini_set('memory_limit', '528M');

class UpgradeCore
{
    /**
     * Remote update URL.
     */
    const REMOTE_URL = 'http://update.ph7cms.com/';
    const ARCHIVE_EXT = '.zip';
    const MIN_SQL_FILE_SIZE = 12; // Size in bytes

    /**
     * Internal update folders.
     *
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const DIR = 'upgrade';
    const FILE_DIR = 'file';
    const DATA_DIR = 'data';
    const SQL_DIR = 'sql';
    const INFO_DIR = 'info';

    const INST_INTRO_FILE = 'introduction';
    const INST_CONCL_FILE = 'conclusion';
    const UPGRADE_FILE = 'upgrade.sql';
    const VERSION_LIST_FILE = 'all_versions.txt';
    const VERSION_FILE = 'Version.class.php';

    // Use UNIX wildcard to be able to select only the sub-directories present in "public/"
    const PUBLIC_DIR = 'public/*';
    // Use UNIX wildcard to be able to select only the sub-directories present in "protected/"
    const PROTECTED_DIR = 'protected/*';


    /** @var Http */
    private $oHttpRequest;

    /** @var F\File */
    private $oFile;

    /** @var Config */
    private $oConfig;

    /** @var string */
    private $sHtml;

    private $sUpgradesDirUpgradeFolder;

    /** @var string */
    private $sVerName;

    /** @var string */
    private $sVerNumber;

    /** @var int */
    private $iVerBuild;

    /** @var bool */
    private $bAutoRemoveUpgradeDir = false;

    /** @var bool */
    private $bUpgradePatchAvailable = false;

    /** @var array */
    private $aErrors = [];

    public function __construct()
    {
        $this->oHttpRequest = new Http;
        $this->oFile = new F\File;
        $this->oConfig = Config::getInstance();

        $this->sHtml = ''; // Default HTML contents
        $this->sVerNumber = '...'; // Default value of the version number upgrade

        $this->prepare(); // Preparation and verification for software upgrade
    }

    /**
     * Output the HTML layout of the upgrade wizard.
     *
     * @return void
     */
    public function display()
    {
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>', t('Upgrade %software_name% | Version %0%', $this->sVerNumber), '</title><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><meta name="robots" content="noindex"><style>body{background:#EFEFEF;color:#555;font:normal 10pt Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.bold{font-weight:bold;font-size:13px}.red,.error{color:red}.success{color:green}.italic{font-style:italic}.underline{text-decoration:underline}pre{margin:2px;font-style:italic}code{font-style:italic;font-size:11px}</style></head><body><div class="center">';
        echo $this->sHtml;
        echo '<p class="italic">' . t('Psst! Do not forget to read the <a href="%0%">documentation page</a> before proceeding with the update.', Version::UPGRADE_DOC_URL) . '</p>';
        echo '</div></body></html>';
    }

    /**
     * @return array Returns all version numbers.
     */
    public function getVersions()
    {
        return (array)file(static::REMOTE_URL . static::VERSION_LIST_FILE);
    }

    /**
     * Checks and returns the correct needed version for the current pH7CMS installation.
     *
     * @return string|bool The version needed number for the current pH7CMS installation.
     */
    public function getNextVersion()
    {
        $aVersions = $this->getVersions();

        if ($iKey = array_search(Kernel::SOFTWARE_VERSION, $aVersions, true)) {
            return $aVersions[$iKey + 1];
        }

        // If no next version is found, just returns the current one.
        return Kernel::SOFTWARE_VERSION;
    }

    private function prepare()
    {
        if (!AdminCore::auth()) {
            // Checking if the administrator is logged in
            $this->aErrors[] = t('You must be logged in as administrator to upgrade your site.');
        }

        if (!$this->hasErrors()) {
            // Download the next upgrade patch to "~/_repository/" folder
            $this->download($this->getNextVersion());

            $aAvailableUpgrades = $this->getAvailableUpgrades();
            if (empty($aAvailableUpgrades)) {
                $this->sHtml .= '<h2>' . t('No upgrade patches available for %software_name%.') . '</h2>';
            } else {
                $this->sHtml .= '<h2>' . t('Upgrade available for %software_name%:') . '</h2>';
                $this->sHtml .= '<form method="post">';

                rsort($aAvailableUpgrades, SORT_NUMERIC);
                foreach ($aAvailableUpgrades as $sFolder) {
                    $this->sUpgradesDirUpgradeFolder = $this->oFile->checkExtDir($sFolder);

                    $this->readConfig();

                    $sVerName = $this->oConfig->values['upgrade.version']['name'];
                    $sVerNumber = $this->oConfig->values['upgrade.version']['number'];
                    $iVerBuild = $this->oConfig->values['upgrade.version']['build'];
                    $sDesc = $this->oConfig->values['upgrade.information']['description'];

                    if ($this->isValidUpgradeFolder($this->sUpgradesDirUpgradeFolder)) {
                        $bIsValidVer = $this->isValidVersion($sVerName, $sVerNumber, $iVerBuild);
                        if ($bIsValidVer) {
                            $this->bUpgradePatchAvailable = true;

                            $this->sHtml .= '<p class="underline italic ' . ($bIsValidVer ? 'bold' : '') . '">' . t('Version Name: %0%, Version Number: %1%, Version Build: %2%', $sVerName, $sVerNumber, $iVerBuild) . '</p>';

                            $sMsg = t('Upgrade <span class="bold italic">%software_version_name% %software_version% Build %software_build%</span> to version <span class="bold italic">%0%</span>', '<span class="bold italic">' . $sVerName . ' ' . $sVerNumber . ' Build ' . $iVerBuild . '</span>');
                            $this->sHtml .= '<button type="submit" class="success" name="submit_upgrade" value="' . $this->sUpgradesDirUpgradeFolder . '" onclick="return confirm(\'' . t('Have you made a backup of your website files, folders and database?') . '\');">' . $sMsg . '</button>';

                            $this->sHtml .= '<p class="underline">' . t('Description of the upgrade patch:') . '</p>';
                            $this->sHtml .= $sDesc;

                            $this->sHtml .= '<p class="bold underline">' . t('Instruction:') . '</p>';
                            $this->sHtml .= $this->readInstruction(static::INST_INTRO_FILE);

                            $this->sHtml .= '<br /><hr /><br />';
                        }
                    } else {
                        $sMsg = t('Upgrade patch is not valid!');
                        $this->sHtml .= '<button type="submit" class="error" disabled="disabled">' . $sMsg . '</button>';
                    }
                }

                $this->sHtml .= '</form>';

                if (!$this->bUpgradePatchAvailable) {
                    $this->sHtml .= '<h3>' . t('No upgrade patches available for your current version: %software_version%, build %software_build%') . '</h3>';
                }

                if ($this->isUpgradeRequested() && $this->isValidUpgradeFolder($this->oHttpRequest->post('submit_upgrade'))) {
                    $this->sUpgradesDirUpgradeFolder = $this->oHttpRequest->post('submit_upgrade');

                    $this->readConfig();

                    $this->sVerName = $this->oConfig->values['upgrade.version']['name']; // Version name upgrade
                    $this->sVerNumber = $this->oConfig->values['upgrade.version']['number']; // Version number upgrade
                    $this->iVerBuild = $this->oConfig->values['upgrade.version']['build']; // Version build upgrade

                    DbConfig::setSiteMode(DbConfig::MAINTENANCE_SITE);
                    $this->oConfig->setDevelopmentMode();
                    usleep(100000);

                    $this->check();

                    if (!$this->hasErrors()) {
                        $this->run(); // Run the upgrade patch!

                        if (!$this->hasErrors()) {
                            /**
                             * It resets the HTML variable ($this->sHtml) to not display versions upgrade available.
                             * The user can refresh the page to réaficher the upgrade available.
                             */
                            $this->sHtml = '<h3 class="success">' . t('Your update ran successfully!') . '</h3>';

                            if ($this->bAutoRemoveUpgradeDir) {
                                if ($this->removeUpgradeDir()) {
                                    $this->sHtml .= '<p class="success">' . t('The upgrade directory <em>(~%0%)</em> has been deleted!', PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder) . '</p>';
                                    $this->sHtml .= '<p class="success">' . t('Status... OK!') . '</p>';
                                } else {
                                    $this->sHtml .= '<p class="error">' . t('The upgrade directory <em>(~%0%)</em> could not be deleted, please delete it manually using an FTP client or SSH.', PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder) . '</p>';
                                    $this->sHtml .= '<p class="error">' . t('Status... Failure!') . '</p>';
                                }
                            } else {
                                $this->sHtml .= '<p>' . t('Please delete the upgrade folder using an FTP client or SSH.') . '</p>';
                            }

                            $this->sHtml .= '<p class="bold underline">' . t('Conclusion Message:') . '</p>';
                            $this->sHtml .= $this->readInstruction(static::INST_CONCL_FILE);
                        } else {
                            $this->addErrorMessagesToLayout();
                        }
                    } else {
                        $this->addErrorMessagesToLayout();
                    }

                    $this->oConfig->setProductionMode();
                    DbConfig::setSiteMode(DbConfig::ENABLED_SITE);
                    usleep(100000);
                }
            }
        } else {
            $this->addErrorMessagesToLayout();
        }
    }

    private function run()
    {
        $this->file();
        $this->sql();
        $this->setNewVersionToKernel();
        $this->clearAllCacheFolders();
    }

    private function file()
    {
        $this->copyPublicPathToRoot();
        $this->copyProtectedPathToRoot();
    }

    private function copyPublicPathToRoot()
    {
        $sPathPublicDir = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder . static::DATA_DIR . PH7_DS . static::FILE_DIR . PH7_DS . static::PUBLIC_DIR;
        if (is_dir($this->oFile->removeWildcards($sPathPublicDir))) {
            $this->oFile->systemCopy($sPathPublicDir, PH7_PATH_ROOT);
            $this->oFile->chmod(PH7_PATH_ROOT, Chmod::MODE_ALL_EXEC);
        }
    }

    private function copyProtectedPathToRoot()
    {
        $sPathProtectedDir = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder . static::DATA_DIR . PH7_DS . static::FILE_DIR . PH7_DS . static::PROTECTED_DIR;
        if (is_dir($this->oFile->removeWildcards($sPathProtectedDir))) {
            $this->oFile->systemCopy($sPathProtectedDir, PH7_PATH_PROTECTED);
            $this->oFile->chmod(PH7_PATH_PROTECTED, Chmod::MODE_ALL_EXEC);
        }
    }

    private function sql()
    {
        $sFullPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder . static::DATA_DIR . PH7_DS . static::SQL_DIR . PH7_DS . $this->oConfig->values['database']['type_name'] . PH7_DS . static::UPGRADE_FILE;

        if (is_file($sFullPath) && filesize($sFullPath) > static::MIN_SQL_FILE_SIZE) {
            $mQuery = (new UpgradeCoreModel)->run($sFullPath);

            if ($mQuery !== true) {
                $this->aErrors[] = t('Unable to execute the upgrade of the database SQL.<br />Error Message: %0%', '<pre>' . print_r($mQuery) . '</pre>');
            }
        }
    }

    private function check()
    {
        if (!AdminCore::auth()) {
            // Recheck if the administrator is still logged in
            $this->aErrors[] = t('You must be logged in as administrator to upgrade your site.');
        }

        if (DbConfig::getSetting('siteStatus') !== DbConfig::MAINTENANCE_SITE) {
            $this->aErrors[] = t('Your site must be in maintenance mode to begin the upgrade.');
        }

        if (!isDebug()) {
            $this->aErrors[] = t('You must put your site in development mode in order to launch the upgrade of your site!') . '<br />' .
                t('1) Please change the permission of the ~%0% file for writing for all groups (0666 in octal).', PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE) . '<br />' .
                t('2) Edit ~%0% file and find the code:', PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE) . '<br />' .
                '"<code>environment = production ; production or development</code>"<br />' .
                t('and replace it with the code:') . '<br />' .
                '"<code>environment = development ; production or development</code>"<br />' .
                t('3) After installation, please edit ~%0% file and find the code:', PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE) . '<br />' .
                '"<code>environment = development ; production or development</code>"<br />' .
                t('and replace it with the code:') . '<br />' .
                '"<code>environment = production ; production or development</code>"<br />' .
                t('4) Change the permission of the file to write only for users and reading for the other groups (0644 in octal).');
        }
    }

    /**
     * Download the new version patches from pH7CMS remote server to the client server.
     * Then, extract the file to "_repository/upgrade/" directory to set it as available for the next update.
     * Then, remove zip archive file, as we don't need it anymore.
     *
     * @param string $sNewVersion Version number (e.g. "1.3.6")
     */
    private function download($sNewVersion)
    {
        $sZipFileName = $sNewVersion . self::ARCHIVE_EXT;
        $sDestinationPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS;

        $rFile = $this->oFile->getUrlContents(self::REMOTE_URL . $sZipFileName);
        $this->oFile->putFile(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName, $rFile);
        $this->oFile->zipExtract(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName, $sDestinationPath);
        $this->oFile->deleteFile(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName);
    }

    /**
     * Check if error is found.
     *
     * @return bool
     */
    private function hasErrors()
    {
        return !empty($this->aErrors);
    }

    /**
     * Assign the error messages to the current HTML code.
     *
     * @return void
     */
    private function addErrorMessagesToLayout()
    {
        $iErrors = count($this->aErrors);

        $this->sHtml .= '<h3 class="error underline italic">' . t('You have %0% error(s):', $iErrors) . '</h3>';

        for ($iKey = 0; $iKey < $iErrors; $iKey++) {
            $this->sHtml .= '<p class="error">' . t('%0%) %1%', $iKey + 1, $this->aErrors[$iKey]) . '</p>';
        }
    }

    /**
     * Checks if the file upgrade is valid.
     *
     * @param string $sFolder The folder.
     *
     * @return bool Returns TRUE if it is correct, FALSE otherwise.
     */
    private function isValidUpgradeFolder($sFolder)
    {
        $sFullPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder;

        return !preg_match('#^' . Version::VERSION_PATTERN . '\-' . Version::VERSION_PATTERN . '#', $sFolder) || !is_file($sFullPath . static::INFO_DIR . PH7_DS . PH7_CONFIG_FILE) ? false : true;
    }

    /**
     * Read the upgrade folders.
     *
     * @return array Returns the upgrade folders.
     */
    private function readUpgrades()
    {
        return $this->oFile->readDirs(PH7_PATH_REPOSITORY . static::DIR . PH7_DS);
    }

    /**
     * Remove the upgrade folder.
     *
     * @return bool Returns TRUE If the folder has been deleted, FALSE otherwise.
     */
    private function removeUpgradeDir()
    {
        return $this->oFile->deleteDir(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder);
    }

    /**
     * @return array
     */
    private function getAvailableUpgrades()
    {
        $aFolders = [];

        foreach ($this->readUpgrades() as $sFolder) {
            $aFolders[$sFolder] = $sFolder;
        }

        return $aFolders;
    }

    /**
     * Set new version to the Version, kernel constants.
     *
     * @return void
     */
    private function setNewVersionToKernel()
    {
        $sVersionPathFile = PH7_PATH_FRAMEWORK . 'Security/' . self::VERSION_FILE;
        $sContents = $this->oFile->getFile($sVersionPathFile);

        if ($this->sVerName != Version::KERNEL_VERSION_NAME) {
            $sContents = str_replace('KERNEL_VERSION_NAME = \'' . Version::KERNEL_VERSION_NAME . '\'', 'KERNEL_VERSION_NAME = \'' . $this->sVerName . '\'', $sContents);
        }

        if ($this->sVerNumber != Version::KERNEL_VERSION) {
            $sContents = str_replace('KERNEL_VERSION = \'' . Version::KERNEL_VERSION . '\'', 'KERNEL_VERSION = \'' . $this->sVerNumber . '\'', $sContents);
        }

        if ($this->iVerBuild != Version::KERNEL_BUILD) {
            $sContents = str_replace('KERNEL_BUILD = \'' . Version::KERNEL_BUILD . '\'', 'KERNEL_BUILD = \'' . $this->iVerBuild . '\'', $sContents);
        }

        $this->oFile->putFile($sVersionPathFile, $sContents);
    }

    /**
     * Check if the version name, number and build they are correct.
     *
     * @param string $sName Name of the version. e.g., pOH
     * @param string $sNumber Number of the version. e.g., 2.1.4
     * @param int $iBuild Number of the version build. e.g., 1
     *
     * @return bool Returns TRUE if the version name is correct, FALSE otherwise.
     */
    private function isValidVersion($sName, $sNumber, $iBuild)
    {
        if (!is_string($sName) || !preg_match('#^' . Version::VERSION_PATTERN . '$#', $sNumber)) {
            return false;
        }

        if (version_compare($sNumber, Kernel::SOFTWARE_VERSION, '<')) {
            return false;
        }

        if (version_compare($sNumber, Kernel::SOFTWARE_VERSION, '==')) {
            return version_compare($iBuild, Kernel::SOFTWARE_BUILD, '>');
        } else {
            if (version_compare($sNumber, Kernel::SOFTWARE_VERSION, '>')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the version upgrade in the config.ini file.
     *
     * @return bool
     */
    private function readConfig()
    {
        return $this->oConfig->load(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder . static::INFO_DIR . PH7_DS . PH7_CONFIG_FILE);
    }

    /**
     * Get the instructions.
     *
     * @param string $sInstFile Instruction file.
     *
     * @return string|bool Returns "false" if the file does not exist or if it fails, otherwise returns the "file contents"
     */
    private function readInstruction($sInstFile)
    {
        try {
            return F\Import::file(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sUpgradesDirUpgradeFolder . static::INFO_DIR . PH7_DS . $sInstFile);
        } catch (F\IOException $oExcept) {
            return '<p class="error">' . t('Instruction file not found!') . '</p>';
        }
    }

    /**
     * Clear all cache folders.
     *
     * Quite often, the changes from newer versions can break the website if older data are still saved in cache.
     */
    private function clearAllCacheFolders()
    {
        $aCacheFolders = [
            PH7_PATH_CACHE . Cache::CACHE_DIR,
            PH7_PATH_CACHE . PH7Tpl::COMPILE_DIR,
            PH7_PATH_CACHE . PH7Tpl::CACHE_DIR,
            PH7_PATH_CACHE . Gzip::CACHE_DIR
        ];

        foreach ($aCacheFolders as $sFolder) {
            $this->oFile->deleteDir($sFolder);
        }
    }

    /**
     * @return bool
     */
    private function isUpgradeRequested()
    {
        return $this->oHttpRequest->postExists('submit_upgrade');
    }
}

(new UpgradeCore)->display();
