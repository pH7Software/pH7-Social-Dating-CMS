<?php
/**
 * @title          Module Management
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 * @version        1.1
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\File as F;

@set_time_limit(0);
@ini_set('memory_limit', '528M');

class Module
{
    /** @var F\File */
    private $_oFile;

    /** @var string */
    private $_sModsDirModFolder;

    /** @var string */
    private $_sDefLangRoute;

    /** @var string */
    private $_sRoutePath;

    /** @var string */
    private $_sModRoutePath;

    const INSTALL = 1;
    const UNINSTALL = 2;

    /**
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const DIR = 'module';
    const INSTALL_DIR = 'install';
    const SQL_DIR = 'sql';
    const INFO_DIR = 'info';

    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';
    const INSTALL_INST_CONCL_FILE = 'in_conclusion';
    const UNINSTALL_INST_CONCL_FILE = 'un_conclusion';
    const ROUTE_FILE = 'route.xml';

    public function __construct()
    {
        $this->_oFile = new F\File;
        $this->_sDefLangRoute = PH7_LANG_CODE;
        $this->_sRoutePath = PH7_PATH_APP_CONFIG . 'routes/' . $this->_sDefLangRoute . '.xml';
    }

    public function setPath($sModsDirModFolder)
    {
        $this->_sModsDirModFolder = $sModsDirModFolder;
    }

    public function run($sSwitch)
    {
        if (empty($this->_sModsDirModFolder)) {
            /**
             * $this->_sModsDirModFolder attribute must be defined by the method Module::setPath() before executing the following methods!
             * See the ModuleController for more information (Module::setPath() method).
             */
            return false;
        }

        $sValue = $this->_checkParam($sSwitch);

        if ($sValue === static::INSTALL) {
            $this->_file($sValue);
            $this->_route($sValue);
            $this->_sql($sValue);
         } else {
            $this->_sql($sValue);
            $this->_route($sValue);
            $this->_file($sValue);
         }
    }

    /**
     * Shows Available modules.
     *
     * @param string $sSwitch \PH7\Module::INSTALL | \PH7\Module::UNINSTALL
     *
     * @return array List of available modules.
     */
    public function showAvailableMods($sSwitch)
    {
        $sValue = $this->_checkParam($sSwitch);
        $aFolders = array();

        foreach($this->_readMods($sValue) as $sFolder) {
            $aFolders[$sFolder] = $sFolder;
        }

        return $aFolders;
    }

    /**
     * Checks if the module is valid.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     * @param string $sFolder The folder
     *
     * @return boolean Returns TRUE if it is correct, FALSE otherwise.
     */
    public function checkModFolder($sSwitch, $sFolder)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sFullPath = ($sValue === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder : PH7_PATH_MOD . $sFolder;

        return !preg_match('#^[a-z0-9\-]{2,35}#i', $sFolder) || !is_file($sFullPath . PH7_CONFIG . PH7_CONFIG_FILE) || (PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder === PH7_PATH_MOD . $sFolder) ? false : true;
    }

    /**
     * Get the module information in the config.ini file.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     * @param string $sFolder
     *
     * @return boolean
     */
    public function readConfig($sSwitch, $sFolder)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sPath = ($sValue === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder : PH7_PATH_MOD . $sFolder;

        return Config::getInstance()->load($sPath . PH7_CONFIG . PH7_CONFIG_FILE);
    }

    /**
     * Get the instructions.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return string|boolean Returns "false" if the file does not exist or if it fails, otherwise returns the "file contents".
     */
    public function readInstruction($sSwitch)
    {
        $sValue = $this->_checkParam($sSwitch);
        $sDir = $this->_sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::INFO_DIR . PH7_DS;
        $sPath = ($sValue === static::INSTALL) ? PH7_PATH_MOD . $sDir . static::INSTALL_INST_CONCL_FILE : PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sDir . static::UNINSTALL_INST_CONCL_FILE;

        try {
            return F\Import::file($sPath);
        } catch (Framework\File\Exception $e) {
            return '<p class="error">' . t('Instruction file not found!') . '</p>';
        }
    }

    /**
     * Read the modules folders.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return array Returns the module folders.
     */
    private function _readMods($sSwitch)
    {
        $sPath = ($sSwitch === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS : PH7_PATH_MOD;

        return $this->_oFile->readDirs($sPath);
    }

    /**
     * FOR INSTALL: Movement of the back module of the repository to the modules directory OR FOR UNISTALL: Movement of the back module of the modules directory to the repository.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void
     */
    private function _file($sSwitch)
    {
        if ($sSwitch === static::INSTALL) {
            $this->_oFile->systemRename(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sModsDirModFolder, PH7_PATH_MOD); // Files of module
            $this->_oFile->chmod(PH7_PATH_MOD . $this->_sModsDirModFolder, 0777);
        } else {
            $this->_oFile->systemRename(PH7_PATH_MOD . $this->_sModsDirModFolder, PH7_PATH_REPOSITORY . static::DIR . PH7_DS); // Files of module
            $this->_oFile->chmod(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->_sModsDirModFolder, 0777);
        }
    }

    /**
     * FOR INSTALL: Execute SQL statements for module installation OR FOR UNISTALL: Uninstalling the database.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void If it found a query SQL error, it display an error message with exit() function.
     */
    private function _sql($sSwitch)
    {
        $sSqlFile = Config::getInstance()->values['database']['type_name'] . PH7_DS . ($sSwitch === static::INSTALL ? static::INSTALL_SQL_FILE : static::UNINSTALL_SQL_FILE);
        $sPath = PH7_PATH_MOD . $this->_sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::SQL_DIR . PH7_DS . $sSqlFile;

        if (is_file($sPath) && filesize($sPath) > 12) {
            $mQuery = (new ModuleModel)->run($sPath);

            if ($mQuery !== true) {
                exit(t('Unable to execute the query SQL of module.<br />Error Message: %0%', '<pre>' . print_r($mQuery) . '</pre>'));
            }
        }
    }

    /**
     * Add or remove the routes module.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void
     */
    private function _route($sSwitch)
    {
        $this->_sModRoutePath = PH7_PATH_MOD . $this->_sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::ROUTE_FILE;

        if (is_file($this->_sModRoutePath)) {
            ($sSwitch === static::INSTALL) ? $this->_addRoute() : $this->_removeRoute();
        }
    }

    /**
     * Add the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function _addRoute()
    {
        $sRoute = $this->_oFile->getFile($this->_sRoutePath);
        $sModRoute = $this->_oFile->getFile($this->_sModRoutePath);

        $sNewRoute = str_replace('</routes>', '', $sRoute);
        $sNewRoute .= $sModRoute . F\File::EOL . '</routes>';

        return $this->_oFile->putFile($this->_sRoutePath, $sNewRoute);
    }

    /**
     * Remove the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function _removeRoute()
    {
        $sRoute = $this->_oFile->getFile($this->_sRoutePath);
        $sModRoute = $this->_oFile->getFile($this->_sModRoutePath);

        $sNewRoute = str_replace($sModRoute . F\File::EOL, '', $sRoute);

        return $this->_oFile->putFile($this->_sRoutePath, $sNewRoute);
    }

    /**
     * Remove the module repository folder.
     *
     * @param string $sModuleDir Folder of module.
     *
     * @return boolean Returns TRUE if the folder has been deleted, FALSE otherwise.
     */
    private function _removeModDir($sModuleDir)
    {
        return $this->_oFile->deleteDir(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sModuleDir);
    }

    /**
     * Checks if the constant is correct.
     *
     * Note: This method is valid only for public methods, it is not necessary to check the private methods.
     * @param string $sSwitch The check constant.
     *
     * @return string Returns the constant if it is correct, otherwise an error message with exit() function.
     */
    private function _checkParam($sSwitch)
    {
        if ($sSwitch === static::INSTALL) {
            return static::INSTALL;
        }

        if ($sSwitch === static::UNINSTALL){
            return static::UNINSTALL;
        }

        exit('Wrong value in the parameter of the method: ' . __METHOD__ . ' in the class: ' . __CLASS__);
    }
}
