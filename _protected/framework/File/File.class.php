<?php

/**
 * @desc             Useful methods for handling files.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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

class File
{
    public const REGEX_BINARY_FILE = '/^(.*?)\.(gif|jpg|jpeg|png|webp|ico|mp3|mp4|mov|avi|flv|mpg|mpeg|wmv|ogg|ogv|webm|pdf|ttf|eot|woff|svg|swf)$/i';

    public const RENAME_FUNC_NAME = 'rename';
    public const COPY_FUNC_NAME = 'copy';

    public const DIR_HANDLE_FUNC_NAMES = [
        self::RENAME_FUNC_NAME,
        self::COPY_FUNC_NAME
    ];

    // End Of Line relative to the operating system
    public const EOL = PHP_EOL;
    private const MAX_ZIP_ENTRIES = 10000;
    private const MAX_ZIP_EXPANSION_RATIO = 1000;
    private const MAX_ZIP_UNCOMPRESSED_BYTES = 1073741824;

    private const WILDCARD_SYMBOL = '*';

    /**
     * Mime Types list.
     */
    private static array $aMimeTypes = [
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
     * @param string $sExt extension File
     *
     * @return string|null Returns the "mime type" if it is found, otherwise "null"
     */
    public function getMimeType(string $sExt): ?string
    {
        return array_key_exists($sExt, self::$aMimeTypes) ? self::$aMimeTypes[$sExt] : null;
    }

    /**
     * Get the file extension, without the dot.
     *
     * @param string $sFile the File Name
     */
    public function getFileExt(string $sFile): string
    {
        return strtolower(pathinfo($sFile, PATHINFO_EXTENSION));
    }

    public static function getFileExtWithDot(string $sFile): string
    {
        $sExtension = pathinfo($sFile, PATHINFO_EXTENSION);

        return $sExtension !== '' ? '.' . strtolower($sExtension) : '';
    }

    /**
     * Return only the final path component for both Unix and Windows input.
     */
    public static function getFileBasename(string $sFile): string
    {
        if (strpos($sFile, "\0") !== false) {
            return '';
        }

        $sBasename = basename(str_replace('\\', PH7_SH, $sFile));

        return in_array($sBasename, ['', '.', '..'], true) ? '' : $sBasename;
    }

    public static function isPathInsideDirectory(string $sFilePath, string $sDirectoryPath): bool
    {
        return strpos($sFilePath . PH7_DS, rtrim($sDirectoryPath, PH7_DS) . PH7_DS) === 0;
    }

    /**
     * Give the filename without the dot and the extension (or the last one, if they are more).
     */
    public function getFileWithoutExt(string $sFile): string
    {
        return pathinfo($sFile, PATHINFO_FILENAME);
    }

    /**
     * Get File Contents.
     *
     * @param string $sFile    file name
     * @param bool   $bIncPath Default FALSE
     *
     * @return string|bool returns the read data or FALSE on failure
     */
    public function getFile(string $sFile, bool $bIncPath = false): string|bool
    {
        return @file_get_contents($sFile, $bIncPath);
    }

    /**
     * Put File Contents.
     *
     * @param string $sFile     file name
     * @param string $sContents contents file
     * @param int    $iFlag     Flag filesystem constant (see http://php.net/manual/function.file-put-contents.php).
     *
     * @return int|bool returns the number of bytes that were written to the file, or FALSE on failure
     */
    public function putFile(string $sFile, string $sContents, int $iFlag = 0): int|bool
    {
        return @file_put_contents($sFile, $sContents, $iFlag);
    }

    /**
     * Check if file exists.
     *
     * @param array|string $mFile
     *
     * @return bool TRUE if file exists, FALSE otherwise
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
     * @return bool TRUE if file exists, FALSE otherwise
     */
    public function existDir($mDir): bool
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
     * @param string $sDir the directory
     *
     * @return array the list of the folder that is in the directory
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
            closedir($rHandle);
        }

        return $aDirList;
    }

    /**
     * Get file size.
     *
     * @return int the size of the file in bytes
     */
    public function size(string $sFile): int
    {
        return (int)@filesize($sFile);
    }

    /**
     * @param string|array|null $mExt retrieves only files with specific extensions
     *
     * @return array list of files sorted alphabetically
     */
    public function getFileList(string $sDir, string|array|null $mExt = null): array
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
            closedir($rHandle);
        }

        return $aTree;
    }

    /**
     * Make sure that folder names have a trailing.
     *
     * @param string $sDir   the directory
     * @param bool   $bStart for check extension directory start. Default FALSE
     * @param bool   $bEnd   for check extension end. Default TRUE
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
     * @param int (octal)  $iMode Default: 0777
     *
     * @throws PermissionException if the file cannot be created
     *
     * @return void
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
                    throw new PermissionException(sprintf($sExceptMessage, $mDir));
                }
            }
        }
    }

    /**
     * Copy files and checks if the "from file" exists.
     *
     * @param string $sFrom file
     * @param string $sTo   file
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
     * @param string $sFrom old directory
     * @param string $sTo   new directory
     *
     * @throws PH7InvalidArgumentException
     *
     * @return bool TRUE if everything went well, otherwise FALSE if the "from directory" couldn't be found or if it couldn't be copied
     */
    public function copyDir($sFrom, $sTo)
    {
        return $this->recursiveDirIterator($sFrom, $sTo, self::COPY_FUNC_NAME);
    }

    /**
     * Copy a file or directory with the Unix cp command.
     *
     * @param string $sFrom file or directory
     * @param string $sTo   file or directory
     *
     * @return int|bool returns the last line on success, and FALSE on failure
     */
    public function systemCopy($sFrom, $sTo)
    {
        if (file_exists($this->removeWildcards($sFrom))) {
            $sFromArg = escapeshellarg($sFrom);
            $sToArg = escapeshellarg($sTo);

            return system("cp -r -- $sFromArg $sToArg");
        }

        return false;
    }

    /**
     * Rename a file or directory and checks if the "from file" or directory exists with file_exists() function
     * since it checks the existence of a file or directory (because, as in the Unix OS, a directory is a file).
     *
     * @param string $sFrom file or directory
     * @param string $sTo   file or directory
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
     * @param string $sFrom old directory
     * @param string $sTo   new directory
     *
     * @throws PH7InvalidArgumentException
     *
     * @return bool TRUE if everything went well, otherwise FALSE if the "from directory" couldn't be found or if it couldn't be renamed
     */
    public function renameDir($sFrom, $sTo)
    {
        return $this->recursiveDirIterator($sFrom, $sTo, self::RENAME_FUNC_NAME);
    }

    /**
     * Rename a file or directory with the Unix mv command.
     *
     * @param string $sFrom file or directory
     * @param string $sTo   file or directory
     *
     * @return int|bool returns the last line on success, and FALSE on failure
     */
    public function systemRename($sFrom, $sTo)
    {
        if (file_exists($this->removeWildcards($sFrom))) {
            $sFromArg = escapeshellarg($sFrom);
            $sToArg = escapeshellarg($sTo);

            return system("mv -- $sFromArg $sToArg");
        }

        return false;
    }

    /**
     * Deletes a file or files if they are in an array.
     * If the file does not exist, the function does nothing.
     *
     * @param string|array $mFile
     */
    public function deleteFile($mFile): void
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
     */
    public function deleteDir(string $sPath): bool
    {
        return is_file($sPath) ? unlink($sPath) : (is_dir($sPath) ? array_map(
            [$this, 'deleteDir'],
            glob($sPath . '/*')
        ) === @rmdir($sPath) : false);
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
        $oIterator = new \RecursiveIteratorIterator(
            $this->getDirIterator($sDir), \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($oIterator as $sPath) {
            $sPath->isFile() ? unlink($sPath) : @rmdir($sPath);
        }

        @rmdir($sDir);
    }

    /**
     * Clean paths if wildcard is found in order to get valid paths.
     */
    public function removeWildcards(string $sPath): string
    {
        return str_replace(self::WILDCARD_SYMBOL, '', $sPath);
    }

    /**
     * Get the creation/modification time of a file in the Unix timestamp.
     *
     * @param string $sFile full path of the file
     *
     * @return int|bool returns the time the file was last modified, or FALSE if it not found
     */
    public function getModifTime($sFile)
    {
        return is_file($sFile) ? filemtime($sFile) : false;
    }

    /**
     * Get the version of a file based on the its latest modification.
     * Shortened form of self::getModifTime().
     *
     * @param string $sFile full path of the file
     *
     * @return int returns the latest modification time of the file in Unix timestamp
     */
    public static function version($sFile)
    {
        return @filemtime($sFile);
    }

    /**
     * Delay script execution.
     *
     * @param int $iSleep halt time in seconds
     *
     * @return int|bool returns 0 on success, or FALSE on error
     */
    public function sleep($iSleep = 5)
    {
        return sleep($iSleep);
    }

    /**
     * Changes permission on a file or directory.
     *
     * @param string $sFile
     * @param int    $iMode octal Permission for the file
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
     * @return string octal Permissions
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
     * @return int the size of the file in bytes
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
                    $iSize += $this->getDirSize($sFullPath);
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
     * @return float the number of available bytes as a float
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
        return unserialize(urldecode($sData), ['allowed_classes' => false]);
    }

    /**
     * For download file.
     *
     * @param string      $sFile     file to download
     * @param string      $sName     a name for the file to download
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

        // if (!is_readable($sFile)) throw new IOException('File not found or inaccessible!');

        $sName = Url::decode($sName); // Clean the name file

        /* Figure out the MIME type (if not specified) */
        if (empty($sMimeType)) {
            $sFileExtension = $this->getFileExt($sFile);
            $mGetMimeType = $this->getMimeType($sFileExtension);

            $sMimeType = 'application/octet-stream'; // default MIME type
            if (!empty($mGetMimeType)) {
                $sMimeType = $mGetMimeType;
            }
        }

        @ob_end_clean(); // Turn off output buffering to decrease CPU usage

        (new Browser())->noCache(); // No cache

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
     *
     * @return void
     */
    public function writeHeader($sHeader, array $aFile = [])
    {
        for ($i = 0, $iCountFiles = count($aFile); $i < $iCountFiles; ++$i) {
            $rHandle = fopen($aFile[$i], 'rb+');
            if ($rHandle === false) {
                continue;
            }

            $sData = stream_get_contents($rHandle);
            rewind($rHandle);
            ftruncate($rHandle, 0);
            fwrite($rHandle, $sHeader . static::EOL . (string)$sData);
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
     * @return int|false returns the number of bytes written, or FALSE on error
     */
    public function save($sFile, $sData)
    {
        $sTemporaryFile = @tempnam(sys_get_temp_dir(), 'ph7-file-save-');
        if ($sTemporaryFile === false) {
            return false;
        }

        try {
            $iWritten = @file_put_contents($sTemporaryFile, $sData, LOCK_EX);
            if ($iWritten === false || $iWritten !== strlen($sData)) {
                return false;
            }

            return @copy($sTemporaryFile, $sFile) ? $iWritten : false;
        } finally {
            @unlink($sTemporaryFile);
        }
    }

    /**
     * Reading Directories.
     *
     * @param string $sPath the full path
     *
     * @return array|bool returns an ARRAY with the folders or FALSE if the folder could not be opened
     */
    public function readDirs(string $sPath = './')
    {
        if (!($rHandle = opendir($sPath))) {
            return false;
        }

        $aRet = [];
        while (false !== ($sFolder = readdir($rHandle))) {
            if ($sFolder === '.' || $sFolder === '..' || !is_dir($sPath . $sFolder)) {
                continue;
            }

            $aRet[] = $sFolder;
        }
        closedir($rHandle);

        return $aRet;
    }

    /**
     * Get the URL contents (For URLs, it is better to use CURL because it is faster than file_get_contents function).
     *
     * @param string $sUrl URL to be read contents
     *
     * @return string|bool return the result content on success, FALSE on failure
     */
    public function getUrlContents(string $sUrl)
    {
        if (function_exists('curl_init')) {
            $rCh = curl_init();
            if ($rCh === false) {
                return false;
            }

            curl_setopt($rCh, CURLOPT_URL, $sUrl);
            curl_setopt($rCh, CURLOPT_HEADER, 0);
            curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
            $mRes = curl_exec($rCh);

            if ($mRes === false) {
                throw new CurlException(curl_error($rCh), curl_errno($rCh));
            }

            unset($rCh);

            return $mRes;
        }

        if (filter_var((string)ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            return @file_get_contents($sUrl);
        }

        return false;
    }

    /**
     * Extract Zip archive.
     *
     * @param string $sFile zip file
     * @param string $sDir  destination to extract the file
     */
    public function zipExtract(string $sFile, string $sDir): bool
    {
        $oZip = new \ZipArchive();
        $mRes = $oZip->open($sFile);

        if ($mRes !== true) {
            return false;
        }

        try {
            if ($oZip->numFiles > self::MAX_ZIP_ENTRIES) {
                return false;
            }

            $this->createDir($sDir);

            $sDestination = realpath($sDir);
            if (!is_string($sDestination)) {
                return false;
            }

            $aEntryNames = [];
            $iTotalUncompressedBytes = 0;

            for ($iIndex = 0; $iIndex < $oZip->numFiles; ++$iIndex) {
                $aEntry = $oZip->statIndex($iIndex);
                if (!is_array($aEntry) || !$this->isSafeZipEntry($oZip, $iIndex, $aEntry, $sDestination)) {
                    return false;
                }

                $sEntryName = strtolower(str_replace('\\', PH7_SH, rtrim($aEntry['name'], '/\\')));
                if (isset($aEntryNames[$sEntryName])) {
                    return false;
                }
                $aEntryNames[$sEntryName] = true;

                $iEntrySize = (int)($aEntry['size'] ?? 0);
                $iCompressedSize = (int)($aEntry['comp_size'] ?? 0);
                $iTotalUncompressedBytes += $iEntrySize;

                if ($iTotalUncompressedBytes > self::MAX_ZIP_UNCOMPRESSED_BYTES) {
                    return false;
                }

                if ($iEntrySize > 0 && ($iCompressedSize <= 0 || $iEntrySize / $iCompressedSize > self::MAX_ZIP_EXPANSION_RATIO)) {
                    return false;
                }
            }

            return $oZip->extractTo($sDestination);
        } catch (PermissionException) {
            return false;
        } finally {
            $oZip->close();
        }
    }

    /**
     * Check if the file is binary.
     */
    public function isBinary(string $sFile): bool
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

            if (function_exists('is_binary')) {
                return is_binary($sContents);
            }

            return preg_match('/[^\x09\x0A\x0D\x20-\x7E]/', $sContents) === 1
                || strpos($sContents, "\x00") !== false;
        }

        return false;
    }

    private function isSafeZipEntry(\ZipArchive $oZip, int $iIndex, array $aEntry, string $sDestination): bool
    {
        $sEntryName = $aEntry['name'] ?? null;
        if (!is_string($sEntryName) || $sEntryName === '' || str_contains($sEntryName, "\0")) {
            return false;
        }

        $sNormalizedName = str_replace('\\', PH7_SH, $sEntryName);
        if (str_starts_with($sNormalizedName, PH7_SH) || preg_match('/^[a-z]:\//i', $sNormalizedName)) {
            return false;
        }

        $aSegments = explode(PH7_SH, $sNormalizedName);
        if (in_array('..', $aSegments, true)) {
            return false;
        }

        $aSegments = array_values(array_filter($aSegments, static fn (string $sSegment): bool => $sSegment !== '' && $sSegment !== '.'));
        if ($aSegments === []) {
            return false;
        }

        $sTargetPath = $sDestination . PH7_DS . implode(PH7_DS, $aSegments);
        if (!self::isPathInsideDirectory($sTargetPath, $sDestination)
            || $this->hasSymlinkInPath($sDestination, $aSegments)
        ) {
            return false;
        }

        $iOperatingSystem = 0;
        $iAttributes = 0;
        if ($oZip->getExternalAttributesIndex($iIndex, $iOperatingSystem, $iAttributes)
            && $iOperatingSystem === \ZipArchive::OPSYS_UNIX
            && (($iAttributes >> 16) & 0170000) === 0120000
        ) {
            return false;
        }

        return true;
    }

    private function hasSymlinkInPath(string $sDestination, array $aSegments): bool
    {
        $sCurrentPath = $sDestination;
        foreach ($aSegments as $sSegment) {
            $sCurrentPath .= PH7_DS . $sSegment;
            if (is_link($sCurrentPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a recursive directory iterator for a given directory.
     */
    private function getDirIterator(string $sPath): \RecursiveDirectoryIterator
    {
        return new \RecursiveDirectoryIterator($sPath);
    }

    /**
     * Recursive Directory Iterator.
     *
     * @param string $sFrom     directory
     * @param string $sTo       directory
     * @param string $sFuncName The function name. Choose between 'copy' and 'rename'.
     *
     * @throws PH7InvalidArgumentException if the function name is invalid
     * @throws PermissionException         if the directory cannot be created
     */
    private function recursiveDirIterator(string $sFrom, string $sTo, string $sFuncName): bool
    {
        if (!in_array($sFuncName, self::DIR_HANDLE_FUNC_NAMES, true)) {
            throw new PH7InvalidArgumentException('Wrong function name: ' . $sFuncName);
        }

        if (!is_dir($sFrom)) {
            return false;
        }

        $bRet = false; // Default value
        $oIterator = new \RecursiveIteratorIterator(
            $this->getDirIterator($sFrom), \RecursiveIteratorIterator::SELF_FIRST
        );

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
