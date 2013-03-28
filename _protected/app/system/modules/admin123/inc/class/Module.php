<?php
/**
 * @title          Module Management
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 * @version        1.1
 */
namespace PH7;
use PH7\Framework\Core\Core, PH7\Framework\File as F;

class Module
{

    private $_oFile, $_sModsDirModFolder, $_sRoutePath, $_sDefLangRoute = 'en';

    const
    INSTALL = 'install',
    UNINSTALL = 'uninstall',

    DIR = 'module/',
    INSTALL_DIR = 'install/',
    SQL_DIR = 'sql/',
    INFO_DIR = 'info/',
    INSTALL_SQL_FILE = 'MySQL/install.sql',
    UNINSTALL_SQL_FILE = 'MySQL/uninstall.sql',
    INSTALL_INST_CONCL_FILE = 'in_conclusion',
    UNINSTALL_INST_CONCL_FILE = 'un_conclusion',
    ROUTE_FILE = 'route.xml',
    CONFIG_FILE = 'config/config.ini';

    public function __construct()
    {
        $this->_oFile = new F\File;
    }

    public function setPath($sModulesDirModuleFolder)
    {
        $this->_sModsDirModFolder = $sModulesDirModuleFolder;
    }

    public function run($sSwitch)
    {
        if(empty($this->_sModsDirModFolder))
        {
            /**
             * $this->_sModsDirModFolder attribute must be defined by the method Module::setPath() before executing the following methods!
             * See the ModuleController for more information (Module::setPath() method).
             */
            return false;
        }

        $sValue = $this->_checkParam($sSwitch);

        if($sValue == self::INSTALL)
        {
            //$this->_file($sValue);
            $this->_route($sValue);
            $this->_sql($sValue);
         }
         else
         {
            $this->_sql($sValue);
            $this->_route($sValue);
            //$this->_file($sValue);
         }
    }

    /**
     * Shows Available modules.
     *
     * @param string $sSwitch \PH7\Module::INSTALL | \PH7\Module::UNINSTALL
     * @return array List of available modules.
     */
    public function showAvailableMods($sSwitch)
    {
        $sValue = $this->_checkParam($sSwitch);
        $aFolders = array();

        foreach($this->_readMods($sValue) as $sFolder)
            $aFolders[$sFolder] = $sFolder;

        return $aFolders;
    }

    /**
     * Checks if the module is valid.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @param string $sFolder The folder
     * @return boolean Return "true" if correct otherwise "false"
     */
    public function checkModFolder($sSwitch, $sFolder)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sFullPath = ($sValue == self::INSTALL) ? PH7_PATH_REPOSITORY . self::DIR . $sFolder : PH7_PATH_MOD . $sFolder;

        return (!preg_match('#^[a-z0-9\-]{2,35}/?$#i', $sFolder) || !is_file($sFullPath . self::CONFIG_FILE) || (PH7_PATH_REPOSITORY . self::DIR . $sFolder == PH7_PATH_MOD . $sFolder)) ? false : true;
    }

    /**
     * Get the module informations in the config.ini file.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @param string $sFolder
     * @return string Contents file
     */
    public function readConfig($sSwitch, $sFolder)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sPath = ($sValue == self::INSTALL) ? PH7_PATH_REPOSITORY . self::DIR . $sFolder : PH7_PATH_MOD . $sFolder;

        return Framework\Config\Config::getInstance()->load($sPath . self::CONFIG_FILE);
    }

    /**
     * Get the instructions.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @return mixed (string or boolean) Returns "false" if the file does not exist or if it fails otherwise returns the "file contents"
     */
    public function readInstruction($sSwitch)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sDir = self::DIR . $this->_sModsDirModFolder . self::INSTALL_DIR . self::INFO_DIR;
        $sPath = ($sValue == self::INSTALL) ? PH7_PATH_MOD . $sDir . self::INSTALL_INST_CONCL_FILE : PH7_PATH_REPOSITORY . $sDir . self::UNINSTALL_INST_CONCL_FILE;
        $mInstruction = (F\Import::file($sPath));
        return (!$mInstruction) ? '<p class="error">' . t('Instruction file not found!') . '</p>' : $mInstruction;
    }

    /**
     * Read the modules folders.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @return string Returns the module folders
     */
    private function _readMods($sSwitch)
    {
        $sPath = ($sSwitch == self::INSTALL) ? PH7_PATH_REPOSITORY . self::DIR : PH7_PATH_MOD;
        return $this->_oFile->readDirs($sPath);
    }

    /**
     * FOR INSTALL: Movement of the back module of the repository to the modules directory OR FOR UNISTALL: Movement of the back module of the modules directory to the repository.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @return void
     */
    private function _file($sSwitch)
    {
        if($sSwitch == self::INSTALL)
            $this->_oFile->copyMost(PH7_PATH_REPOSITORY . self::DIR . $this->_sModsDirModFolder, PH7_PATH_MOD); // Files of module
        else
            $this->_oFile->copyMost(PH7_PATH_MOD . $this->_sModsDirModFolder,  PH7_PATH_REPOSITORY . self::DIR); // Files of module
    }

    /**
     * FOR INSTALL: Execute SQL statements for module installation OR FOR UNISTALL: Uninstalling the database.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @return void If it found a query SQL error, it display an error message with exit()
     */
    private function _sql($sSwitch)
    {
        $sSqlFile = ($sSwitch == self::INSTALL) ? self::INSTALL_SQL_FILE : UNINSTALL_SQL_FILE;
        $sPath = PH7_PATH_MOD . $this->_sModsDirModFolder . self::INSTALL_DIR . self::SQL_DIR . $sSqlFile;

        if(is_file($sPath) && filesize($sPath) > 12)
        {
            $mQuery = (new ModuleModel)->run($sPath);

            if($mQuery !== true) exit(t('Unable to execute the query SQL of module.<br />Error Message: %0%', '<pre>' . print_r($mQuery) . '</pre>'));
        }
    }

    /**
     * Router for the routes methods Module::addRoute() and Module::removeRoute()
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant
     * @return void
     */
    private function _route($sSwitch)
    {
        $this->_sRoutePath = PH7_PATH_MOD . $this->_sModsDirModFolder . self::INSTALL_DIR . self::ROUTE_FILE;

        if(is_file($this->_sRoutePath))
            ($sSwitch == self::INSTALL) ? $this->_addRoute() : $this->_removeRoute();
    }

    /**
     * Add the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function _addRoute()
    {
        $sModRoute = file_get_contents($this->_sRoutePath);
        return file_put_contents(PH7_PATH_APP_CONFIG . 'routes/' . $this->_sDefLangRoute . '.xml', "\n" . $sModRoute, FILE_APPEND);
    }

    /**
     * Remove the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function _removeRoute()
    {
        $sGlobalRoute = file_get_contents(PH7_PATH_APP_CONFIG . 'routes/' . $this->_sDefLangRoute . '.xml');
        $sModRoute = file_get_contents($this->_sRoutePath);
        $sNewRoute = str_replace("\n" . $sModRoute, '', $sGlobalRoute);

        return file_put_contents(PH7_PATH_APP_CONFIG . 'routes/' . $this->_sDefLangRoute . '.xml', $sNewRoute);
    }

    /**
     * Remove the module repository folder.
     *
     * @param string $sModuleDir Folder of module
     * @return boolean Return "true" if the folder has been deleted otherwise "false"
     */
    private function _removeModDir($sModuleDir)
    {
        return $this->_oFile->deleteDir(PH7_PATH_REPOSITORY . self::DIR . $sModuleDir);
    }

    /**
     * Checks if the constant is correct.
     *
     * Note: This method is valid only for public methods, it is not necessary to check the private methods.
     * @param string $sSwitch The constant check
     * @return string Return the constant if it is correct otherwise an error message with exit()
     */
    private function _checkParam($sSwitch)
    {
        if($sSwitch == self::INSTALL)
        {
            return self::INSTALL;
        }
        elseif($sSwitch == self::UNINSTALL)
        {
            return self::UNINSTALL;
        }
        else
        {
            exit('Wrong value in the parameter of the method: ' . __METHOD__ . ' in the class: ' . __CLASS__);
        }

    }

    public function __destruct()
    {
        unset($this->_oFile, $this->_sModsDirModFolder, $this->_sRoutePath, $this->_sDefLangRoute);
    }

}
