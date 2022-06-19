<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Cache
 */

declare(strict_types=1);

namespace PH7\Framework\Cache;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;
use PH7\Framework\File\GenerableFile;

class Cache implements GenerableFile
{
    public const CACHE_DIR = 'pH7_cache/';

    private const DATETIME_FORMAT = 'Y-m-d H:i:s';
    private const CACHE_FILE_EXT = '.cache.php';

    private File $oFile;

    private string $sCacheDir;

    private string $sGroup;

    private ?string $sId;

    private int $iTtl;

    private string $sPrefix = 'pH7_';

    private bool $bEnabled = true;

    public function __construct()
    {
        $this->oFile = new File;
        $this->bEnabled = (bool)Config::getInstance()->values['cache']['enable.general.cache'];
    }

    /**
     * Enabled/Disabled the cache.
     *
     * @param bool $bIsEnable
     *
     * @return self
     */
    public function enabled(bool $bIsEnable): self
    {
        $this->bEnabled = $bIsEnable;

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
    public function setCacheDir(string $sCacheDir): self
    {
        if (is_dir($sCacheDir)) {
            $this->sCacheDir = $sCacheDir;
        } else {
            throw new PH7InvalidArgumentException(
                sprintf('"%s" cache directory cannot be found!', $sCacheDir)
            );
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
    public function setPrefix(string $sPrefix): self
    {
        $this->sPrefix = $sPrefix;

        return $this;
    }

    /**
     * Start the cache.
     *
     * @param string $sGroup The Group Cache (This creates a folder).
     * @param string|null $sId (The ID for the file).
     * @param int|null $iTtl Cache lifetime in seconds. If NULL, the file never expires.
     *
     * @return self
     */
    public function start(string $sGroup, ?string $sId, ?int $iTtl): self
    {
        $this->checkCacheDir();

        $this->sGroup = $sGroup . PH7_DS;
        $this->sId = $sId;
        $this->iTtl = (int)$iTtl;

        return $this;
    }

    /**
     * Sets the time expire cache.
     *
     * @return self
     */
    public function setExpire(): self
    {
        // How long to cache for (in seconds, e.g. 3600*24 = 24 hour)
        @touch($this->getFile(), time() + $this->iTtl);

        return $this;
    }

    /**
     * Gets the data cache.
     *
     * @param bool $bPrint
     *
     * @return bool|int|float|string|array|object Returns the converted cache value if successful, FALSE otherwise.
     */
    public function get(bool $bPrint = false)
    {
        $mData = $this->read($bPrint);

        if ($mData !== false) {
            $mData = unserialize($mData);
        }

        return $mData;
    }

    /**
     * Puts the data in the cache.
     *
     * @param bool|int|float|string|array|object $mData
     *
     * @return string|null|self If the cache is disabled, returns null, otherwise returns this class.
     *
     * @throws IOException
     */
    public function put($mData)
    {
        if (!$this->bEnabled) {
            return null;
        }

        $this->write(serialize($mData));

        return $this;
    }

    /**
     * Clear the cache.
     *
     * @return self this
     */
    public function clear(): self
    {
        if (!empty($this->sId)) {
            $this->oFile->deleteFile($this->getFile());
        } else {
            $this->oFile->deleteDir($this->sCacheDir . $this->sGroup);
        }

        return $this;
    }

    /**
     * Get the creation/modification time of the current cache file.
     *
     * @return int|bool Time the file was last modified/created as a Unix timestamp, or FALSE on failure.
     */
    public function getTimeOfCacheFile()
    {
        return $this->oFile->getModifTime($this->getFile());
    }

    /**
     * Get the header content to put in the file.
     *
     * @return string
     */
    final public function getHeaderContents(): string
    {
        return 'defined(\'PH7\') or exit(\'Restricted access\');
/*
Created on ' . gmdate(static::DATETIME_FORMAT) . '
File ID: ' . $this->sId . '
*/
/**
 * @author     Pierre-Henry Soria
 * @email      ' . Kernel::SOFTWARE_EMAIL . '
 * @link       ' . Kernel::SOFTWARE_WEBSITE . '
 * @copyright  ' . sprintf(Kernel::SOFTWARE_COPYRIGHT, date('Y')) . '
 */
';
    }

    /**
     * Checks the cache.
     *
     * @return bool
     */
    private function check(): bool
    {
        if ($this->hasCacheExpired()) {
            $this->oFile->deleteFile($this->getFile());
            return false;
        }

        return true;
    }

    /**
     * Check if the cache has expired.
     *
     * @return bool
     */
    private function hasCacheExpired(): bool
    {
        $sFile = $this->getFile();

        return !$this->bEnabled || !is_file($sFile) || (!empty($this->iTtl) && $this->oFile->getModifTime($sFile) < time());
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the folder cache does not exist, it creates a folder.
     *
     * @return self
     */
    private function checkCacheDir(): self
    {
        $this->sCacheDir = empty($this->sCacheDir) ? PH7_PATH_CACHE . static::CACHE_DIR : $this->sCacheDir;

        return $this;
    }

    /**
     * Gets the file cache.
     *
     * @return string
     */
    private function getFile(): string
    {
        return $this->sCacheDir . $this->sGroup . sha1($this->sId) . static::CACHE_FILE_EXT;
    }

    /**
     * Reads the Cache.
     *
     * @param bool $bPrint
     *
     * @return bool|string Returns TRUE or a string if successful, FALSE otherwise.
     */
    private function read(bool $bPrint)
    {
        if ($this->check()) {
            require $this->getFile();

            /** @internal $_mData is in the cache file included just above */
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
     * @param string $sSerializedData
     *
     * @return bool|null
     *
     * @throws IOException If the file cannot be written.
     * @throws \PH7\Framework\File\Permission\PermissionException If the file cannot be created.
     */
    private function write(string $sSerializedData): ?bool
    {
        if (!$this->bEnabled) {
            return null;
        }

        $sFile = $this->getFile();
        $this->oFile->createDir($this->sCacheDir . $this->sGroup);

        $sPhpHeader = $this->getHeaderContents();

        $sData = '<?php ' . $sPhpHeader . '$_mData = <<<\'EOF\'' . File::EOL . $sSerializedData . File::EOL . 'EOF;' . File::EOL;

        if ($rHandle = @fopen($sFile, 'wb')) {
            if (@flock($rHandle, LOCK_EX)) {
                fwrite($rHandle, $sData);
            }

            fclose($rHandle);
            $this->setExpire();
            $this->oFile->chmod($sFile, 420);
            return true;
        }

        throw new IOException('Could not write cache file: ' . $sFile);
    }
}
