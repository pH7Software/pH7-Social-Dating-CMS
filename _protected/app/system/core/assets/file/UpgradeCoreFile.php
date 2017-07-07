<?php
/**
 * @title            Upgrade Class
 * @desc             Allows you to make updated the software (SQL, files, ...).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / File
 * @version          1.3
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\File as F;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\Version;

@set_time_limit(0);
@ini_set('memory_limit', '528M');

class UpgradeCore extends Kernel
{
    /**
     * Remote update URL.
     */
    const REMOTE_URL = 'http://update.hizup.com/';

    /**
     * Internal update folders.
     *
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const DIR = 'upgrade';
    const FILE_DIR = 'file';
    const PUBLIC_DIR = 'public';
    const PROTECTED_DIR = '_protected';
    const DATA_DIR = 'data';
    const SQL_DIR = 'sql';
    const INFO_DIR = 'info';

    const UPGRADE_FILE = 'upgrade.sql';
    const INST_INTRO_FILE = 'introduction';
    const INST_CONCL_FILE = 'conclusion';


    /** @var Http */
    private $_oHttpRequest;

    /** @var F\File */
    private $_oFile;

    /** @var Config */
    private $_oConfig;

    /** @var string */
    private $_sHtml;

    private $_sUpgradesDirUpgradeFolder;

    /** @var string */
    private $_sVerName;

    /** @var string */
    private $_sVerNumber;

    /** @var int */
    private $_iVerBuild;

    /** @var bool */
    private $_bAutoRemoveUpgradeDir = false;

    /** @var array */
    private $_aErrors = array();


    public function __construct()
    {
        parent::__construct();

        $this->_oHttpRequest = new Http;
        $this->_oFile = new F\File;
        $this->_oConfig = Config::getInstance();

        $this->_sHtml = ''; // Default HTML contents
        $this->_sVerNumber = '...'; // Default value of the version number upgrade

        $this->_prepare(); // Preparation and verification for software upgrade
    }

    private function _prepare()
    {
        if (!AdminCore::auth()) {
            // Checking if the administrator is logged in
            $this->_aErrors[] = t('You must be logged in as administrator to upgrade your site.');
        }

        if (!$this->_displayIfErr()) {
            // Download the next upgrade patch to "~/_repository/" folder
            $this->_download($this->getNextVersion());

            // If not found error
            if (!$this->_showAvailableUpgrades()) {
                $this->_sHtml .= '<h2>' . t('No upgrade path for %software_name%!') . '</h2>';
            } else {
                $this->_sHtml .= '<h2>' . t('Upgrade available for %software_name%:') . '</h2>';

                $this->_sHtml .= '<form method="post">';

                foreach ($this->_showAvailableUpgrades() as $sFolder) {
                    $this->_sUpgradesDirUpgradeFolder = $this->_oFile->checkExtDir($sFolder);

                    $this->_readConfig();

                    $sVerName = $this->_oConfig->values['upgrade.version']['name'];
                    $sVerNumber = $this->_oConfig->values['upgrade.version']['number'];
                    $iVerBuild = $this->_oConfig->values['upgrade.version']['build'];
                    $sDesc = $this->_oConfig->values['upgrade.information']['description'];

                    if ($this->_checkUpgradeFolder($this->_sUpgradesDirUpgradeFolder)) {
                        $this->_sHtml .= '<p class="underline italic">' . t('Version Name: %0%, Version Number: %1%, Version Build: %2%', $sVerName, $sVerNumber, $iVerBuild) . '</p>';

                        if ($this->_checkVersion($sVerName, $sVerNumber, $iVerBuild)) {
                            $sMsg = t('Upgrade <span class="bold italic">%software_version_name% %software_version% Build %software_build%</span> to version <span class="bold italic">%0%</span>', '<span class="bold italic">' . $sVerName . ' ' . $sVerNumber . ' Build ' . $iVerBuild . '</span>');
                            $this->_sHtml .= '<button type="submit" class="success" name="submit_upgrade" value="' . $this->_sUpgradesDirUpgradeFolder . '" onclick="return confirm(\'' . t('Have you made a backup of your website files, folders and database?') . '\');">' . $sMsg . '</button>';

                            // Description upgrade path
                            $this->_sHtml .= '<p class="underline">' . t('Description of the upgrade patch:') . '</p>';
                            $this->_sHtml .= $sDesc;

                            // Introduction file
                            $this->_sHtml .= '<p class="bold underline">' . t('Introductory instruction:') . '</p>';
                            $this->_sHtml .= $this->_readInstruction(static::INST_INTRO_FILE);
                        } else {
                            $sMsg = t('Bad "version name, version number or version build" of upgrade path!');
                            $this->_sHtml .= '<button type="submit" class="error" disabled="disabled">' . $sMsg . '</button>';
                        }
                    } else {
                        $sMsg = t('Upgrade path is not valid!');
                        $this->_sHtml .= '<button type="submit" class="error" disabled="disabled">' . $sMsg . '</button>';
                    }

                    $this->_sHtml .= '<br /><hr /><br />';

                    unset($sVerName, $sVerNumber, $iVerBuild);
                }

                $this->_sHtml .= '</form>';

                if ($this->_oHttpRequest->postExists('submit_upgrade')) {
                    if ($this->_checkUpgradeFolder($this->_oHttpRequest->post('submit_upgrade'))) {
                        $this->_sUpgradesDirUpgradeFolder = $this->_oHttpRequest->post('submit_upgrade'); // Upgrade Directory Path

                        $this->_readConfig();

                        $this->_sVerName = $this->_oConfig->values['upgrade.version']['name']; // Version name upgrade
                        $this->_sVerNumber = $this->_oConfig->values['upgrade.version']['number']; // Version number upgrade
                        $this->_iVerBuild = $this->_oConfig->values['upgrade.version']['build']; // Version build upgrade

                        DbConfig::setSiteMode(DbConfig::MAINTENANCE_SITE);
                        $this->_oConfig->setDevelopmentMode();
                        usleep(100000);

                        $this->_check(); // Checking

                        // If not found error
                        if (!$this->_displayIfErr()) {
                            $this->_run(); // Run Upgrade!

                            // If no error
                            if (!$this->_displayIfErr()) {
                                /**
                                 * It resets the HTML variable ($this->_sHtml) to not display versions upgrade available.
                                 * The user can refresh the page to réaficher the upgrade available.
                                 */
                                $this->_sHtml = '<h3 class="success">' . t('Your update ran successfully!') . '</h3>';

                                if ($this->_bAutoRemoveUpgradeDir) {
                                    if ($this->_removeUpgradeDir()) {
                                        $this->_sHtml .= '<p class="success">' . t('The upgrade directory <em>(~%0%)</em> has been deleted!', PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder) . '</p>';
                                        $this->_sHtml .= '<p class="success">' . t('Status... OK!') . '</p>';
                                    } else {
                                        $this->_sHtml .= '<p class="error">' . t('The upgrade directory <em>(~%0%)</em> could not be deleted, please delete it manually using an FTP client or SSH.', PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder) . '</p>';
                                        $this->_sHtml .= '<p class="error">' . t('Status... Failure!') . '</p>';
                                    }
                                } else {
                                    $this->_sHtml .= '<p>' . t('Please delete the upgrade file using an FTP client or SSH.') . '</p>';
                                }

                                // Conclusion file
                                $this->_sHtml .= '<p class="bold underline">' . t('Conclusion of Instruction:') . '</p>';
                                $this->_sHtml .= $this->_readInstruction(static::INST_CONCL_FILE);
                            }
                        }

                        $this->_oConfig->setProductionMode();
                        DbConfig::setSiteMode(DbConfig::ENABLE_SITE);
                        usleep(100000);
                    }
                }
            }
        }
    }

    private function _run()
    {
        //$this->_file();
        $this->_sql();
    }

    private function _file()
    {
        $sPathPublicDir = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder . static::FILE_DIR . PH7_DS . static::PUBLIC_DIR . PH7_DS;
        if (is_dir($sPathPublicDir)) {
            $this->_oFile->systemRename($sPathPublicDir, PH7_PATH_ROOT);
            $this->_oFile->chmod(PH7_PATH_ROOT, 0777);
        }

        $sPathProtectedDir = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder . static::FILE_DIR . PH7_DS . static::PROTECTED_DIR . PH7_DS;
        if (is_dir($sPathProtectedDir)) {
            $this->_oFile->systemRename($sPathProtectedDir, PH7_PATH_PROTECTED);
            $this->_oFile->chmod(PH7_PATH_PROTECTED, 0777);
        }
    }

    private function _sql()
    {
       $sPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder . static::DATA_DIR . PH7_DS . static::SQL_DIR . PH7_DS . $this->_oConfig->values['database']['type_name'] . PH7_DS . static::UPGRADE_FILE . PH7_DS;

       if (is_file($sPath) && filesize($sPath) > 12) {
           $mQuery = (new UpgradeCoreModel)->run($sPath);

           if ($mQuery !== true) {
               $this->_aErrors[] = t('Unable to execute the upgrade of the database SQL.<br />Error Message: %0%', '<pre>' . print_r($mQuery) . '</pre>');
           }
       }
    }

    private function _check()
    {
        if (!AdminCore::auth()) {
            // Recheck if the administrator is still logged in
            $this->_aErrors[] = t('You must be logged in as administrator to upgrade your site.');
        }

        if (DbConfig::getSetting('siteStatus') !== DbConfig::MAINTENANCE_SITE) {
            $this->_aErrors[] = t('Your site must be in maintenance mode to begin the upgrade.');
        }

        if (!isDebug()) {
            $this->_aErrors[] = t('You must put your site in development mode in order to launch the upgrade of your site!') . '<br />' .
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
     * Download the new version path from HiZup remote server to the client server.
     * Then, extract the file to "_repository" folder to set it available for the next update.
     * Then, remove zip archive file as we don't need it anymore.
     *
     * @param string $sNewVersion Version number (e.g. "1.3.6")
     */
    private function _download($sNewVersion)
    {
        $sZipFileName = $sNewVersion . '.zip';
        $sDestinationPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS;

        $rFile = $this->_oFile->getUrlContents(self::REMOTE_URL . $sZipFileName);
        $this->_oFile->putFile(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName, $rFile);
        $this->_oFile->zipExtract(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName, $sDestinationPath);
        $this->_oFile->deleteFile(PH7_PATH_REPOSITORY . PH7_TMP . $sZipFileName);
    }

    /**
     * Check if error is found.
     *
     * @return bool
     */
    private function _isErr()
    {
        return !empty($this->_aErrors);
    }

    /**
     * Check and return HTML contents errors.
     *
     * @return bool TRUE if there are errors else FALSE
     */
    private function _displayIfErr()
    {
        if ($this->_isErr()) {
           $iErrors = count($this->_aErrors);

           $this->_sHtml .= '<h3 class="error underline italic">' . t('You have %0% error(s):', $iErrors) . '</h3>';

           for ($i=0; $i < $iErrors; $i++) {
               $this->_sHtml .= '<p class="error">' . t('%0%) %1%', $i + 1, $this->_aErrors[$i]) . '</p>';
           }

           return true;
        }

        return false;
    }

    /**
     * Checks if the file upgrade is valid.
     *
     * @param string $sFolder The folder.
     *
     * @return bool Returns TRUE if it is correct, FALSE otherwise.
     */
    private function _checkUpgradeFolder($sFolder)
    {
        $sFullPath = PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder;

        return !preg_match('#^' . Version::PATTERN . '\-' . Version::PATTERN . '#', $sFolder) || !is_file($sFullPath . static::INFO_DIR . PH7_DS . PH7_CONFIG_FILE) ? false : true;
    }

    /**
     * Read the upgrade folders.
     *
     * @return array Returns the upgrade folders.
     */
    private function _readUpgrades()
    {
        return $this->_oFile->readDirs(PH7_PATH_REPOSITORY . static::DIR . PH7_DS);
    }

    /**
     * Remove the upgrade folder.
     *
     * @return bool Returns TRUE If the folder has been deleted, FALSE otherwise.
     */
    private function _removeUpgradeDir()
    {
        return $this->_oFile->deleteDir(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder);
    }

    private function _showAvailableUpgrades()
    {
        $aFolders = array();

        foreach ($this->_readUpgrades() as $sFolder) {
            $aFolders[$sFolder] = $sFolder;
        }

        return $aFolders;
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
    private function _checkVersion($sName, $sNumber, $iBuild)
    {
        if (!is_string($sName) || !preg_match('#^' . Version::PATTERN . '$#', $sNumber)) {
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
    private function _readConfig()
    {
        return $this->_oConfig->load(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder . static::INFO_DIR . PH7_DS . PH7_CONFIG_FILE);
    }

    /**
     * Get the instructions.
     *
     * @param string $sInstFile Instruction file.
     *
     * @return string|bool Returns "false" if the file does not exist or if it fails, otherwise returns the "file contents"
     */
    private function _readInstruction($sInstFile)
    {
      try {
        return F\Import::file(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sUpgradesDirUpgradeFolder . static::INFO_DIR . PH7_DS . $sInstFile);
      }
      catch (Framework\File\Exception $e)
      {
        return '<p class="error">' . t('Instruction file not found!') . '</p>';
      }
    }
}

// Go
(new UpgradeCore)->display();
