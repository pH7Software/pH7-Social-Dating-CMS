<?php
/**
 * @title            Cache Class
 * @desc             Handler Cache.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache
 * @version          1.3
 */

namespace PH7\Framework\Cache;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel, PH7\Framework\Config\Config, PH7\Framework\File\File;

class Cache
{

    const CACHE_DIR = 'pH7_cache/';

    private
    $oFile,
    $sCaheDir,
    $sGroup,
    $sId,
    $iTtl,
    $sPrefix = 'pH7_',
    $bEnabled = true;

    public function __construct()
    {
        $this->oFile = new File;
        $this->bEnabled = (bool) Config::getInstance()->values['cache']['enable.general.cache'];
    }

    /**
     * Enabled/Disabled the cache.
     *
     * @param boolean $bIsEnable
     * @return object this
     */
    public function enabled($bIsEnable)
    {
        $this->bEnabled = (bool) $bIsEnable;

        return $this;
    }

    /**
     * Sets cache directory.
     * If the directory is not correct, the method will cause an exception.
     * If you do not use this method, a default directory will be created.
     *
     * @param string $sCacheDir
     * @return object this
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCacheDir($sCacheDir)
    {
        if (is_dir($sCacheDir))
            $this->sCacheDir = $sCacheDir;
        else
            throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('No Cache directory \'' . $sCacheDir . '\' in template engine <strong>PH7Tpl</strong>');

        return $this;
    }

    /**
     * Sets the cache prefix.
     *
     * @param string $sPrefix
     * @return object this
     */
    public function setPrefix($sPrefix)
    {
        $this->sPrefix = $sPrefix;
        return $this;
    }

    /**
     * Sets the time expire cache.
     *
     * @param integer $iExpire (the time with the 'touch' function).
     * @return object this
     */
    public function setExpire($iExpire)
    {
        // How long to cache for (in seconds, e.g. 3600*24 = 24 hour)
        @touch($this->getFile(), time()+(int)$this->iTtl);

        return $this;
    }

    /**
     * Start the cache.
     *
     * @param string $sGroup The Group Cache (This creates a folder).
     * @param string $sId (The ID for the file).
     * @param integer $iTtl Cache lifetime. If NULL, the file never expires.
     * @return object this
     */
    public function start($sGroup, $sId, $iTtl)
    {
      $this->checkCacheDir();

      if ($this->bEnabled)
      {
          $this->sGroup = $sGroup . PH7_DS;
          $this->sId = $sId;
          $this->iTtl = $iTtl;
          ob_start();
      }

      return $this;
    }

    /**
     * Stop the cache.
     *
     * @param boolean $bPrint TRUE = Display data with ECHO. FALSE = Return data. Default is TRUE.
     * @return string (string | null)
     */
    public function stop($bPrint = true)
    {
        if (!$this->bEnabled) return null;

        $sBuffer = ob_get_contents();
        ob_end_clean();
        $this->write($sBuffer);
        if ($bPrint)
        {
            echo $sBuffer;
            return null;
        }

        return $sBuffer;
    }

    /**
     * Gets the data cache.
     *
     * @param boolean $bPrint Default FALSE
     * @return mixed (boolean | integer | float | string | array | object) Returns the converted cache value if successful otherwise returns false.
     */
    public function get($bPrint = false)
    {
        $mData = $this->read($bPrint);
        if ($mData !== false)
            $mData = unserialize($mData);

        return $mData;
    }

    /**
     * Puts the data in the cache.
     *
     * @param string $sData
     * @return string (object | null) If the cache is disabled, returns null otherwise returns a this object.
     */
    public function put($sData)
    {
        if (!$this->bEnabled) return null;

        $this->write(serialize($sData));

        return $this;
    }

    /**
     * Clear the cache.
     *
     * @return object this
     */
    public function clear()
    {
        if (!empty($this->sId))
            $this->oFile->deleteFile($this->getFile());
         else
            $this->oFile->deleteDir($this->sCacheDir . $this->sGroup);

         return $this;
    }

    /**
     * Reads the Cache.
     *
     * @access private
     * @param boolean $bPrint
     * @return mixed (boolean | string) Returns true or a string if successful otherwise returns false.
     */
    private function read($bPrint)
    {
        if ($this->check())
        {
            $sFile = $this->getFile();
            require $sFile;

            if (!empty($mData))
            {
                if ($bPrint)
                {
                    echo $mData;
                    return true;
                }
                else
                {
                    return $mData;
                }
            }
        }
        return false;
    }

    /**
     * Writes data in a cache file.
     *
     * @access private
     * @param string $sData
     * @return boolean
     * @throws \PH7\Framework\Cache\Exception If the file cannot be written.
     */
    final private function write($sData)
    {
      if (!$this->bEnabled) return null;

      $sFile = $this->getFile();
      $this->oFile->createDir($this->sCacheDir . $this->sGroup);

      $sPhpHeader = 'defined(\'PH7\') or exit(\'Restricted access\');
/*
Created on ' . gmdate('Y-m-d H:i:s') . '
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

        $sData = '<?php ' . $sPhpHeader . '$mData = <<<EOF' . File::EOL . $sData . File::EOL . 'EOF;' . File::EOL . '?>';

        if ($rHandle = @fopen($sFile, 'wb'))
        {
            if (@flock($rHandle, LOCK_EX))
                fwrite($rHandle, $sData);

            fclose($rHandle);
            $this->setExpire($this->iTtl);
            $this->oFile->chmod($sFile, 420);
            return true;
        }
        throw new Exception('Could not write cache file: \'' . $sFile . '\'');
        return false;
    }

    /**
     * Gets the file cache.
     *
     * @access private
     * @return string
     */
    private function getFile()
    {
        return $this->sCacheDir . $this->sGroup . sha1($this->sId) . '.cache.php';
    }

    /**
     * Checks the cache.
     *
     * @access private
     * @return boolean
     */
    private function check()
    {
        $sFile = $this->getFile();
        if (!$this->bEnabled || !is_file($sFile) || (!empty($this->iTtl) && $this->oFile->modificationTime($sFile) < time()))
        {   // If the cache has expired
            $this->oFile->deleteFile($this->getFile());
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     *
     * If the folder cache does not exist, it creates a folder.
     * @access private
     * @return object this
     */
    private function checkCacheDir()
    {
        $this->sCacheDir = (empty($this->sCacheDir)) ? PH7_PATH_CACHE . static::CACHE_DIR : $this->sCacheDir;
        return $this;
    }

    public function __destruct()
    {
        unset(
          $this->oFile,
          $this->sCaheDir,
          $this->sGroup,
          $this->sId,
          $this->iTtl,
          $this->sPrefix,
          $this->bEnabled
        );
    }

}
