<?php
/**
 * @desc             Loading and management config files.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Config
 * @version          1.2
 */

declare(strict_types=1);

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
    private const DEVELOPMENT_MODE = 'development';
    private const PRODUCTION_MODE = 'production';

    public array $values = [];

    private string $sConfigAppFilePath;

    private string $sConfigSysFilePath;

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
    public function load(string $sFile): bool
    {
        if (!is_file($sFile)) {
            return false;
        }

        $aContents = $this->parseIniFile($sFile);
        $this->values = array_merge($this->values, $aContents);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(string $sKey): string
    {
        return $this->values[$sKey];
    }

    /**
     * {@inheritdoc}
     *
     * @throws KeyAlreadyExistsException
     */
    public function setValue(string $sKey, string $sValue): void
    {
        if (!array_key_exists($sKey, $this->values)) {
            $this->values[$sKey] = $sValue;
        } else {
            throw new KeyAlreadyExistsException(sprintf('%s already exists. You cannot reassign a config key.', $sKey));
        }
    }

    public function setProductionMode(): void
    {
        $this->setMode(self::PRODUCTION_MODE);
    }

    public function setDevelopmentMode(): void
    {
        $this->setMode(self::DEVELOPMENT_MODE);
    }

    /**
     * Set a Mode (Generic method).
     *
     * @param string $sReplace The environment mode.
     *
     * @see Config::setProductionMode()
     * @see Config::setDevelopmentMode()
     *
     * @return void
     */
    private function setMode(string $sReplaceMode): void
    {
        $sSearch = $sReplaceMode === self::DEVELOPMENT_MODE ? self::PRODUCTION_MODE : self::DEVELOPMENT_MODE;

        $oFile = new File;

        // Check and correct the file permission if necessary.
        $oFile->chmod($this->sConfigAppFilePath, Chmod::MODE_ALL_WRITE);

        $sFileContents = $oFile->getFile($this->sConfigAppFilePath);
        $sSearchContents = 'environment = ' . $sSearch;
        $sReplaceContents = 'environment = ' . $sReplaceMode;
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
    private function read(): void
    {
        /** Load configuration files **/
        // 1) Load app config file
        $this->values = $this->parseIniFile($this->sConfigAppFilePath);
        // 2) Now we have to use array_merge() function, so we do with the Config::load() method for loading system config file
        $this->load($this->sConfigSysFilePath);

        /* The config constants */
        define('PH7_DEFAULT_THEME', $this->values['application']['default_theme']);
        define('PH7_DEFAULT_LANG', $this->values['application']['default_lang']);
    }

    /**
     * @param string $sFile The ini config file to parse.
     *
     * @return array|bool The file settings as associative array on success, FALSE otherwise.
     */
    private function parseIniFile(string $sFile)
    {
        return parse_ini_file($sFile, true, INI_SCANNER_TYPED);
    }
}
