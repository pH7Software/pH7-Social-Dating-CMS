<?php
/**
 * @title            Cache Class
 * @desc             Handler Cache.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache
 * @version          1.3
 */

namespace PH7\Framework\Cache;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;

class Cache
{
    const CACHE_DIR = 'pH7_cache/';
    const CACHE_FILE_EXT = '.cache.php';

    /** @var File */
    private $_oFile;

    /** @var string */
    private $_sCacheDir;

    /** @var string */
    private $_sGroup;

    /** @var string */
    private $_sId;

    /** @var integer */
    private $_iTtl;

    /** @var string */
    private $_sPrefix = 'pH7_';

    /** @var boolean */
    private $_bEnabled = true;

    public function __construct()
    {
        $this->_oFile = new File;
        $this->_bEnabled = (bool)Config::getInstance()->values['cache']['enable.general.cache'];
    }

    /**
     * Enabled/Disabled the cache.
     *
     * @param boolean $bIsEnable
     *
     * @return self
     */
    public function enabled($bIsEnable)
    {
        $this->_bEnabled = (bool)$bIsEnable;

        return $this;
    }

    /**
     * Sets cache directory.
     * If the directory is not correct, the method will cause an exception.
     * If you do not use this method, a default directory will be created.
     *
     * @param string $sCacheDir
     *
     * @return self
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCacheDir($sCacheDir)
    {
        if (is_dir($sCacheDir)) {
            $this->_sCacheDir = $sCacheDir;
        } else {
            throw new PH7InvalidArgumentException('"' . $sCacheDir . '" cache directory cannot be found!');
        }

        return $this;
    }

    /**
     * Sets the cache prefix.
     *
     * @param string $sPrefix
     *
     * @return self
     */
    public function setPrefix($sPrefix)
    {
        $this->_sPrefix = $sPrefix;
        return $this;
    }

    /**
     * Start the cache.
     *
     * @param string $sGroup The Group Cache (This creates a folder).
     * @param string $sId (The ID for the file).
     * @param integer $iTtl Cache lifetime in seconds. If NULL, the file never expires.
     *
     * @return self
     */
    public function start($sGroup, $sId, $iTtl)
    {
        $this->_checkCacheDir();

        if ($this->_bEnabled) {
            $this->_sGroup = $sGroup . PH7_DS;
            $this->_sId = $sId;
            $this->_iTtl = $iTtl;
            ob_start();
        }

        return $this;
    }

    /**
     * Stop the cache.
     *
     * @param boolean $bPrint TRUE = Display data with ECHO. FALSE = Return data. Default TRUE.
     *
     * @return string|null
     */
    public function stop($bPrint = true)
    {
        if (!$this->_bEnabled) {
            return null;
        }

        $sBuffer = ob_get_contents();
        ob_end_clean();
        $this->_write($sBuffer);

        if ($bPrint) {
            echo $sBuffer;
            return null;
        }

        return $sBuffer;
    }

    /**
     * Sets the time expire cache.
     *
     * @param integer $iExpire (the time with the 'touch' function).
     *
     * @return self
     */
    public function setExpire($iExpire)
    {
        // How long to cache for (in seconds, e.g. 3600*24 = 24 hour)
        @touch($this->_getFile(), time() + (int)$this->_iTtl);

        return $this;
    }

    /**
     * Gets the data cache.
     *
     * @param boolean $bPrint Default FALSE
     * @return boolean|integer|float|string|array|object Returns the converted cache value if successful, FALSE otherwise.
     */
    public function get($bPrint = false)
    {
        $mData = $this->_read($bPrint);

        if ($mData !== false) {
            $mData = unserialize($mData);
        }

        return $mData;
    }

    /**
     * Puts the data in the cache.
     *
     * @param string $sData
     *
     * @return string|null|self If the cache is disabled, returns null, otherwise returns this class.
     */
    public function put($sData)
    {
        if (!$this->_bEnabled) {
            return null;
        }

        $this->_write(serialize($sData));

        return $this;
    }

    /**
     * Clear the cache.
     *
     * @return self this
     */
    public function clear()
    {
        if (!empty($this->_sId))
            $this->_oFile->deleteFile($this->_getFile());
        else
            $this->_oFile->deleteDir($this->_sCacheDir . $this->_sGroup);

        return $this;
    }

    /**
     * Get the creation/modification time of the current cache file.
     *
     * @return integer|boolean Time the file was last modified/created as a Unix timestamp, or FALSE on failure.
     */
    public function getTimeOfCacheFile()
    {
        return $this->_oFile->getModifTime($this->_getFile());
    }

    /**
     * Get the header content to put in the file.
     *
     * @return string
     */
    final protected function getHeaderContents()
    {
        return 'defined(\'PH7\') or exit(\'Restricted access\');
/*
Created on ' . gmdate('Y-m-d H:i:s') . '
File ID: ' . $this->_sId . '
*/
/***************************************************************************
 *     ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_COMPANY . '
 *               --------------------
 * @since      Mon Oct 14 2011
 * @author     SORIA Pierre-Henry
 * @email      ' . Kernel::SOFTWARE_EMAIL . '
 * @link       ' . Kernel::SOFTWARE_WEBSITE . '
 * @copyright  ' . Kernel::SOFTWARE_COPYRIGHT . '
 * @license    ' . Kernel::SOFTWARE_LICENSE . '
 ***************************************************************************/
';
    }

    /**
     * Checks the cache.
     *
     * @return boolean
     */
    private function _check()
    {
        $sFile = $this->_getFile();

        if (!$this->_bEnabled || !is_file($sFile) || (!empty($this->_iTtl) && $this->_oFile->getModifTime($sFile) < time())) {
            // If the cache has expired
            $this->_oFile->deleteFile($this->_getFile());
            return false;
        }

        return true;
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the folder cache does not exist, it creates a folder.
     *
     * @return self
     */
    private function _checkCacheDir()
    {
        $this->_sCacheDir = (empty($this->_sCacheDir)) ? PH7_PATH_CACHE . static::CACHE_DIR : $this->_sCacheDir;

        return $this;
    }

    /**
     * Gets the file cache.
     *
     * @return string
     */
    private function _getFile()
    {
        return $this->_sCacheDir . $this->_sGroup . sha1($this->_sId) . static::CACHE_FILE_EXT;
    }

    /**
     * Reads the Cache.
     *
     * @param boolean $bPrint
     * @return boolean|string Returns TRUE or a string if successful, FALSE otherwise.
     */
    private function _read($bPrint)
    {
        if ($this->_check()) {
            require $this->_getFile();

            if (!empty($_mData)) {
                if ($bPrint) {
                    echo $_mData;
                    return true;
                } else {
                    return $_mData;
                }
            }
        }

        return false;
    }

    /**
     * Writes data in a cache file.
     *
     * @param string $sData
     *
     * @return boolean
     *
     * @throws Exception If the file cannot be written.
     */
    final private function _write($sData)
    {
        if (!$this->_bEnabled) {
            return null;
        }

        $sFile = $this->_getFile();
        $this->_oFile->createDir($this->_sCacheDir . $this->_sGroup);

        $sPhpHeader = $this->getHeaderContents();

        $sData = '<?php ' . $sPhpHeader . '$_mData = <<<\'EOF\'' . File::EOL . $sData . File::EOL . 'EOF;' . File::EOL . '?>';

        if ($rHandle = @fopen($sFile, 'wb')) {
            if (@flock($rHandle, LOCK_EX)) {
                fwrite($rHandle, $sData);
            }

            fclose($rHandle);
            $this->setExpire($this->_iTtl);
            $this->_oFile->chmod($sFile, 420);
            return true;
        }

        throw new Exception('Could not write cache file: \'' . $sFile . '\'');

        return false;
    }
}
