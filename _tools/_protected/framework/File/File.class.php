<?php
/**
 * @title            File Class
 * @desc             Useful methods for handling files.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\File\Permission\PermissionException;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Parse\Url as UrlParser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Server\Server;
use PH7\Framework\Url\Url;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;
use ZipArchive;

class File
{
    const REGEX_BINARY_FILE = '/^(.*?)\.(gif|jpg|jpeg|png|webp|ico|mp3|mp4|mov|avi|flv|mpg|mpeg|wmv|ogg|ogv|webm|pdf|ttf|eot|woff|svg|swf)$/i';

    const RENAME_FUNC_NAME = 'rename';
    const COPY_FUNC_NAME = 'copy';

    const DIR_HANDLE_FUNC_NAMES = [
        self::RENAME_FUNC_NAME,
        self::COPY_FUNC_NAME
    ];

    const WILDCARD_SYMBOL = '*';

    // End Of Line relative to the operating system
    const EOL = PHP_EOL;

    /**
     * Mime Types list.
     *
     * @var array $aMimeTypes
     */
    private static $aMimeTypes = [
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'exe' => 'application/octet-stream',
        'zip' => 'application/zip',
        'doc' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'eot' => 'application/vnd.ms-fontobject',
        'otf' => 'application/octet-stream',
        'ttf' => 'application/octet-stream',
        'woff' => 'application/octet-stream',
        'svg' => 'application/octet-stream',
        'swf' => 'application/x-shockwave-flash',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'php' => 'text/plain',
    ];

    /**
     * @param string $sExt Extension File.
     *
     * @return string (string | null) Returns the "mime type" if it is found, otherwise "null"
     */
    public function getMimeType($sExt)
    {
        return array_key_exists($sExt, self::$aMimeTypes) ? self::$aMimeTypes[$sExt] : null;
    }

    /**
     * Get the file extension, without the dot.
     *
     * @param string $sFile The File Name.
     *
     * @return string
     */
    public function getFileExt($sFile)
    {
        return strtolower(pathinfo($sFile, PATHINFO_EXTENSION));
    }

    /**
     * Give the filename without the dot and the extension (or the last one, if they are more).
     *
     * @param string $sFile
     *
     * @return string
     */
    public function getFileWithoutExt($sFile)
    {
        return pathinfo($sFile, PATHINFO_FILENAME);
    }

    /**
     * Get File Contents.
     *
     * @param string $sFile File name.
     * @param bool $bIncPath Default FALSE
     *
     * @return string|bool Returns the read data or FALSE on failure.
     */
    public function getFile($sFile, $bIncPath = false)
    {
        return @file_get_contents($sFile, $bIncPath);
    }

    /**
     * Put File Contents.
     *
     * @param string $sFile File name.
     * @param string $sContents Contents file.
     * @param int $iFlag Constant (see http://php.net/manual/function.file-put-contents.php).
     *
     * @return int|bool Returns the number of bytes that were written to the file, or FALSE on failure.
     */
    public function putFile($sFile, $sContents, $iFlag = 0)
    {
        return @file_put_contents($sFile, $sContents, $iFlag);
    }

    /**
     * Check if file exists.
     *
     * @param array|string $mFile
     *
     * @return bool TRUE if file exists, FALSE otherwise.
     */
    public function existFile($mFile)
    {
        $bExists = false; // Default value

        if (is_array($mFile)) {
            foreach ($mFile as $sFile) {
                if (!$bExists = $this->existFile($sFile)) {
                    return false;
                }
            }
        } else {
            $bExists = is_file($mFile);
        }

        return $bExists;
    }

    /**
     * Check if directory exists.
     *
     * @param array|string $mDir
     *
     * @return bool TRUE if file exists, FALSE otherwise.
     */
    public function existDir($mDir)
    {
        $bExists = false; // Default value

        if (is_array($mDir)) {
            foreach ($mDir as $sDir) {
                if (!$bExists = $this->existDir($sDir)) {
                    return false;
                }
            }
        } else {
            $bExists = is_dir($mDir);
        }

        return $bExists;
    }

    /**
     * @param string $sDir The directory.
     *
     * @return array The list of the folder that is in the directory.
     */
    public function getDirList($sDir)
    {
        $aDirList = [];

        if ($rHandle = opendir($sDir)) {
            while (false !== ($sFile = readdir($rHandle))) {
                if ($sFile !== '.' && $sFile !== '..' && is_dir($sDir . PH7_DS . $sFile)) {
                    $aDirList[] = $sFile;
                }
            }
            asort($aDirList);
            reset($aDirList);
        }
        closedir($rHandle);

        return $aDirList;
    }

    /**
     * Get file size.
     *
     * @param string $sFile
     *
     * @return int The size of the file in bytes.
     */
    public function size($sFile)
    {
        return (int)@filesize($sFile);
    }

    /**
     * @param string $sDir
     * @param string|array|null $mExt Retrieves only files with specific extensions.
     *
     * @return array List of files sorted alphabetically.
     */
    public function getFileList($sDir, $mExt = null)
    {
        $aTree = [];
        $sDir = $this->checkExtDir($sDir);

        if (is_dir($sDir) && $rHandle = opendir($sDir)) {
            while (false !== ($sFile = readdir($rHandle))) {
                if ($sFile !== '.' && $sFile !== '..') {
                    if (is_dir($sDir . $sFile)) {
                        $aTree = array_merge($aTree, $this->getFileList($sDir . $sFile, $mExt));
                    } else {
                        if ($mExt !== null) {
                            $aExt = (array)$mExt;

                            foreach ($aExt as $sExt) {
                                if (substr($sFile, -strlen($sExt)) === $sExt) {
                                    $aTree[] = $sDir . $sFile;
                                }
                            }
                        } else {
                            $aTree[] = $sDir . $sFile;
                        }
                    }
                }
            }
            sort($aTree);
        }
        closedir($rHandle);

        return $aTree;
    }

    /**
     * Make sure that folder names have a trailing.
     *
     * @param string $sDir The directory.
     * @param bool $bStart for check extension directory start. Default FALSE
     * @param bool $bEnd for check extension end. Default TRUE
     *
     * @return string $sDir Directory
     */
    public function checkExtDir($sDir, $bStart = false, $bEnd = true)
    {
        $bIsWindows = Server::isWindows();

        if (!$bIsWindows && $bStart === true && substr($sDir, 0, 1) !== PH7_DS) {
            $sDir = PH7_DS . $sDir;
        }

        if ($bEnd === true && substr($sDir, -1) !== PH7_DS) {
            $sDir .= PH7_DS;
        }

        return $sDir;
    }

    /**
     * Creates a directory if they are in an array. If it does not exist and
     * allows the creation of nested directories specified in the pathname.
     *
     * @param string|array $mDir
     * @param int (octal) $iMode Default: 0777
     *
     * @return void
     *
     * @throws PermissionException If the file cannot be created.
     */
    public function createDir($mDir, $iMode = Chmod::MODE_ALL_EXEC)
    {
        if (is_array($mDir)) {
            foreach ($mDir as $sDir) {
                $this->createDir($sDir);
            }
        } else {
            if (!is_dir($mDir)) {
                if (!@mkdir($mDir, $iMode, true)) {
                    $sExceptMessage = 'Cannot create "%s" directory.<br /> Please verify that the directory permission is in writing mode.';
                    throw new PermissionException(
                        sprintf($sExceptMessage, $mDir)
                    );
                }
            }
        }
    }

    /**
     * Copy files and checks if the "from file" exists.
     *
     * @param string $sFrom File.
     * @param string $sTo File.
     *
     * @return bool
     */
    public function copy($sFrom, $sTo)
    {
        if (!is_file($sFrom)) {
            return false;
        }

        return @copy($sFrom, $sTo);
    }

    /**
     * Copy the contents of a directory into another.
     *
     * @param string $sFrom Old directory.
     * @param string $sTo New directory.
     *
     * @return bool TRUE if everything went well, otherwise FALSE if the "from directory" couldn't be found or if it couldn't be copied.
     *
     * @throws PH7InvalidArgumentException
     */
    public function copyDir($sFrom, $sTo)
    {
        return $this->recursiveDirIterator($sFrom, $sTo, self::COPY_FUNC_NAME);
    }

    /**
     * Copy a file or directory with the Unix cp command.
     *
     * @param string $sFrom File or directory.
     * @param string $sTo File or directory.
     *
     * @return int|bool Returns the last line on success, and FALSE on failure.
     */
    public function systemCopy($sFrom, $sTo)
    {
        if (file_exists($this->removeWildcards($sFrom))) {
            return system("cp -r $sFrom $sTo");
        }

        return false;
    }

    /**
     * Rename a file or directory and checks if the "from file" or directory exists with file_exists() function
     * since it checks the existence of a file or directory (because, as in the Unix OS, a directory is a file).
     *
     * @param string $sFrom File or directory.
     * @param string $sTo File or directory.
     *
     * @return bool
     */
    public function rename($sFrom, $sTo)
    {
        if (!file_exists($sFrom)) {
            return false;
        }

        return @rename($sFrom, $sTo);
    }

    /**
     * Rename the contents of a directory into another.
     *
     * @param string $sFrom Old directory.
     * @param string $sTo New directory.
     *
     * @return bool TRUE if everything went well, otherwise FALSE if the "from directory" couldn't be found or if it couldn't be renamed.
     *
     * @throws PH7InvalidArgumentException
     */
    public function renameDir($sFrom, $sTo)
    {
        return $this->recursiveDirIterator($sFrom, $sTo, self::RENAME_FUNC_NAME);
    }

    /**
     * Rename a file or directory with the Unix mv command.
     *
     * @param string $sFrom File or directory.
     * @param string $sTo File or directory.
     *
     * @return int|bool Returns the last line on success, and FALSE on failure.
     */
    public function systemRename($sFrom, $sTo)
    {
        if (file_exists($this->removeWildcards($sFrom))) {
            return system("mv $sFrom $sTo");
        }

        return false;
    }

    /**
     * Deletes a file or files if they are in an array.
     * If the file does not exist, the function does nothing.
     *
     * @param string|array $mFile
     *
     * @return void
     */
    public function deleteFile($mFile)
    {
        if (is_array($mFile)) {
            foreach ($mFile as $sF) {
                $this->deleteFile($sF);
            }
        } else {
            if (is_file($mFile)) {
                @unlink($mFile);
            }
        }
    }

    /**
     * For deleting Directory and files!
     * A "rmdir" function improved PHP which also delete files in a directory.
     *
     * @param string $sPath The path
     *
     * @return bool
     */
    public function deleteDir($sPath)
    {
        return (is_file($sPath) ? unlink($sPath) : (is_dir($sPath) ? array_map([$this, 'deleteDir'], glob($sPath . '/*')) === @rmdir($sPath) : false));
    }

    /**
     * Remove the contents of a directory.
     *
     * @param string $sDir
     *
     * @return void
     */
    public function remove($sDir)
    {
        $oIterator = new RecursiveIteratorIterator($this->getDirIterator($sDir), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($oIterator as $sPath) {
            $sPath->isFile() ? unlink($sPath) : @rmdir($sPath);
        }

        @rmdir($sDir);
    }

    /**
     * Clean paths if wildcard is found in order to get valid paths.
     *
     * @param string $sPath
     *
     * @return string
     */
    public function removeWildcards($sPath)
    {
        return str_replace(self::WILDCARD_SYMBOL, '', $sPath);
    }

    /**
     * Get the creation/modification time of a file in the Unix timestamp.
     *
     * @param string $sFile Full path of the file.
     *
     * @return int|bool Returns the time the file was last modified, or FALSE if it not found.
     */
    public function getModifTime($sFile)
    {
        return is_file($sFile) ? filemtime($sFile) : false;
    }

    /**
     * Get the version of a file based on the its latest modification.
     * Shortened form of self::getModifTime()
     *
     * @param string $sFile Full path of the file.
     *
     * @return int Returns the latest modification time of the file in Unix timestamp.
     */
    public static function version($sFile)
    {
        return @filemtime($sFile);
    }

    /**
     * Delay script execution.
     *
     * @param int $iSleep Halt time in seconds.
     *
     * @return int|bool Returns 0 on success, or FALSE on error.
     */
    public function sleep($iSleep = 5)
    {
        return sleep($iSleep);
    }

    /**
     * Changes permission on a file or directory.
     *
     * @param string $sFile
     * @param int $iMode Octal Permission for the file.
     *
     * @return bool
     */
    public function chmod($sFile, $iMode)
    {
        // file_exists function verify the existence of a "file" or "folder"!
        if (file_exists($sFile) && $this->getOctalAccess($sFile) !== $iMode) {
            return @chmod($sFile, $iMode);
        }

        return false;
    }

    /**
     * @param string $sFile
     *
     * @return string Octal Permissions.
     */
    public function getOctalAccess($sFile)
    {
        clearstatcache();
        return substr(sprintf('%o', fileperms($sFile)), -4);
    }

    /**
     * @param string $sData
     *
     * @return string
     */
    public function pack($sData)
    {
        return urlencode(serialize($sData));
    }

    /**
     * Get the size of a directory.
     *
     * @param string $sPath
     *
     * @return int The size of the file in bytes.
     */
    public function getDirSize($sPath)
    {
        if (!is_dir($sPath)) {
            return 0;
        }

        if (!($rHandle = opendir($sPath))) {
            return 0;
        }

        $iSize = 0;
        while (false !== ($sFile = readdir($rHandle))) {
            if ($sFile !== '.' && $sFile !== '..') {
                $sFullPath = $sPath . PH7_DS . $sFile;

                if (is_dir($sFullPath)) {
                    $iSize = $this->getDirSize($sFullPath);
                } else {
                    $iSize += $this->size($sFullPath);
                }
            }
        }
        closedir($rHandle);

        return $iSize;
    }

    /**
     * Get free space of a directory.
     *
     * @param string $sPath
     *
     * @return float The number of available bytes as a float.
     */
    public function getDirFreeSpace($sPath)
    {
        return disk_free_space($sPath);
    }

    /**
     * @param string $sData
     *
     * @return bool|int|float|string|array|object
     */
    public function unpack($sData)
    {
        return unserialize(urldecode($sData));
    }

    /**
     * For download file.
     *
     * @param string $sFile File to download.
     * @param string $sName A name for the file to download.
     * @param string|null $sMimeType
     *
     * @return void
     */
    public function download($sFile, $sName, $sMimeType = null)
    {
        /*
          This function takes a path to a file to output ($sFile),
          the filename that the browser will see ($sName) and
          the MIME type of the file ($sMimeType, optional).

          If you want to do something on download abort/finish,
          register_shutdown_function('function_name');
         */

        //if (!is_readable($sFile)) throw new IOException('File not found or inaccessible!');

        $sName = Url::decode($sName); // Clean the name file

        /* Figure out the MIME type (if not specified) */
        if (empty($sMimeType)) {
            $sFileExtension = $this->getFileExt($sFile);

            $mGetMimeType = $this->getMimeType($sFileExtension);

            $sMimeType = 'application/force-download';
            if (!empty($mGetMimeType)) {
                $sMimeType = $mGetMimeType;
            }
        }

        @ob_end_clean(); // Turn off output buffering to decrease CPU usage

        (new Browser)->noCache(); // No cache

        $sPrefix = Registry::getInstance()->site_name . '_'; // the prefix
        header('Content-Type: ' . $sMimeType);
        header('Content-Disposition: attachment; filename=' . UrlParser::clean($sPrefix) . $sName);
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $this->size($sFile));
        readfile($sFile);
    }

    /**
     * Write Header Contents.
     *
     * @param string $sHeader Text to be shown in the headers
     * @param array $aFile
     *
     * @return void
     */
    public function writeHeader($sHeader, array $aFile = [])
    {
        for ($i = 0, $iCountFiles = count($aFile); $i < $iCountFiles; $i++) {
            $rHandle = fopen($aFile[$i], 'wb+');

            if ($this->size($aFile[$i]) > 0) {
                $sData = fread($rHandle, $this->size($aFile[$i]));
                fwrite($rHandle, $sHeader . static::EOL . $sData);
            }
            fclose($rHandle);
        }
    }

    /**
     * Writes and saves the contents to a file.
     * It also creates a temporary file to not delete the original file if something goes wrong during the recording file.
     *
     * @param string $sFile
     * @param string $sData
     *
     * @return int Returns the number of bytes written, or NULL on error.
     */
    public function save($sFile, $sData)
    {
        $sTmpFile = $this->getFileWithoutExt($sFile) . '.tmp.' . $this->getFileExt($sFile);
        $iWritten = (new SplFileObject($sTmpFile, 'wb'))->fwrite($sData);

        if ($iWritten !== null) {
            // Copy of the temporary file to the original file if no problem occurred.
            copy($sTmpFile, $sFile);
        }

        // Deletes the temporary file.
        $this->deleteFile($sTmpFile);

        return $iWritten;
    }

    /**
     * @param string $sPath
     * @param array|string $mFiles
     *
     * @return array|string The Files.
     */
    public function readFiles($sPath = './', &$mFiles)
    {
        if (!($rHandle = opendir($sPath))) {
            return false;
        }

        while (false !== ($sFile = readdir($rHandle))) {
            if ($sFile !== '.' && $sFile !== '..') {
                if (strpos($sFile, '.') === false) {
                    $this->readFiles($sPath . PH7_DS . $sFile, $mFiles);
                } else {
                    $mFiles[] = $sPath . PH7_DS . $sFile;
                }
            }
        }
        closedir($rHandle);

        return $mFiles;
    }

    /**
     * Reading Directories.
     *
     * @param string $sPath
     *
     * @return array|bool Returns an ARRAY with the folders or FALSE if the folder could not be opened.
     */
    public function readDirs($sPath = './')
    {
        if (!($rHandle = opendir($sPath))) {
            return false; // TODO: Return when yield is used will be OK with PHP 7
        }

        $aRet = []; // TODO: Remove it once yield is used
        while (false !== ($sFolder = readdir($rHandle))) {
            if ($sFolder === '.' || $sFolder === '..' || !is_dir($sPath . $sFolder)) {
                continue;
            }

            //yield $sFolder; // TODO: For PHP 7
            $aRet[] = $sFolder; // TODO: Remove it for yield
        }
        closedir($rHandle);

        return $aRet; // TODO: Remove it for yield
    }

    /**
     * Get the URL contents (For URLs, it is better to use CURL because it is faster than file_get_contents function).
     *
     * @param string $sUrl URL to be read contents.
     *
     * @return string|bool Return the result content on success, FALSE on failure.
     */
    public function getUrlContents($sUrl)
    {
        $rCh = curl_init();
        curl_setopt($rCh, CURLOPT_URL, $sUrl);
        curl_setopt($rCh, CURLOPT_HEADER, 0);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
        $mRes = curl_exec($rCh);
        curl_close($rCh);
        unset($rCh);

        return $mRes;
    }

    /**
     * Extract Zip archive.
     *
     * @param string $sFile Zip file.
     * @param string $sDir Destination to extract the file.
     *
     * @return bool
     */
    public function zipExtract($sFile, $sDir)
    {
        $oZip = new ZipArchive;
        $mRes = $oZip->open($sFile);

        if ($mRes === true) {
            $oZip->extractTo($sDir);
            $oZip->close();
            return true;
        }

        return false; // Return error value
    }

    /**
     * Check if the file is binary.
     *
     * @param string $sFile
     *
     * @return bool
     */
    public function isBinary($sFile)
    {
        if (file_exists($sFile)) {
            if (!is_file($sFile)) {
                return false;
            }

            if (preg_match(self::REGEX_BINARY_FILE, $sFile)) {
                return true;
            }

            $rHandle = fopen($sFile, 'r');
            $sContents = fread($rHandle, 512); // Get 512 bytes of the file.
            fclose($rHandle);
            clearstatcache();

            if (!function_exists('is_binary')) // PHP 6
                return is_binary($sContents);

            return (
                0 or substr_count($sContents, "^ -~", "^\r\n") / 512 > 0.3
                or substr_count($sContents, "\x00") > 0
            );
        }

        return false;
    }

    /**
     * Create a recurive directory iterator for a given directory.
     *
     * @param string $sPath
     *
     * @return RecursiveDirectoryIterator
     */
    private function getDirIterator($sPath)
    {
        return new RecursiveDirectoryIterator($sPath);
    }

    /**
     * Recursive Directory Iterator.
     *
     * @param string $sFrom Directory.
     * @param string $sTo Directory.
     * @param string $sFuncName The function name. Choose between 'copy' and 'rename'.
     *
     * @return bool
     *
     * @throws PH7InvalidArgumentException If the function name is invalid.
     * @throws PermissionException If the directory cannot be created
     *
     */
    private function recursiveDirIterator($sFrom, $sTo, $sFuncName)
    {
        if (!in_array($sFuncName, self::DIR_HANDLE_FUNC_NAMES, true)) {
            throw new PH7InvalidArgumentException('Wrong function name: ' . $sFuncName);
        }

        if (!is_dir($sFrom)) {
            return false;
        }

        $bRet = false; // Default value
        $oIterator = new RecursiveIteratorIterator($this->getDirIterator($sFrom), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($oIterator as $sFromFile) {
            // http://php.net/manual/en/recursivedirectoryiterator.getsubpathname.php#example-4559
            $sDest = $sTo . PH7_DS . $oIterator->getSubPathName();

            if ($sFromFile->isDir()) {
                $this->createDir($sDest);
            } else {
                if (!$bRet = $this->$sFuncName($sFromFile, $sDest)) {
                    return false;
                }
            }
        }

        return $bRet;
    }
}
