<?php
/**
 * @title            Config Class
 * @desc             Loading and management config files.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Config
 * @version          1.2
 */

namespace PH7\Framework\Config;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\Pattern\Singleton;

/**
 * @class Singleton Class
 */
class Config implements Configurable
{
    const DEVELOPMENT_MODE = 'development';
    const PRODUCTION_MODE = 'production';

    /** @var array */
    public $values = [];

    /** @var string */
    private $sConfigAppFilePath;

    /** @var string */
    private $sConfigSysFilePath;

    /** Import the Singleton trait*/
    use Singleton;

    /**
     * Set to private so nobody can create a new instance using new.
     *
     * @throws FileNotFoundException
     */
    private function __construct()
    {
        $this->sConfigAppFilePath = PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE;
        $this->sConfigSysFilePath = PH7_PATH_SYS . PH7_CONFIG . PH7_CONFIG_FILE;

        if (!is_file($this->sConfigAppFilePath) || !is_file($this->sConfigSysFilePath)) {
            $aFile = !is_file($this->sConfigAppFilePath) ?
                ['code' => FileNotFoundException::APP_FILE, 'filename' => $this->sConfigAppFilePath] :
                ['code' => FileNotFoundException::SYS_FILE, 'filename' => $this->sConfigSysFilePath];

            throw new FileNotFoundException(
                sprintf(
                    '"%s" config file cannot be found.',
                    $aFile['filename']
                ),
                $aFile['code']
            );
        }

        $this->read();
    }

    /**
     * {@inheritdoc}
     */
    public function load($sFile)
    {
        if (!is_file($sFile)) {
            return false;
        }

        $aContents = parse_ini_file($sFile, true);
        $this->values = array_merge($this->values, $aContents);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($sKey)
    {
        return $this->values[$sKey];
    }

    /**
     * {@inheritdoc}
     *
     * @throws KeyAlreadyExistsException
     */
    public function setValue($sKey, $sValue)
    {
        if (!array_key_exists($sKey, $this->values)) {
            $this->values[$sKey] = $sValue;
        } else {
            throw new KeyAlreadyExistsException(sprintf('%s already exists. You cannot reassign a config key.', $sKey));
        }
    }

    /**
     * Set Production Mode site.
     *
     * @return void
     */
    public function setProductionMode()
    {
        $this->setMode(self::PRODUCTION_MODE);
    }

    /**
     * Set Development Mode site.
     *
     * @return void
     */
    public function setDevelopmentMode()
    {
        $this->setMode(self::DEVELOPMENT_MODE);
    }

    /**
     * Set a Mode (Generic method).
     *
     * @param string $sReplace The Mode site.
     *
     * @see Config::setProductionMode()
     * @see Config::setDevelopmentMode()
     *
     * @return void
     */
    private function setMode($sReplace)
    {
        $sSearch = $sReplace === self::DEVELOPMENT_MODE ? self::PRODUCTION_MODE : self::DEVELOPMENT_MODE;

        $oFile = new File;

        // Check and correct the file permission if necessary.
        $oFile->chmod($this->sConfigAppFilePath, Chmod::MODE_ALL_WRITE);

        $sFileContents = $oFile->getFile($this->sConfigAppFilePath);
        $sSearchContents = 'environment = ' . $sSearch;
        $sReplaceContents = 'environment = ' . $sReplace;
        $sNewContents = str_replace($sSearchContents, $sReplaceContents, $sFileContents);
        $oFile->putFile($this->sConfigAppFilePath, $sNewContents);

        // Check and correct the file permission if necessary.
        $oFile->chmod($this->sConfigAppFilePath, Chmod::MODE_WRITE_READ);

        unset($oFile, $sFileContents);
    }

    /**
     * Read Config File.
     *
     * @return void
     */
    private function read()
    {
        /** Load configuration files **/
        // 1) Load app config file
        $this->values = parse_ini_file($this->sConfigAppFilePath, true);
        // 2) Now we have to use array_merge() function, so we do with the Config::load() method for loading system config file
        $this->load($this->sConfigSysFilePath);

        /* The config constants */
        define('PH7_DEFAULT_THEME', $this->values['application']['default_theme']);
        define('PH7_DEFAULT_LANG', $this->values['application']['default_lang']);
    }
}
