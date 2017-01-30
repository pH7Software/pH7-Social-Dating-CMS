<?php
/**
 * @title            Config Class
 * @desc             Loading and management config files.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Config
 * @version          1.2
 */

namespace PH7\Framework\Config;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;

/**
 * @class Singleton Class
 */
class Config implements Configurable
{
    const DEVELOPMENT_MODE = 'development', PRODUCTION_MODE = 'production';

    /**
     * @var array $values;
     */
    public $values = array();

   /**
    * @var string $_sConfigAppFilePath
    */
    private $_sConfigAppFilePath;

    /**
     * @var string $_sConfigAppFilePath
     */
    private $_sConfigSysFilePath;

    /**
     * Import the Singleton trait.
     */
    use \PH7\Framework\Pattern\Singleton;

    /**
     * Set to private so nobody can create a new instance using new.
     */
    private function __construct()
    {
        $this->_sConfigAppFilePath = PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE;
        $this->_sConfigSysFilePath = PH7_PATH_SYS . PH7_CONFIG . PH7_CONFIG_FILE;

        $this->_read();
    }

    /**
     * Load ini file.
     *
     * @param string $sFile
     * @return boolean Returne FALSE if the file doesn't exist, TRUE otherwise.
     */
    public function load($sFile)
    {
        if (!is_file($sFile)) return false;

        $aContents = parse_ini_file($sFile, true);
        $this->values = array_merge($this->values, $aContents);
        return true;
    }

    /**
     * Get a config option by key.
     *
     * @param string $sKey The configuration setting key.
     * @return string
     */
    public function getValue($sKey)
    {
        return $this->values[$sKey];
    }

    /**
     * Set dynamically a value to config data.
     *
     * @param string $sKey A unique config key.
     * @param string $sValue The value to add.
     * @return void
     * @throws PH7InvalidArgumentException
     */
    public function setValue($sKey, $sValue)
    {
        if (!array_key_exists($sKey, $this->values)) {
            $this->values[$sKey] = $sValue;
        } else {
            throw new PH7InvalidArgumentException(sprintf('%s already exists. You cannot reassign a config key.', $sKey));
        }
    }

    /**
     * Set Production Mode site.
     *
     * @return void
     */
    public function setProductionMode()
    {
        $this->_setMode(self::PRODUCTION_MODE);
    }

    /**
     * Set Development Mode site.
     *
     * @return void
     */
    public function setDevelopmentMode()
    {
        $this->_setMode(self::DEVELOPMENT_MODE);
    }

    /**
     * Set a Mode (Generic method).
     *
     * @param string $sReplace The Mode site.
     * @see PH7\Framework\Config\Config::setProductionMode()
     * @see PH7\Framework\Config\Config::setDevelopmentMode()
     * @return void
     */
    private function _setMode($sReplace)
    {
        $sSearch = ($sReplace === self::DEVELOPMENT_MODE) ? self::PRODUCTION_MODE : self::DEVELOPMENT_MODE;

        $oFile = new \PH7\Framework\File\File;

        // Check and correct the file permission if necessary.
        $oFile->chmod($this->_sConfigAppFilePath, 0666);

        $sContents = $oFile->getFile($this->_sConfigAppFilePath);
        $sNewContents = str_replace('environment = ' . $sSearch .  ' ; production or development', 'environment = ' . $sReplace . ' ; production or development', $sContents);
        $oFile->putFile($this->_sConfigAppFilePath, $sNewContents);

        // Check and correct the file permission if necessary.
        $oFile->chmod($this->_sConfigAppFilePath, 0644);

        unset($oFile, $sContents);
    }

    /**
     * Read Config File.
     *
     * @return void
     */
    private function _read()
    {
        /* Loading configuration files */

        // 1) Load app config file
        $this->values = parse_ini_file($this->_sConfigAppFilePath, true);
        // 2) Now we have to use array_merge() function, so we do with the Config::load() method for loading system config file
        $this->load($this->_sConfigSysFilePath);

        /* The config constants */
        define('PH7_DEFAULT_THEME', $this->values['application']['default_theme']);
        define('PH7_DEFAULT_LANG', $this->values['application']['default_lang']);
    }
}
